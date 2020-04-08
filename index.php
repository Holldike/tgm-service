<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use \MtProtoDriver\MadelineProto;

$driver = new MadelineProto();

$driver->login();

$requestValidator = new RequestValidator();
$telegram = new Telegram($driver);
$router = new Router();
$logger = new Logger();

$application = new Application(
    $router,
    $telegram,
    $logger,
    $requestValidator
);

$response = $application->run();
$response->send();