<?php

$page = 'main';
include('partials/header.php');//this is just to load the bootstrap and css. 
?>

<div class="container" style="margin-top:2em">
<div id="accordion">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h5 class="mb-0">
        <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Charge Sample - Feature #1
        </button>
      </h5>
    </div>

    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
      <div class="card-body">
      <ol>
          <li><a href="card.php">Charge a card using Flutterwave's APIs</a></li>
          <li>Charge  bank accounts using Flutterwave- <a href="AccountPayment.php">Nigerian Account</a> or <a href="">UK account</a></li>
          <li><a href="Transfer.php">How to transfer or make payouts<a/></li>
          <li><a href="Ussd.php">how to collect payments via ussd</a></li>
          <li><a href="Mobilemoney.php">how to collect payments via mobile money.</a></li>
          <li><a href="VoucherPayment.php">how to collect ZAR payments offline using Vouchers</li></a>
          <li><a href="Mpesa.php">how to collect payments from your customers via Mpesa<a/></li>
          <li><a href="TokenCharge.php">Charge with token [Tokenized charge]</a></li>
      </ol>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Transactions - Feature #2
        </button>
      </h5>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
      <div class="card-body">

        <ol>
            <li><a href="">Fetch all transactions on your account</a></li>
            <li><a href="">Get transaction fees</a></li>
            <li><a href="">Verify transactions using the transaction reference tx_ref</a></li>
            <li><a href="">Resend a failed transaction webhook to your server</a></li>
            <li><a href="">View Transaction Timeline</a></li>
        </ol>


      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Transfers -  Feature #3
        </button>
      </h5>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
      <div class="card-body">
          <ol>
              <li><a href="">how to initiate a transfer</a></li>
              <li><a href="">how to initiate a bulk transfer</a></li>
              <li><a href="">Get applicable transfer fee</a></li>
              <li><a href="">Fetch all transfers on your account</a></li>
          </ol>
      </div>
    </div>
  </div>
</div>
</div>
    
<?php include('partials/footer.php');//this is just to load the jquery and js scripts. 

?>

