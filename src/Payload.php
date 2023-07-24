<?php

declare(strict_types=1);

namespace Flutterwave;

/**
 * Class Payload
 *
 * @package    Flutterwave
 * @deprecated Use Flutterwave\Entities\Payload instead.
 */
class Payload
{
    private Entities\Payload $instance;

    public function __construct()
    {
        $this->instance = new \Flutterwave\Entities\Payload();
    }

    public function get(string $param)
    {
        if (! $this->instance->has($param)) {
            return null;
        }
        return $this->instance->get($param);
    }

    public function set(string $param, $value): void
    {
        $this->instance->set($param, $value);
    }

    public function delete(string $param, array $assoc_option = []): void
    {
        $this->instance->delete($param, $assoc_option);
    }

    public function setPayloadType(string $type): Entities\Payload
    {
        $this->instance->setPayloadType($type);
        return $this->instance;
    }

    public function toArray(?string $payment_method = null): array
    {
        return $this->instance->toArray($payment_method);
    }

    public function update($param, $value): void
    {
        $this->instance->update($param, $value);
    }

    public function empty(): void
    {
        $this->instance->empty();
    }

    public function has(string $param): bool
    {
        return $this->instance->has($param);
    }

    public function size(): int
    {
        return $this->instance->size();
    }

    public function generateTxRef(): void
    {
        $this->instance->generateTxRef();
    }
}
