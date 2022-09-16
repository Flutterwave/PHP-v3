<?php

namespace Flutterwave;

class Customer
{
    private array $data = [];

    public function get(string $param)
    {
        return $this->data[$param];
    }

    public function set(string $param, $value): void
    {
        $this->data[$param] = $value;
    }

    public function has(string $param): bool
    {
        return isset($this->data[$param]);
    }

    public function toArray(): array
    {
        return $this->data;
    }

}