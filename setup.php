<?php  

require_once "./vendor/composer/autoload_files.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();