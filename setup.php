<?php  

use Flutterwave\Helper;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/../../");
$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    exit;
}

// check for required key in SERVER super global
$flutterwaveKeys = ["SECRET_KEY","PUBLIC_KEY","ENV"];
asort($flutterwaveKeys);

try{
    foreach($flutterwaveKeys as $key)
    {
        if(!array_key_exists($key, $_SERVER))
        {
            throw new InvalidArgumentException("$key variable not supplied.");
        }
    }
}catch(\Exception $e)
{
    echo "Flutterwave: " .$e->getMessage();
    echo "<br /> Kindly create a .env in the project root ";
    exit;
}