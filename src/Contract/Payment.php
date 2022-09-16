<?php

namespace Flutterwave\Contract;

interface Payment
{
    public function initiate(\Flutterwave\Payload $payload);

    public function charge(\Flutterwave\Payload $payload);

    public function save(callable $callback);

    public function verify(?string $transactionId);

}