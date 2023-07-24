<?php  

use Flutterwave\Helper;
use Dotenv\Dotenv;

$flutterwave_installation = 'composer';

if( !file_exists( '.env' )) {
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../"); # on the event that the package is install via composer.
} else {
    $flutterwave_installation = "manual";
    $dotenv = Dotenv::createImmutable(__DIR__); # on the event that the package is forked or donwload directly from Github.
}

$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    exit;
}

// check for required key in ENV super global
$flutterwaveKeys = ["SECRET_KEY","PUBLIC_KEY","ENV", "ENCRYPTION_KEY"];
asort($flutterwaveKeys);

try{
    foreach($flutterwaveKeys as $key)
    {
        if( empty( $_ENV[ $key ] ) )
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

$keys = [
    'SECRET_KEY' => $_ENV['SECRET_KEY'],
    'PUBLIC_KEY' => $_ENV['PUBLIC_KEY'],
    'ENV' => $_ENV['ENV'],
    'ENCRYPTION_KEY' => $_ENV['ENCRYPTION_KEY']
];