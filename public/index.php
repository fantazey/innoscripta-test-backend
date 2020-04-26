<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

// used for separated react server and symfony server

//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,HEAD,OPTIONS");
//header("Access-Control-Allow-Headers: Origin,Content-Type,Accept,Authorization");
//header("Access-Control-Allow-Headers: *");

//if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//    header("Access-Control-Allow-Origin: *");
//    header("Access-Control-Allow-Credentials: true");
//    header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,HEAD,OPTIONS");
//    header("Access-Control-Allow-Headers: Origin,Content-Type,Accept,Authorization");
//    header("Access-Control-Allow-Headers: *");
//    header("Content-Type: text/plain charset=UTF-8");
//    header("Content-Length: 0");
//    return;
//}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
