<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Preflight
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}


$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

$resource = array_shift($request);

switch ($resource) {
    case 'auth':
        include 'auth.php';
        break;
    case 'rmc':
        include 'rmc.php';
        break;
    case 'rms':
        include 'rms.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Resource not found"]);
        break;
}
?>
