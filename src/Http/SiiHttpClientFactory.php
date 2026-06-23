<?php

declare(strict_types=1);

namespace Jsanbae\SIIAPI\Http;

use GuzzleHttp\Client;

final class SiiHttpClientFactory
{
    public static function make(array $options = []): Client
    {
        $config = SiiHttpConfig::instance();

        $defaults = [
            'verify' => false,
            'timeout' => 120,
            'connect_timeout' => 30,
        ];

        $proxy = $config->resolveProxyUrl();

        if ($proxy !== null) {
            $defaults['proxy'] = $proxy;
        }

        return new Client(array_merge($defaults, $options));
    }
}
