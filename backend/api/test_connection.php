<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
else echo json_encode(["success" => true, "error" => "Connected successfully"]);
$conn->close();
?>