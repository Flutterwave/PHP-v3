<?php

declare(strict_types=1);

namespace Flutterwave;

/**
 * Class Customer
 *
 * @package    Flutterwave
 * @deprecated Use Flutterwave\Entities\Customer instead.
 */
class Customer
{
    private Entities\Customer $instance;
    public function __construct(array $data = [])
    {
        $this->instance = new \Flutterwave\Entities\Customer($data);
    }

    public function get(string $param)
    {
        return $this->instance->get($param);
    }

    public function set(string $param, $value): void
    {
        $this->instance->set($param, $value);
    }

    public function has(string $param): bool
    {
        return $this->instance->has($param);
    }

    public function toArray(): array
    {
        return $this->instance->toArray();
    }
}
