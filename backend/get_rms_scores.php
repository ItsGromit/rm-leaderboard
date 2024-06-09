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
$sql = "SELECT timestamp, nickname, ats, golds, verified FROM rms";
$result = $conn->query($sql);

$rmsData = array();

if ($result->num_rows > 0) {
    // Fetch all entries
    while($row = $result->fetch_assoc()) {
        $rmsData[] = $row;
    }
}


// Output the rms data as JSON
echo json_encode($rmsData);

// Close connection
$conn->close();
?>