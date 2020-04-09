<?php
require_once 'vendor/autoload.php';

use \MtProtoDriver\MadelineProto;
use Symfony\Component\HttpFoundation\Request;

$driver = new MadelineProto();

$driver->login();

if (!file_exists('config.php')) {
    throw new Exception('Need config file');
}

require_once 'config.php';

$requestValidator = new Validator();
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