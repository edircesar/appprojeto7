<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

$token = str_replace('Bearer ', '', $authHeader);

// In a real app we'd validate the session/JWT here
// For simplicity, we just check if token is provided and we pass a generic user ID
// Let's assume the frontend sends the user_id in the body for now, or it should be verified via JWT
if (empty($token) || $token === 'null') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($data['app_id']) || !isset($data['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing app_id or user_id']);
    exit;
}

$userId = (int)$data['user_id'];
$appId = (int)$data['app_id'];

// Generate a temporary access token for the specific app
$microSaaSToken = bin2hex(random_bytes(16));
$expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

$stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
if ($stmt->execute([$userId, $microSaaSToken, $expiresAt])) {
    // Fetch app URL to redirect to
    $appStmt = $pdo->prepare("SELECT url FROM apps WHERE id = ?");
    $appStmt->execute([$appId]);
    $app = $appStmt->fetch();
    
    if ($app) {
        $targetUrl = $app['url'] . '?token=' . $microSaaSToken;
        echo json_encode(['success' => true, 'token' => $microSaaSToken, 'target_url' => $targetUrl]);
    } else {
        echo json_encode(['success' => false, 'message' => 'App not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to generate token']);
}
?>
