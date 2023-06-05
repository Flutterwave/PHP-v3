<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Factories\PayloadFactory as Factory;

/**
 * Class Payload.
 *
 * @deprecated use \Flutterwave\Factories\PayloadFactory instead
 */
class Payload
{
    private Factory $payloadFactory;
    public function __construct()
    {
        $this->payloadFactory = new Factory();
    }

    public function create(array $data): \Flutterwave\Entities\Payload
    {
        return $this->payloadFactory->create($data);
    }

    public function validSuppliedData(array $data): array
    {
        return $this->payloadFactory->validSuppliedData($data);
    }
}
