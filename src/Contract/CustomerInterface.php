<?php

declare(strict_types=1);

namespace Flutterwave\Contract;

use Flutterwave\Customer;

interface CustomerInterface
{
    public function create(array $data): Customer;
}
