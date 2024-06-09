<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the database configuration file
include 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the query
$sql = "SELECT timestamp, nickname, ats, golds, verified FROM rmc";
$result = $conn->query($sql);

$rmcData = array();

if ($result->num_rows > 0) {
    // Fetch all entries
    while($row = $result->fetch_assoc()) {
        $rmcData[] = $row;
    }
}


// Output the rmc data as JSON
echo json_encode($rmcData);

// Close connection
$conn->close();
?>