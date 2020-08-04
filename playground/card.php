
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

DEFINE('DS', DIRECTORY_SEPARATOR);

require("../library/CardPayment.php");
require("testcards.php");
use Flutterwave\Card;

//check the file testcards.php for the sample request...
//The data variable holds the payload
// $cards['card1'];
// $cards['card2'];

$payment = new Card();


// $result = $payment->cardCharge($cards['card1']);
// print_r($result);

if (isset($_POST['charge'])){


    $card_option = $_POST['token'];

    $result = $payment->cardCharge($cards[$card_option]);
    $sera = serialize($payment);

    $filePath = getcwd().DS."payment.txt";
    if (is_writable($filePath)) {
        $fp = fopen($filePath, "w"); 
        fwrite($fp, $sera); 
        fclose($fp);
        
    }
    $url = 'otp2.php?ref='.$result['data']['flw_ref']."&id=".$result['data']['id'];
        header( 'Location:'.$url);


}

include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>

<div class="container" style="text-align:center">
<h1>Card payment Implementation </h1>
<ul class="list-group col-md-6"><li class="list-group-item">
                                <form method="POST" action="">
                                    MasterCard/Verve - Pin
                                    <input type="hidden" name="token" value="card1">
                                    <input type="submit" value="Charge" class="btn btn-sm btn-success float-right" name="charge">
                                </form>
                            </li><li class="list-group-item">
                                <form method="POST" action="">
                                    NO_auth International Card - No_auth
                                    <input type="hidden" name="token" value="card2">
                                    <input type="submit" value="Charge" class="btn btn-sm btn-success float-right" name="charge">
                                </form>
                            </li></ul> 

    </div>






