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
$refreshToken  = $data['refresh_token'] ?? null;


$token = isset($token) ? trim((string)$token) : null;
if ($token && str_starts_with($token, 'Bearer ')) {
    $token = substr($token, 7); // "Bearer " removed
}
$refreshToken = isset($refreshToken) ? trim((string)$refreshToken) : null;
if ($refreshToken === '') {
    $refreshToken = null;
}

if (!$token || !$playerId) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields: token, player_id"]);
    exit;
}

/**
 * @return array{0: array, 1: bool} Decoded response data and whether an HTTP error occurred
 */
function callOpenplanetEndpoint(string $url, array $payload, int $timeout = 5): array
{
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  =>
                "Content-Type: application/json\r\n" .
                "User-Agent: PHP/" . phpversion() . " RMC_API/1.0 (Greep & FlinkTM)\r\n",
            'content' => $payloadJson,
            'timeout' => $timeout,
            'ignore_errors' => true,
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        return [[], true];
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        $decoded = [];
    }

    return [$decoded, false];
}

function isExpiredTokenResponse(array $response): bool
{
    $error = $response['error'] ?? null;
    if (is_string($error)) {
        $normalizedError = strtolower($error);
        if (in_array($normalizedError, ['token_expired', 'expired_token', 'invalid_grant'], true)) {
            return true;
        }
    }

    $fragments = [];
    foreach (['error_description', 'message', 'reason', 'detail'] as $key) {
        if (isset($response[$key]) && is_string($response[$key])) {
            $fragments[] = strtolower($response[$key]);
        }
    }

    if (isset($response['errors']) && is_array($response['errors'])) {
        foreach ($response['errors'] as $errValue) {
            if (is_string($errValue)) {
                $fragments[] = strtolower($errValue);
            } elseif (is_array($errValue)) {
                $fragments[] = strtolower(json_encode($errValue));
            }
        }
    }

    $combined = implode(' ', $fragments);
    return $combined !== '' && str_contains($combined, 'expired');
}

// Auth with Openplanet
$tokenForStorage      = $token;
$latestRefreshToken   = $refreshToken;
$openplanetData       = [];
$expiredWithoutRefresh = false;

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
    $openplanetValidateUrl = "https://openplanet.dev/api/auth/validate";
    [$openplanetData, $httpError] = callOpenplanetEndpoint($openplanetValidateUrl, [
        'token'  => $token,
        'secret' => $openplanetSecret,
    ]);

    if ($httpError) {
        http_response_code(502);
        echo json_encode(["success" => false, "message" => "Openplanet request failed"]);
        $conn->close();
        exit;
    }

    $tokenExpired = isExpiredTokenResponse($openplanetData);

    if ((!isset($openplanetData['account_id']) || $openplanetData['account_id'] !== $playerId)
        && $refreshToken
        && $tokenExpired
    ) {
        $refreshUrl = "https://openplanet.dev/api/auth/token";
        [$refreshData, $refreshHttpError] = callOpenplanetEndpoint($refreshUrl, [
            'secret'        => $openplanetSecret,
            'refresh_token' => $refreshToken,
        ]);

        if ($refreshHttpError) {
            http_response_code(502);
            echo json_encode(["success" => false, "message" => "Openplanet refresh request failed"]);
            $conn->close();
            exit;
        }

        $newToken = null;
        if (isset($refreshData['token']) && is_string($refreshData['token'])) {
            $newToken = trim($refreshData['token']);
        } elseif (isset($refreshData['access_token']) && is_string($refreshData['access_token'])) {
            $newToken = trim($refreshData['access_token']);
        }

        if ($newToken) {
            $token = $newToken;
            $tokenForStorage = $newToken;
            if (isset($refreshData['refresh_token']) && is_string($refreshData['refresh_token'])) {
                $latestRefreshToken = trim($refreshData['refresh_token']) ?: $latestRefreshToken;
            }

            if (isset($refreshData['account_id'])) {
                $openplanetData = $refreshData;
            } else {
                [$openplanetData, $httpError] = callOpenplanetEndpoint($openplanetValidateUrl, [
                    'token'  => $token,
                    'secret' => $openplanetSecret,
                ]);

                if ($httpError) {
                    http_response_code(502);
                    echo json_encode(["success" => false, "message" => "Openplanet request failed"]);
                    $conn->close();
                    exit;
                }
            }
        } else {
            http_response_code(401);
            $errorMessage = "Authentication failed: token expired and refresh was rejected";
            if (isset($refreshData['error']) && is_string($refreshData['error'])) {
                $errorMessage .= " (" . $refreshData['error'] . ")";
            }
            if (isset($refreshData['error_description']) && is_string($refreshData['error_description'])) {
                $errorMessage .= ": " . $refreshData['error_description'];
            }
            echo json_encode(["success" => false, "message" => $errorMessage]);
            $conn->close();
            exit;
        }
    } elseif ((!isset($openplanetData['account_id']) || $openplanetData['account_id'] !== $playerId) && $tokenExpired && !$refreshToken) {
        $expiredWithoutRefresh = true;
    }
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
            $stmt->bind_param("ssss", $openplanetData['account_id'], $openplanetData['display_name'], $pluginVersion, $tokenForStorage);
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
            $stmt->bind_param("ssss", $openplanetData['display_name'], $pluginVersion, $tokenForStorage, $openplanetData['account_id']);
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
        "player_name"  => $openplanetData['display_name'],
        "token"        => $tokenForStorage,
        "refresh_token" => $latestRefreshToken
    ]);
} else {
    http_response_code(401);
    $errMsg = "Authentication failed";
    if ($expiredWithoutRefresh) {
        $errMsg .= ": token expired and no refresh token was provided";
    } elseif (isset($openplanetData["error"])) {
        $errMsg .= ": ".$openplanetData["error"];
        if (isset($openplanetData['error_description'])) {
            $errMsg .= " - ".$openplanetData['error_description'];
        }
    } elseif (isset($openplanetData['error_description'])) {
        $errMsg .= ": ".$openplanetData['error_description'];
    }
    echo json_encode(["success" => false, "message" => $errMsg]);
    $conn->close();
    exit;
}

$conn->close();
?>
