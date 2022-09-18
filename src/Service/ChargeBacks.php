<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\ConfigInterface;

class ChargeBacks extends Service
{
    public function __construct(?ConfigInterface $config = null)
    {
        parent::__construct($config);
    }

}