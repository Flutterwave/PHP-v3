<?php  

use Flutterwave\Helper;
use Dotenv\Dotenv;

$flutterwave_installation = 'composer';

if( !file_exists( '.env' ) && !is_dir('vendor')) {
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../"); # on the event that the package is install via composer.
} else {
    $flutterwave_installation = "manual";
    $dotenv = Dotenv::createImmutable(__DIR__); # on the event that the package is forked or donwload directly from Github.
}

$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    if (PHP_SAPI === 'cli') {
        echo "❌ Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    } else {
        echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    }
    
    exit;
}

// check for required key in ENV super global
$flutterwaveKeys = ["SECRET_KEY","PUBLIC_KEY","ENV", "ENCRYPTION_KEY"];
asort($flutterwaveKeys);

try{
    foreach($flutterwaveKeys as $key)
    {

        $new_key = sprintf( 'FLW_%s', $key );
        if(empty($_ENV[ $new_key ]) && empty(\getenv($new_key)) && empty($_ENV[ $key ]) && empty(\getenv($key)))
        {
            throw new InvalidArgumentException("$new_key or $key environment variable missing.");
        }

    }
}catch(\Exception $e)
{
    if (PHP_SAPI === 'cli') {
        echo "❌❌Flutterwave sdk: " .$e->getMessage();
        echo "Kindly create a .env in the project root and add the required environment variables. ❌". PHP_EOL;
    } else {
        echo "Flutterwave: Setup incomplete. check your environment variables are set currently. confirm .env contains the required variables.";
    }

    exit;
}

$keys = [
    'SECRET_KEY' => $_ENV['FLW_SECRET_KEY'] ?? ($_ENV['SECRET_KEY'] ?? getenv('FLW_SECRET_KEY') ?: getenv('SECRET_KEY')),
    'PUBLIC_KEY' => $_ENV['FLW_PUBLIC_KEY'] ?? ($_ENV['PUBLIC_KEY'] ?? getenv('FLW_PUBLIC_KEY') ?: getenv('PUBLIC_KEY')),
    'ENV' => $_ENV['FLW_ENV'] ?? ($_ENV['ENV'] ?? getenv('FLW_ENV') ?: getenv('ENV')),
    'ENCRYPTION_KEY' => $_ENV['FLW_ENCRYPTION_KEY'] ?? ($_ENV['ENCRYPTION_KEY'] ?? getenv('FLW_ENCRYPTION_KEY') ?: getenv('ENCRYPTION_KEY'))
];