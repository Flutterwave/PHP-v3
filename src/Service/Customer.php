<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\CustomerInterface;
use Flutterwave\Entities\Customer as Person;
use Flutterwave\Factories\CustomerFactory;
use InvalidArgumentException;

/**
 * Class Customer.
 *
 * @deprecated use \Flutterwave\Factories\CustomerFactory instead
 */
class Customer
{
    protected CustomerInterface $customerFactory;

    public function __construct()
    {
        $this->customerFactory = new CustomerFactory();
    }

    public function create(array $data = []): Person
    {
        return $this->customerFactory->create($data);
    }
}
