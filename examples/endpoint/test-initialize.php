<?php
require __DIR__."/../../vendor/autoload.php";

session_start();

\Flutterwave\Flutterwave::bootstrap();

try {
    $flw = new \Flutterwave\Flutterwave();
    $flw->setAmount('1000')
        ->setCurrency(\Flutterwave\Util\Currency::NGN)
        ->setCountry('NG')
        ->setEmail('test@example.com')
        ->setFirstname('John')
        ->setLastname('Doe')
        ->setPhoneNumber('+2349067985861')
        ->setRedirectUrl("http://{$_SERVER['HTTP_HOST']}/examples/endpoint/verify.php")
        ->setTitle('Test Payment') # ->setTitle('</script><script>alert(1)</script>') change title to this for testing XSS fix
        ->setDescription('Testing initialize XSS fix')
        ->setLogo('https://mysite.com/logo.png')
        ->setPaymentOptions('card,banktransfer');

    if (!empty($_REQUEST) && isset($_REQUEST['make'])) {
        $flw->initialize(); // this renders the full modal page
        exit;
    }

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<link rel="stylesheet" href="../assets/css/index.css">
<div class="buttons">
    <form method="get">
        <h3>Initialize() XSS Fix - Smoke Test</h3>
        <span class="error"><?= $error ?? "" ?></span>
        <div class="cta">
            <button class="make-payment" name="make" value="1">Test Initialize</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
<script src="../assets/js/index.js"></script>