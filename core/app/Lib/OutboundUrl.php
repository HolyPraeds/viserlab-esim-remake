<?php

namespace App\Lib;

class OutboundUrl
{
    public static function assertCronUrl(string $url): void
    {
        self::validate($url, [
            'allowed_hosts' => self::siteHosts(),
            'ports' => [443],
        ]);
    }

    public static function assertPublicHttps(string $url): void
    {
        self::validate($url);
    }

    public static function siteHosts(): array
    {
        $hosts = [];

        foreach (array_keys(config('brands.hosts', [])) as $host) {
            $hosts[] = strtolower((string) $host);
        }

        foreach (config('brands.catalog', []) as $brand) {
            $host = parse_url((string) ($brand['url'] ?? ''), PHP_URL_HOST);
            if (is_string($host) && $host !== '') {
                $hosts[] = strtolower($host);
            }
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (is_string($appHost) && $appHost !== '') {
            $hosts[] = strtolower($appHost);
        }

        return array_values(array_unique($hosts));
    }

    /**
     * @param  array{allowed_hosts?: array<int, string>|null, ports?: array<int, int>|null, allow_ip_host?: bool, resolver?: callable|null}  $options
     */
    public static function validate(string $url, array $options = []): void
    {
        $allowedHosts = $options['allowed_hosts'] ?? null;
        $ports = $options['ports'] ?? null;
        $allowIpHost = (bool) ($options['allow_ip_host'] ?? false);
        $resolver = $options['resolver'] ?? null;

        $url = trim($url);
        if ($url === '' || strlen($url) > 2048 || strpbrk($url, " \t\r\n\0\\") !== false) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        $parts = parse_url($url);
        if (!is_array($parts)) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if ($scheme !== 'https') {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        $host = strtolower(rtrim((string) ($parts['host'] ?? ''), '.'));
        if ($host === '' || strlen($host) > 253) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        if (isset($parts['port'])) {
            $port = (int) $parts['port'];
            if ($port < 1 || $port > 65535 || ($ports !== null && !in_array($port, $ports, true))) {
                throw new \InvalidArgumentException('URL is not allowed');
            }
        }

        if (self::isBlockedHostname($host) || self::isObfuscatedAddress($host)) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        $isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;
        if ($isIp && (!$allowIpHost || self::isBlockedIp($host))) {
            throw new \InvalidArgumentException('URL is not allowed');
        }

        if (!$isIp) {
            if (!preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $host)) {
                throw new \InvalidArgumentException('URL is not allowed');
            }

            if (is_array($allowedHosts)) {
                $allowed = array_map(static fn ($item) => strtolower(rtrim((string) $item, '.')), $allowedHosts);
                if (!in_array($host, $allowed, true)) {
                    throw new \InvalidArgumentException('URL is not allowed');
                }
            }

            $ips = $resolver ? $resolver($host) : self::resolveHost($host);
            if (!is_array($ips) || $ips === []) {
                throw new \InvalidArgumentException('URL is not allowed');
            }

            foreach ($ips as $ip) {
                if (!is_string($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false || self::isBlockedIp($ip)) {
                    throw new \InvalidArgumentException('URL is not allowed');
                }
            }
        }
    }

    private static function isBlockedHostname(string $host): bool
    {
        $exact = [
            'localhost',
            'localhost.localdomain',
            'metadata.google.internal',
            'metadata.goog',
            'fldubai.com',
            'maxxspace.com',
            'guerrillamailblock.com',
            'mailinator.com',
            'nip.io',
        ];

        if (in_array($host, $exact, true)) {
            return true;
        }

        foreach (['.localhost', '.local', '.internal', '.localdomain', '.intranet', '.fldubai.com', '.maxxspace.com', '.guerrillamailblock.com', '.mailinator.com', '.nip.io'] as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }

    private static function isObfuscatedAddress(string $host): bool
    {
        if (preg_match('/^(?:0x[0-9a-f]+|\d+)$/i', $host)) {
            return true;
        }

        if (!str_contains($host, '.') || !preg_match('/^[0-9.]+$/', $host)) {
            return false;
        }

        $parts = explode('.', $host);
        if (count($parts) !== 4) {
            return true;
        }

        foreach ($parts as $part) {
            if ($part === '' || (strlen($part) > 1 && str_starts_with($part, '0')) || (int) $part > 255) {
                return true;
            }
        }

        return false;
    }

    public static function isBlockedIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $cidrs = [
                '0.0.0.0/8',
                '10.0.0.0/8',
                '127.0.0.0/8',
                '169.254.0.0/16',
                '172.16.0.0/12',
                '192.0.0.0/24',
                '192.0.2.0/24',
                '192.168.0.0/16',
                '100.64.0.0/10',
                '198.18.0.0/15',
                '198.51.100.0/24',
                '203.0.113.0/24',
                '224.0.0.0/4',
                '240.0.0.0/4',
            ];

            foreach ($cidrs as $cidr) {
                if (self::ipv4InCidr($ip, $cidr)) {
                    return true;
                }
            }

            return $ip === '255.255.255.255';
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        }

        $bin = inet_pton($ip);
        if ($bin === false || strlen($bin) !== 16) {
            return true;
        }

        if ($bin === str_repeat("\0", 16) || $ip === '::1') {
            return true;
        }

        if (str_starts_with($bin, str_repeat("\0", 10)."\xff\xff")) {
            $mapped = inet_ntop(substr($bin, 12));

            return $mapped === false || self::isBlockedIp($mapped);
        }

        $first = ord($bin[0]);
        $second = ord($bin[1]);

        if (($first & 0xfe) === 0xfc) {
            return true;
        }

        if ($first === 0xfe && ($second & 0xc0) === 0x80) {
            return true;
        }

        if ($first === 0xff) {
            return true;
        }

        $documentation = inet_pton('2001:db8::');

        return $documentation !== false && substr($bin, 0, 4) === substr($documentation, 0, 4);
    }

    private static function ipv4InCidr(string $ip, string $cidr): bool
    {
        [$net, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;
        $ipBin = inet_pton($ip);
        $netBin = inet_pton($net);
        if ($ipBin === false || $netBin === false) {
            return false;
        }

        $ipLong = unpack('N', $ipBin)[1];
        $netLong = unpack('N', $netBin)[1];
        $mask = $bits === 0 ? 0 : ((0xFFFFFFFF << (32 - $bits)) & 0xFFFFFFFF);

        return ($ipLong & $mask) === ($netLong & $mask);
    }

    private static function resolveHost(string $host): array
    {
        $ips = [];
        $records = @dns_get_record($host, DNS_A + DNS_AAAA);
        if (is_array($records)) {
            foreach ($records as $record) {
                if (!empty($record['ip'])) {
                    $ips[] = $record['ip'];
                }
                if (!empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        if ($ips === []) {
            $v4 = @gethostbynamel($host);
            if (is_array($v4)) {
                $ips = $v4;
            }
        }

        return array_values(array_unique($ips));
    }
}
