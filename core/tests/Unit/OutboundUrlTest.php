<?php

namespace Tests\Unit;

use App\Lib\OutboundUrl;
use PHPUnit\Framework\TestCase;

class OutboundUrlTest extends TestCase
{
    public function test_cron_url_allows_only_https_on_an_owned_public_host(): void
    {
        $resolver = static fn () => ['1.1.1.1'];

        OutboundUrl::validate('https://travelsim.live/cron', [
            'allowed_hosts' => ['travelsim.live', 'payersim.com'],
            'ports' => [443],
            'resolver' => $resolver,
        ]);

        $this->assertTrue(true);
    }

    public function test_cron_url_rejects_internal_metadata_and_callback_targets(): void
    {
        $owned = [
            'allowed_hosts' => ['travelsim.live'],
            'ports' => [443],
            'resolver' => static fn () => ['1.1.1.1'],
        ];

        $blocked = [
            'gopher://127.0.0.1:6379/_PING',
            'dict://127.0.0.1:6379/INFO',
            'ftp://127.0.0.1/',
            'http://127.0.0.1/cron?alias=tsim_oob1',
            'http://169.254.169.254/latest/meta-data/',
            'https://127.0.0.1/',
            'https://169.254.169.254/latest/meta-data/',
            'https://100.100.100.200/latest/meta-data/',
            'https://[::1]/',
            'https://2130706433/',
            'https://0177.0.0.1/',
            'https://127.1/',
            'https://0x7f000001/',
            'https://metadata.google.internal/computeMetadata/v1/',
            'https://45.139.84.37:8080/oob1',
            'https://evil.example/oob',
            'https://user:pass@travelsim.live/',
            'https://travelsim.live:6379/',
            'https://travelsim.live.evil.com/',
        ];

        foreach ($blocked as $url) {
            try {
                OutboundUrl::validate($url, $owned);
                $this->fail('Allowed blocked URL: '.$url);
            } catch (\InvalidArgumentException $e) {
                $this->assertSame('URL is not allowed', $e->getMessage());
            }
        }
    }

    public function test_owned_host_is_rejected_when_it_resolves_to_a_private_address(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        OutboundUrl::validate('https://travelsim.live/cron', [
            'allowed_hosts' => ['travelsim.live'],
            'ports' => [443],
            'resolver' => static fn () => ['127.0.0.1'],
        ]);
    }

    public function test_rejects_whitespace_and_backslashes(): void
    {
        $options = [
            'allowed_hosts' => ['travelsim.live'],
            'resolver' => static fn () => ['1.1.1.1'],
        ];

        foreach (["https://travelsim.live/a\nb", "https://travelsim.live/a\tb", "https://travelsim.live/a\\b"] as $url) {
            try {
                OutboundUrl::validate($url, $options);
                $this->fail('Allowed unsafe URL');
            } catch (\InvalidArgumentException $e) {
                $this->assertSame('URL is not allowed', $e->getMessage());
            }
        }
    }

    public function test_public_https_host_is_rejected_when_dns_points_inside(): void
    {
        OutboundUrl::validate('https://sms.example.com/send', [
            'resolver' => static fn () => ['1.1.1.1'],
        ]);

        $this->expectException(\InvalidArgumentException::class);

        OutboundUrl::validate('https://sms.example.com/send', [
            'resolver' => static fn () => ['10.0.0.5'],
        ]);
    }

    public function test_public_ip_ranges_used_in_the_incident_are_blocked(): void
    {
        $this->assertTrue(OutboundUrl::isBlockedIp('127.0.0.1'));
        $this->assertTrue(OutboundUrl::isBlockedIp('10.1.2.3'));
        $this->assertTrue(OutboundUrl::isBlockedIp('172.16.0.1'));
        $this->assertTrue(OutboundUrl::isBlockedIp('192.168.0.1'));
        $this->assertTrue(OutboundUrl::isBlockedIp('169.254.169.254'));
        $this->assertTrue(OutboundUrl::isBlockedIp('169.254.42.42'));
        $this->assertTrue(OutboundUrl::isBlockedIp('100.100.100.200'));
        $this->assertTrue(OutboundUrl::isBlockedIp('::1'));
        $this->assertFalse(OutboundUrl::isBlockedIp('1.1.1.1'));
        $this->assertFalse(OutboundUrl::isBlockedIp('8.8.8.8'));
    }
}
