<?php

declare(strict_types=1);

namespace Flutterwave\Contract;

use Flutterwave\Helper\Config;

interface ServiceInterface
{
    public function __construct(Config $config);

    public function getName(): string;
}
