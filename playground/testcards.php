<?php

$cards =  [

    'card1' => array(
            "card_number"=> "5531886652142950",
            "cvv"=> "564",
            "expiry_month"=> "09",
            "expiry_year"=> "22",
            "currency"=> "NGN",
            "amount" => "1000",
            "fullname"=> "Ekene Eze",
            "email"=> "ekene@flw.com",
            "phone_number"=> "0902620185",
            "fullname" => "temi desola",
            //"tx_ref"=> "MC-3243e",// should be unique for every transaction
            "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
            "authorization"=> [
                "mode"=> "pin",
                "pin"=> "3310",
            ]
    ),
    'card2' => array(
                "card_number"=> "4556052704172643",
                "cvv" => "899",
                "expiry_month"=> "01",
                "expiry_year"=> "21",
                "currency"=> "NGN",
                "amount"=> "1000",
                "fullname"=> "Ekene Eze",
                "email"=> "ekene@flw.com",
                "phone_number"=> "0902620185",
                "fullname"=> "temi desola",
                //"tx_ref"=> "MC-3243e",// should be unique for every transaction
                "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
                "authorization"=> [
                    "mode" => "avs_noauth",
                    "city"=> "Sampleville",
                    "address"=> "3310 sample street ",
                    "state"=> "Simplicity",
                    "country"=> "Simple",
                    "zipcode"=> "000000",
                ]
                ),
                'card3' => array(
                    "card_number"=> "5531886652142950",
                    "cvv"=> "564",
                    "expiry_month"=> "09",
                    "expiry_year"=> "22",
                    "currency"=> "NGN",
                    "country" => "NG",
                    "amount" => "1000",
                    "tx_ref" => "BY-34552-RE",
                    "fullname"=> "Ekene Eze",
                    "email"=> "ekene@flw.com",
                    "phone_number"=> "0902620185",
                    "fullname" => "temi desola",
                    //"tx_ref"=> "MC-3243e",// should be unique for every transaction
                    "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
                    "authorization"=> [
                        "mode"=> "pin",
                        "pin"=> "3310",
                    ],
                    "preauthorize" => true,
                    "usesecureauth" => true,
            ),
];

return $cards;

$card1 = array(
    "card_number"=> "5531886652142950",
    "cvv"=> "564",
    "expiry_month"=> "09",
    "expiry_year"=> "22",
    "currency" => "NGN",
    "amount" => "1000",
    "fullname"=> "Ekene Eze",
    "email" => "ekene@flw.com",
    "phone_number"=> "0902620185",
    "fullname"=> "temi desola",
    //"tx_ref"=> "MC-3243e",// should be unique for every transaction
    "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
    // "authorization"=> [
    //     "mode"=> "pin",
    //     "pin"=> "3310",
    // ]
);

$card2 = array(
        "card_number"=> "4556052704172643",
        "cvv" => "899",
        "expiry_month"=> "01",
        "expiry_year"=> "21",
        "currency"=> "NGN",
        "amount"=> "1000",
        "fullname"=> "Ekene Eze",
        "email"=> "ekene@flw.com",
        "phone_number"=> "0902620185",
        "fullname"=> "temi desola",
        //"tx_ref"=> "MC-3243e",// should be unique for every transaction
        "redirect_url"=> "https://webhook.site/3ed41e38-2c79-4c79-b455-97398730866c",
        "authorization"=> [
            "mode" => "avs_noauth",
            "city"=> "Sampleville",
            "address"=> "3310 sample street ",
            "state"=> "Simplicity",
            "country"=> "Simple",
            "zipcode"=> "000000",
        ]

);


