<?php

namespace Flutterwave\Entities;

use Flutterwave\Contract\EntityInterface;

class Customer implements EntityInterface
{
    private array $data = [];

    public function __construct(array $data = [])
    {
        //TODO: validate data contains the required fields.
        $this->data = [...$data];
    }

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
