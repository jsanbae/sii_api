<?php

declare(strict_types=1);

namespace Jsanbae\SIIAPI\Http;

final class SiiHttpConfig
{
    private static ?self $instance = null;

    public function __construct(
        public readonly bool $proxyEnabled = false,
        public readonly ?string $proxyUrl = null,
    ) {}

    public static function configure(self $config): void
    {
        self::$instance = $config;
    }

    public static function instance(): self
    {
        return self::$instance ?? new self();
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public function resolveProxyUrl(): ?string
    {
        if (!$this->proxyEnabled) {
            return null;
        }

        if ($this->proxyUrl === null || $this->proxyUrl === '') {
            return null;
        }

        return $this->proxyUrl;
    }
}
