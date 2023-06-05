<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PHP SDK Implementation </title>
    <style>
        #btn-of-destiny {
            margin-top: 2em;
        }

        #explain1 {
            padding: 20px;
            margin: 2em auto auto;
        }

    </style>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body>


<form method="POST" action="processPayment.php" id="paymentForm">
    <input type="hidden" name="amount" value="200"/> 
    <!-- Replace the value with your transaction amount -->
    <input type="hidden" name="description" value="I Phone X, 100GB, 32GB RAM"/>
    <!-- Replace the value with your transaction description -->
    <input type="hidden" name="currency" value="NGN"/> 
    <!-- Replace the value with your transaction currency -->
    <input type="hidden" name="payment_method" value="card"/> 
    <input type="hidden" name="email" value="busa@yahoo.com"/> 
    <!-- Replace the value with your customer email -->
    <input type="hidden" name="first_name" value="Olaobaju"/>
    <!-- Replace the value with your customer firstname (optional) -->
    <input type="hidden" name="last_name" value="Abraham"/>
    <!-- Replace the value with your customer lastname (optional) -->
    <input type="hidden" name="phone_number" value="08098787676"/>
    <!-- Replace the value with your customer phonenumber (optional if email is passes) -->
    <input type="hidden" name="pay_button_text" value="Complete Payment"/>
    <!-- Replace the value with the payment button text you prefer (optional) -->
<!--    <input type="hidden" name="tx_ref" value="TEST_TXREF_--><?//= uniqid() ?><!--"/>-->
    <!-- Replace the value with your transaction reference. It must be unique per transaction. You can delete this line if you want one to be generated for you. -->
    <input type="hidden" name="success_url" value="http://request.lendlot.com/13b9gxc1?status=success">
    <!-- Put your success url here -->
    <input type="hidden" name="failure_url" value="http://request.lendlot.com/13b9gxc1?status=failed">
    <!-- Put your failure url here -->
    <center><input id="btn-of-destiny" class="btn btn-warning" type="submit" value="Pay Now"/></center>
</form>


<!--you can delete this if you no longer need the guide--->
<div id="explain1" class="container-lg bg-dark " style="color:white; text-align:center">
    <p>Your Form Should Contain Hidden Values</p>

    <p>To view the hidden values <code>Inspect the Page </code>or Hold <code>Ctrl</code> + <code>Shift</code> + <code>
            I</code></p>
</div>


<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
</body>
</html>
