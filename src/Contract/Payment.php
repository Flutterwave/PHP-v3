<?php

declare(strict_types=1);

namespace Flutterwave\Contract;

interface Payment
{
    public function initiate(\Flutterwave\Payload $payload): array;

    public function charge(\Flutterwave\Payload $payload): array;

    public function save(callable $callback): void;

    public function verify(?string $transactionId): \stdClass;
}
