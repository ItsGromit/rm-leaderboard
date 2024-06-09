<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

// Include the database configuration file
include '../config.php';

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];
$playerId = $data['player_id'];

// Verify the token with Openplanet
$openplanetUrl = "https://openplanet.dev/api/verify_token"; // Replace with the actual Openplanet API endpoint
$openplanetResponse = file_get_contents($openplanetUrl . "?token=" . urlencode($token));
$openplanetData = json_decode($openplanetResponse, true);

if ($openplanetData && $openplanetData['player_id'] === $playerId) {
    echo json_encode(["message" => "Authenticated successfully", "player_name" => $openplanetData['player_name']]);
} else {
    http_response_code(401);
    echo json_encode(["message" => "Authentication failed"]);
}
?>
