<?php

declare(strict_types=1);

return [
    '057' => [
        'requiredParams' => [
            'passcode' => 'DDMMYYYY',
        ],
    ],
    '033' => [
        'requiredParams' => [
            'bvn' => '/[0-9]{11}/g',
        ],
    ],
];

//return [
//    "zenith" => [
//        "code" =>  057,
//        "requiredParams" => [
//            "DOB" => "dd-mm-YYYY"
//        ]
//    ],
//    "uba" => [
//        "code" => 033,
//        "requiredParams" => [
//            "bvn" => "/[0-9]{11}/g"
//        ]
//    ]
//];
