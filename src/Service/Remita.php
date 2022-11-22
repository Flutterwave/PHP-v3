<?php

declare(strict_types=1);

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;

class Remita extends Service
{
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    public function getAgencies(): void
    {
    }

    public function getProductUnderAgency(): void
    {
    }

    public function getProductAmount(): void
    {
    }

    public function createOrder(): void
    {
    }

    public function updateOrder(): void
    {
    }
}
