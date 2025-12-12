<?php

namespace Asaas\Support;

class Config
{
    private array $config;

    public function __construct(array $override = [])
    {
        $this->config = $this->mergeConfig($override);
    }

    /**
     * Get a configuration value using dot notation.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value    = $this->config;

        foreach ($segments as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Retrieve the base URL for the configured environment.
     */
    public function getBaseUrl(): string
    {
        $env = $this->get('environment', 'production');

        return $this->get("base_urls.$env");
    }

    /**
     * Load default configuration and merge overrides.
     *
     * @param array<string, mixed> $override
     * @return array<string, mixed>
     */
    private function mergeConfig(array $override): array
    {
        $default = require __DIR__ . '/../config/asaas.php';

        return array_replace_recursive($default, $override);
    }
}
