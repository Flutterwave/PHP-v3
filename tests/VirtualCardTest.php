<?php
require("../lib/VirtualCards.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\VirtualCard;
use PHPUnit\Framework\TestCase;

class API {}

interface virtualCardAPI {
    function create($array): array;
    function fund($array): array;
    function list($array): array;
    function get($array): array;
    function terminate($array): array;
    function transactions($array): array;
    function withdraw($array): array;
}


class VirtualCardTest extends TestCase
{
    public function test_card_is_created_when_payload_is_posted()
    {
        $array = array(
            "secret_key"=>"FLWSECK-xxxxxxxxxxxxxxxxxxxxx-X",
            "currency"=> "NGN",
            "amount"=>"200",
            "billing_name"=> "Mohammed Lawal",
            "billing_address"=>"DREAM BOULEVARD",
            "billing_city"=> "ADYEN",
            "billing_state"=>"NEW LANGE",
            "billing_postal_code"=> "293094",
            "billing_country"=> "US"
        );
        $double = Mockery::mock(virtualCardAPI::class);
        $virtualCard = new VirtualCard();
        $result = $virtualCard->create($array);
        $this->assertInstanceOf($double->shouldReceive('create')->with($array)->andReturn(array()), []);

    }

}

?>
