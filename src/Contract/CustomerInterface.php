<?php

declare(strict_types=1);

namespace Flutterwave\Contract;

use Flutterwave\Entities\Customer;

interface CustomerInterface
{
    public function create(array $data): Customer;
}
