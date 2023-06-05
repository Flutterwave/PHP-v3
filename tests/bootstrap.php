<?php
/**
 *
 * ################   #######        ############               ############
 * ################   #######          ######                      ######
 * #######            #######           #####          #####      #######
 * #######            #######           #####         #######      #######
 * ################   #######           ######     ####  #####     #######
 * ################   #######           ######     #####   #####   #######
 * #######            #######           ######    ####     #############
 * #######            #######           ##############      ############
 * #######            #######            #############      ##########
 * #######            #######             ##########         ########
 * #######            ###############      ########         ########
 * #######            ###############       ######           ######
 *
 * Flutterwave Client Library for PHP
 *
 * Copyright (c) 2020 Flutterwave inc.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

# include vendor directory
require_once __DIR__ . '/../vendor/autoload.php';

# by pass final definitions.
DG\BypassFinals::enable();
DG\BypassFinals::setWhitelist([
    '*/src/Library/*',
    '*/src/Entities/*',
    '*/src/Factories/*',
    '*/src/HttpAdapter/*',
    '*/src/Controller/*',
]);

# flutterwave setup.
require_once __DIR__ . '/../setup.php';

