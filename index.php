<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use \MtProtoDriver\MadelineProto;

$driver = new MadelineProto();

$driver->login();

$telegram = new Telegram($driver);
$router = new Router();
$requestValidator = new RequestValidator();

$application = new Application(
    $router,
    $telegram,
    $requestValidator
);

$response = $application->run();
$response->send();