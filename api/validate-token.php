<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['token'])) {
    echo json_encode(['success' => false, 'message' => 'Token required']);
    exit;
}

$token = $data['token'];

// Find token and ensure it's not expired
$stmt = $pdo->prepare("
    SELECT s.user_id, s.expires_at, u.name, u.email, u.plan 
    FROM user_sessions s
    JOIN users u ON s.user_id = u.id
    WHERE s.token = ?
");
$stmt->execute([$token]);
$sessionData = $stmt->fetch();

if ($sessionData) {
    if (strtotime($sessionData['expires_at']) > time()) {
        echo json_encode([
            'success' => true,
            'message' => 'Token valid',
            'user' => [
                'id' => $sessionData['user_id'],
                'name' => $sessionData['name'],
                'email' => $sessionData['email'],
                'plan' => $sessionData['plan']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Token expired']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
}
?>
