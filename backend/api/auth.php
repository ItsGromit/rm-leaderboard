<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

// Include the database configuration file
include '../config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    $conn->close();
    die();
}

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];
$playerId = $data['player_id'];
$pluginVersion = isset($data['plugin_version']) ? $data['plugin_version'] : null;

if ($token === $openplanetSecret) {
    // Test token, don't verify with Openplanet
    // Just return a test mock data

    // Retreive display name from tm.io
    $options = [
        "http" => [
            "header" => "User-Agent: PHP/".phpversion()." RMC_API/1.0 (Greep & FlinkTM)" // User-Agent header is important
        ]
    ];
    $context = stream_context_create($options);
    $tmioResp = file_get_contents("https://trackmania.io/api/player/".$playerId, false, $context);
    $tmioData = json_decode($tmioResp, true);

    $openplanetData = [
        'player_id' => $playerId,
        'player_name' => $tmioData['displayname']
    ];
} else {
    // Verify the token with Openplanet
    $openplanetUrl = "https://openplanet.dev/api/auth/validate";
    $postdata = http_build_query(
        array(
            'token' => $token,
            'secret' => $openplanetSecret
        )
    );
    $options = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded; User-Agent: PHP/'.phpversion().' RMC_API/1.0 (Greep & FlinkTM)',
            'content' => $postdata
        )
    );
    $context = stream_context_create($options);
    $openplanetResponse = file_get_contents($openplanetUrl, false, $context);
    $openplanetData = json_decode($openplanetResponse, true);
}

if (isset($openplanetData) && $openplanetData['player_id'] === $playerId) {

    // Player is connected with Openplanet
    // Check if the player is already in the database
    $playerExists = false;
    if ($stmt = $conn->prepare("SELECT * FROM `players` WHERE `accountId` = ?")) {
        $stmt->bind_param("s", $openplanetData['player_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $playerExists = $result->num_rows == 1;
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
        $conn->close();
        die();
    }

    if ($playerExists == false) {
        $sql = "INSERT INTO `players` (`accountId`, `displayName`, `lastLogon`, `lastPluginVersion`, `lastToken`) VALUES (?, ?, now(), ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $openplanetData['player_id'], $openplanetData['player_name'], $pluginVersion, $token);
            $stmt->execute();
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
            $conn->close();
            die();
        }
    } else {
        $sql = "UPDATE `players` SET `displayName` = ?, `lastLogon` = now(), `lastPluginVersion` = ?, `lastToken` = ? WHERE `accountId` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $openplanetData['player_name'], $pluginVersion, $token, $openplanetData['player_id']);
            $stmt->execute();
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
            $conn->close();
            die();
        }
    }

    echo json_encode(["success" => true, "message" => "Authenticated successfully", "player_name" => $openplanetData['player_name']]);
} else {
    http_response_code(401);
    $errMsg = "Authentication failed";
    if (isset($openplanetData) && isset($openplanetData["error"])) {
        $errMsg .= ": ".$openplanetData["error"];
    }
    echo json_encode(["success" => false, "message" => $errMsg]);
    $conn->close();
    die();
}
?>
