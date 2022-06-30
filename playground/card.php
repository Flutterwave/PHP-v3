
<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

DEFINE('DS', DIRECTORY_SEPARATOR);

require("../library/CardPayment.php");
require("testcards.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\CardPayment;

//check the file testcards.php for the sample request...
//The data variable holds the payload
// $cards['card1'];
// $cards['card2'];

if (isset($_POST['charge'])){
    $card_option = $_POST['token'];

    $payment = new CardPayment();

    $result = $payment->cardCharge($cards[$card_option]);
    if(gettype($result) == 'string'){
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit;
    }

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

<div id="paymentOptions" class="container-fluid d-none" style="text-align:center;">
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

    <div class="shadow main-section mt-4 pt-4 pb-4 mx-4">
        <div class="cta-1 text-center mt-4 pt-4">
            <img class="" src="./uber/assets/images/car.svg" width="50px" height="50px" />
        </div>

        <div>
            <h4 class="text-center mt-4">Request a ride</h4>
        </div>

        <div class="text-center">
            <button id="showPaymentCards" class="btn btn-warning" >Proceed</button>
        </div>
    </div>

    <script>

        var spc = document.querySelector('#showPaymentCards');
        var main = document.querySelector('.main-section');
        var pm = document.querySelector('#paymentOptions');
        spc.addEventListener('click', function(){
            main.classList.add('d-none');
            pm.classList.remove('d-none');

        })
    </script>








