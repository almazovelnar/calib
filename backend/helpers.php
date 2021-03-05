<?php

if (!function_exists('ip')) {
    function ip() : string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];

            $ip = explode(',', $ips);

            if (!empty($ip[0])) {
                $ip = $ip[0] ?? $_SERVER["HTTP_CF_CONNECTING_IP"];
            } else {
                $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
            }
        } else {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        return $ip ?? '0.0.0.0';
    }
}
