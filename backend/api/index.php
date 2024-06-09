<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

$resource = array_shift($request);

switch ($resource) {
    case 'rmc_scores':
        include 'rmc_scores.php';
        break;
    case 'submit_score':
        include 'submit_score.php';
        break;
    case 'auth':
        include 'auth.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Resource not found"]);
        break;
}
?>
