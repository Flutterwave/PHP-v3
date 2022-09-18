<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;

class Remita extends Service
{
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

    public function getAgencies()
    {
        
    }

    public function getProductUnderAgency()
    {

    }

    public function getProductAmount()
    {

    }

    public function createOrder()
    {

    }

    public function updateOrder()
    {

    }
}