<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

use \MtProtoDriver\MadelineProto;
use Symfony\Component\HttpFoundation\Request;

$driver = new MadelineProto();

$driver->login();

$requestValidator = new RequestValidator();
$request = Request::createFromGlobals();
$telegram = new Telegram($driver);
$router = new Router();
$logger = new Logger();

$application = new Application(
    $requestValidator,
    $telegram,
    $request,
    $logger,
    $router
);

$response = $application->run();
$response->send();