<?php

namespace Flutterwave\Service;

use Flutterwave\Contract\CustomerInterface;
use Flutterwave\Customer as Person;

class Customer implements CustomerInterface
{
    public function create(array $data = []): Person
    {
        $data = array_change_key_case($data);
        if(empty($data))
        {
            throw new \InvalidArgumentException("Customer data is empty");
        }

        $person = new Person;
        $person->set("fullname", $data['full_name']);
        $person->set("email", $data['email']);
        $person->set("phone_number", $data["phone"]);
        $person->set("address", $data["address"] ?? null);

        return $person;
    }

    public function retrieve(string $email): Person
    {
        //TODO: Customer API integration
        return new Person("john doe", "olaobajua@gmail.com", "+2349067985861", null);
    }
}