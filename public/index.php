<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: http://localhost:8000');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, Content-Type, X-Requested-With, X-Auth-Token, Origin, Accept, Access-Control-Request-Method, Access-Control-Request-Headers');
    header('Access-Control-Max-Age: 1728000'); 
    header('Content-Length: 0');
    header('Content-Type: text/plain, charset=UTF-8');
    header('HTTP/1.1 200 OK');
    header('Permissions-Policy: interest-cohort=()');
    exit;
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
