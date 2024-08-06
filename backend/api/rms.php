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
        $objective = isset($_GET['objective']) ? $_GET['objective'] : 'author';

        if ($time !== 'all') {
            // Query for a specific year
            $sql = "
                SELECT rms.*, players.displayName
                FROM `rms`
                INNER JOIN `players` ON `rms`.`accountId` = `players`.`accountId`
                INNER JOIN (
                    SELECT `accountId`, MAX(`goals`) AS maxGoals
                    FROM `rms`
                    WHERE `objective` = ? AND YEAR(`submitTime`) = ?
                    GROUP BY `accountId`
                ) AS best_runs ON `rms`.`accountId` = best_runs.`accountId`
                AND `rms`.`goals` = best_runs.`maxGoals`
                WHERE YEAR(`rms`.`submitTime`) = ?
                ORDER BY `rms`.`goals` DESC, `rms`.`skips` ASC, `rms`.`timeSurvived` DESC;
            ";
        } else {
            // Query across all years
            $sql = "
                SELECT rms.*, players.displayName
                FROM `rms`
                INNER JOIN `players` ON `rms`.`accountId` = `players`.`accountId`
                INNER JOIN (
                    SELECT `accountId`, MAX(`goals`) AS maxGoals
                    FROM `rms`
                    WHERE `objective` = ?
                    GROUP BY `accountId`
                ) AS best_runs ON `rms`.`accountId` = best_runs.`accountId`
                AND `rms`.`goals` = best_runs.`maxGoals`
                ORDER BY `rms`.`goals` DESC, `rms`.`skips` ASC, `rms`.`timeSurvived` DESC;
            ";
        }

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            if ($time !== 'all') {
                $stmt->bind_param("sii", $objective, $time, $time);
            } else {
                $stmt->bind_param("s", $objective);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $rmsData = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $rmsData[] = $row;
                }
            }
            echo json_encode($rmsData);

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

        if ($player["banned"] == 1) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "You've been banned from posting to the leaderboard"]);
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $accountId = $player['accountId'];
        $objective = isset($data['objective']) ? $data['objective'] : "author";

        if (!isset($data['goal']) || !isset($data['skips']) || !isset($data['time_survived'])) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "goal, skips and time_survived must be indicated in the body"]);
            break;
        }

        $goals = $data['goal'];
        $skips = $data['skips'];
        $timeSurvived = $data['time_survived'];

        $stmt = $conn->prepare("INSERT INTO `rms` (`accountId`, `objective`, `submitTime`, `goals`, `skips`, `timeSurvived`) VALUES (?, ?, now(), ?, ?, ?)");
        $stmt->bind_param("ssiii", $accountId, $objective, $goals, $skips, $timeSurvived);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Record uploaded successfully to the leaderboard"]);
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
