<?php
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

// Check if a token is provided in the header for "logged-in" view
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
$isLoggedIn = false;

// Simple check - in a real app, validate the token against db/JWT signature
if (strpos($authHeader, 'Bearer ') !== false) {
    $token = str_replace('Bearer ', '', $authHeader);
    if (!empty($token) && $token !== 'null') {
        $isLoggedIn = true;
    }
}

if ($isLoggedIn) {
    $stmt = $pdo->query("SELECT id, name, description, url, icon, category, is_public FROM apps ORDER BY category, name");
} else {
    $stmt = $pdo->query("SELECT id, name, description, url, icon, category, is_public FROM apps WHERE is_public = 1 ORDER BY category, name");
}

$apps = $stmt->fetchAll();

echo json_encode(['success' => true, 'data' => $apps]);
?>
