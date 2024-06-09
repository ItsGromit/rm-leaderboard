<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Include the database configuration file
include '../config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);
$player_name = $data['player_name'];
$score = $data['score'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO high_scores (player_name, score) VALUES (?, ?)");
$stmt->bind_param("si", $player_name, $score);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["message" => "New record created successfully"]);
} else {
    echo json_encode(["message" => "Error: " . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
