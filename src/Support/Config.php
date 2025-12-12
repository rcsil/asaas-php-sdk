<?php

namespace Asaas\Support;

class Config
{
    protected array $config;

    public function __construct(array $override = [])
    {
        $default = require __DIR__ . '../config/asaas.php';

        $this->config = array_merge_recursive($default, $override);
    }

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

    public function getBaseUrl(): string
    {
        $env = $this->get('environment', 'production');

        return $this->get("base_urls.$env");
    }
}