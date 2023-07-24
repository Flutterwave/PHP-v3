<?php

namespace Flutterwave\Contract;

interface EntityInterface
{
    public function get(string $param);
    public function set(string $param, $value): void;
    public function has(string $param): bool;
}
