<?php

$storagePath = '/tmp/storage';

foreach ([
    "$storagePath/framework/views",
    "$storagePath/framework/cache/data",
    "$storagePath/framework/sessions",
    "$storagePath/logs",
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

function setEnvVar(string $key, string $value): void
{
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

setEnvVar('LARAVEL_STORAGE_PATH', $storagePath);
setEnvVar('VIEW_COMPILED_PATH', "$storagePath/framework/views");
setEnvVar('APP_CONFIG_CACHE', '/tmp/config.php');
setEnvVar('APP_ROUTES_CACHE', '/tmp/routes.php');
setEnvVar('APP_SERVICES_CACHE', '/tmp/services.php');
setEnvVar('APP_PACKAGES_CACHE', '/tmp/packages.php');

require __DIR__ . '/../public/index.php';