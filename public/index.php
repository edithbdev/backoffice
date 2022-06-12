<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     header('Access-Control-Allow-Origin: *');
//     header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
//     header('Access-Control-Allow-Headers: Authorization, Content-Type');
//     header('HTTP/1.1 200 OK');
//     exit;
// }

//Access to XMLHttpRequest at 'https://backoffice.edithbredon.fr/api/projects' from origin 'https://edithbredon.fr' has been blocked by CORS policy: Response to preflight request doesn't pass access control check: It does not have HTTP ok status.

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: https://edithbredon.fr');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
    header('HTTP/1.1 200 OK');
    exit;
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
