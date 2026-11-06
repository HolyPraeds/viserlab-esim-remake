<?php

namespace App\Lib;

class CurlRequest
{

    /**
     * GET request using curl
     *
     * @return mixed
     */
    public static function curlContent($url, $header = null)
    {
        if (!self::schemeAllowed($url)) {
            return false;
        }

        $ch = curl_init();
        self::lockProtocols($ch);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * POST request using curl
     *
     * @return mixed
     */
    public static function curlPostContent($url, $postData = null, $header = null)
    {
        if (is_array($postData)) {
            $params = http_build_query($postData);
        } else {
            $params = $postData;
        }
        if (!self::schemeAllowed($url)) {
            return false;
        }

        $ch = curl_init();
        self::lockProtocols($ch);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private static function schemeAllowed($url): bool
    {
        $scheme = strtolower((string) parse_url((string) $url, PHP_URL_SCHEME));

        return $scheme === 'http' || $scheme === 'https';
    }

    private static function lockProtocols($ch): void
    {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        if (defined('CURLOPT_PROTOCOLS_STR')) {
            curl_setopt($ch, CURLOPT_PROTOCOLS_STR, 'http,https');
        } elseif (defined('CURLOPT_PROTOCOLS') && defined('CURLPROTO_HTTP') && defined('CURLPROTO_HTTPS')) {
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS_STR')) {
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS_STR, 'http,https');
        } elseif (defined('CURLOPT_REDIR_PROTOCOLS') && defined('CURLPROTO_HTTP') && defined('CURLPROTO_HTTPS')) {
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        }
    }
}
