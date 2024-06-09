<?php
header('Access-Control-Allow-Origin: *');

// Include the database configuration file
include 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the request
$player_name = $_POST['player_name'];
$score = $_POST['score'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO high_scores (player_name, score) VALUES (?, ?)");
$stmt->bind_param("si", $player_name, $score);

// Execute the statement
if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>