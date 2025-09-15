<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Request Method Handling
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

// Include DB Config
include '../config.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit;
}

// Parse Input Data
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
$raw = file_get_contents('php://input');
$data = null;

if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid JSON body"]);
        exit;
    }
} elseif (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
    parse_str($raw, $data);
} else {
    // Fallback
    $try = json_decode($raw, true);
    if (is_array($try)) {
        $data = $try;
    } elseif (!empty($raw)) {
        parse_str($raw, $tmp);
        $data = $tmp;
    } else {
        $data = $_POST;
    }
}

$token         = $data['token'] ?? null;
$playerId      = $data['player_id'] ?? null;
$pluginVersion = $data['plugin_version'] ?? null;


$token = isset($token) ? trim((string)$token) : null;
if ($token && str_starts_with($token, 'Bearer ')) {
    $token = substr($token, 7); // "Bearer " removed
}

if (!$token || !$playerId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields: token, player_id"]);
    exit;
}

// Auth with Openplanet
if ($token === $openplanetSecret) {
    // Test token path
    $options = [
        "http" => [
            "header" => "User-Agent: PHP/".phpversion()." RMC_API/1.0 (Greep & FlinkTM)\r\n",
            "timeout" => 5
        ]
    ];
    $context = stream_context_create($options);
    $tmioResp = @file_get_contents("https://trackmania.io/api/player/".$playerId, false, $context);
    $tmioData = json_decode($tmioResp, true) ?: [];

    $openplanetData = [
        'account_id'   => $playerId,
        'display_name' => $tmioData['displayname'] ?? "Unknown"
    ];
} else {
    // Verify token via Openplanet (JSON instead of x-www-form-urlencoded)
    $openplanetUrl = "https://openplanet.dev/api/auth/validate";
    $payload = json_encode([
        'token'  => $token,            // no "Bearer "
        'secret' => $openplanetSecret,
    ], JSON_UNESCAPED_SLASHES);

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  =>
                "Content-Type: application/json\r\n" .
                "User-Agent: PHP/".phpversion()." RMC_API/1.0 (Greep & FlinkTM)\r\n",
            'content' => $payload,
            'timeout' => 5,
            'ignore_errors' => true,
        ]
    ];

    $context = stream_context_create($options);
    $openplanetResponse = @file_get_contents($openplanetUrl, false, $context);
    if ($openplanetResponse === false) {
        http_response_code(502);
        echo json_encode(["success" => false, "message" => "Openplanet request failed"]);
        $conn->close();
        exit;
    }
    $openplanetData = json_decode($openplanetResponse, true) ?: [];
}

// Check Auth Result
if (isset($openplanetData['account_id']) && $openplanetData['account_id'] === $playerId) {
    // Player found
    $playerExists = false;
    if ($stmt = $conn->prepare("SELECT 1 FROM `players` WHERE `accountId` = ?")) {
        $stmt->bind_param("s", $openplanetData['account_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $playerExists = $result->num_rows === 1;
        $stmt->close();
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "DB prepare error: ".$conn->error]);
        $conn->close();
        exit;
    }

    if (!$playerExists) {
        $sql = "INSERT INTO `players` (`accountId`, `displayName`, `lastLogon`, `lastPluginVersion`, `lastToken`)
                VALUES (?, ?, NOW(), ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $openplanetData['account_id'], $openplanetData['display_name'], $pluginVersion, $token);
            $stmt->execute();
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "DB insert error: ".$conn->error]);
            $conn->close();
            exit;
        }
    } else {
        $sql = "UPDATE `players`
                SET `displayName` = ?, `lastLogon` = NOW(), `lastPluginVersion` = ?, `lastToken` = ?
                WHERE `accountId` = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssss", $openplanetData['display_name'], $pluginVersion, $token, $openplanetData['account_id']);
            $stmt->execute();
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "DB update error: ".$conn->error]);
            $conn->close();
            exit;
        }
    }

    echo json_encode([
        "success"      => true,
        "message"      => "Authenticated successfully",
        "player_name"  => $openplanetData['display_name']
    ]);
} else {
    http_response_code(401);
    $errMsg = "Authentication failed";
    if (isset($openplanetData["error"])) {
        $errMsg .= ": ".$openplanetData["error"];
    }
    echo json_encode(["success" => false, "message" => $errMsg]);
    $conn->close();
    exit;
}

$conn->close();
?>
