<?php  

use Flutterwave\Helper;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__."/../../../");
$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    exit;
}

// check for required key in SERVER super global
$flutterwaveKeys = ["SECRET_KEY","PUBLIC_KEY","ENV", "ENCRYPTION_KEY"];
asort($flutterwaveKeys);

try{
    foreach($flutterwaveKeys as $key)
    {
        if(!array_key_exists($key, $_SERVER))
        {
            throw new InvalidArgumentException("$key environment variable missing.");
        }
    }
}catch(\Exception $e)
{
    echo "<code>Flutterwave sdk: " .$e->getMessage()."</code>";

    echo "<br /> Kindly create a <code>.env </code> in the project root and add the required environment variables.";
    exit;
}