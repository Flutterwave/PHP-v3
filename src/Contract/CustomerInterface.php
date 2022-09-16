<?php

namespace Flutterwave\Contract;

use Flutterwave\Customer;

interface CustomerInterface
{
    public function create(array $data): Customer;

    public function retrieve(string $email): Customer;
}