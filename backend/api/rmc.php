<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE');

// Include the database configuration file
include '../config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    $conn->close();
    die();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get the time parameter from the query string
        $time = isset($_GET['year']) ? $_GET['year'] : 'all';

        // Prepare the SQL query
        $sql = "SELECT DISTINCT `rmc`.*, `players`.`displayName` FROM `rmc` INNER JOIN `players` ON `rmc`.`accountId` = `players`.`accountId` GROUP BY `rmc`.`accountId` ORDER BY `rmc`.`goals` DESC, `rmc`.`belowGoals` DESC";
        if ($time !== 'all') {
            $sql .= " WHERE YEAR(`submitTime`) = ?";
        }

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            if ($time !== 'all') {
                $stmt->bind_param("i", $time); // Bind the time parameter as an integer
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $rmcData = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $rmcData[] = $row;
                }
            }
            echo json_encode($rmcData);

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
        }
        $conn->close();
        break;

    case 'POST':

        // Check player token (in Authorization header)
        $headers = getallheaders();
        if (!array_key_exists('Authorization', $headers)) {
            echo json_encode(["success" => false, "error" => "Authorization header is missing"]);
            $conn->close();
            break;
        }

        if (substr($headers['Authorization'], 0, 6) !== 'Token ') {
            echo json_encode(["success" => false, "error" => "Token keyword is missing"]);
            $conn->close();
            break;
        }

        // Get Player Token
        $token = trim(substr($headers['Authorization'], 6));

        // Check if token is listed in the database
        $playerExists = false;
        if ($stmt = $conn->prepare("SELECT * FROM `players` WHERE `lastToken` = ?")) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $player = $row;
            }
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
            $conn->close();
            die();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $accountId = $player['accountId'];
        $objective = isset($data['objective']) ? $data['objective'] : "author";
        $goals = $data['goal'];
        $belowGoals = $data['below_goal'];

        if (!isset($goals) || !isset($belowGoals)) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "goal and below_goal must be indicated in the body"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO `rmc` (`accountId`, `objective`, `submitTime`, `goals`, `belowGoals`) VALUES (?, ?, now(), ?, ?)");
        $stmt->bind_param("ssii", $accountId, $objective, $goals, $belowGoals);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "New record created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed"]);
        $conn->close();
        break;
}
?>
