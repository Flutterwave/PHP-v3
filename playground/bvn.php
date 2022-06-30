<?php 
$page = 'result';
include('partials/header.php');//this is just to load the bootstrap and css. 

require("../library/bvn.php");

use Flutterwave\EventHandlers\EventHandlers\EventHandlers\EventHandlers\EventHandlers\Bvn;

//The data variable holds the payload
$bvn_number = "123456789";
$bvn = new Bvn();
$result = $bvn->verifyBVN($bvn_number);

echo '<div class="alert alert-success role="alert">
        <h1>BVN verification Result: </h1>
        <p><b> '.print_r($result, true).'</b></p>
      </div>';

include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>


