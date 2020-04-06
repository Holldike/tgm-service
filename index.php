<?php
require_once 'bootstrap.php';

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

$application->run();