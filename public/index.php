<?php

use App\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['APP_DEBUG'] ?? ($_ENV['APP_DEBUG'] ?? false)) {
    umask(0000);
    Symfony\Component\ErrorHandler\Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Symfony\Component\HttpFoundation\Request::setTrustedProxies(
        explode(',', $trustedProxies),
        Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR |
        Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST |
        Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO |
        Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT |
        Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PREFIX
    );
}
if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Symfony\Component\HttpFoundation\Request::setTrustedHosts([
        $trustedHosts
    ]);
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool)($_SERVER['APP_DEBUG'] ?? ($_ENV['APP_DEBUG'] ?? false)));
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
