<?php

namespace Flutterwave\Contract;

interface FactoryInterface
{
    public function create(array $data): Entityinterface;
}
