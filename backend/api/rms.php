<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE');

// Include the database configuration file
include '../config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
$method = $_SERVER['REQUEST_METHOD'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

switch ($method) {
    case 'GET':
        $sql = "SELECT timestamp, nickname, ats, skips, verified FROM rms";
        $result = $conn->query($sql);

        $rmsData = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $rmsData[] = $row;
            }
        }
        echo json_encode($rmsData);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $player_name = $data['player_name'];
        $score = $data['score'];

        $stmt = $conn->prepare("INSERT INTO high_scores (player_name, score) VALUES (?, ?)");
        $stmt->bind_param("si", $player_name, $score);

        if ($stmt->execute()) {
            echo json_encode(["message" => "New record created successfully"]);
        } else {
            echo json_encode(["message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

$conn->close();
?>
