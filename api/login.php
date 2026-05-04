<?php
require_once __DIR__ . '/db.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Missing email or password.']);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

$stmt = $pdo->prepare("SELECT id, name, password, plan, is_admin FROM users WHERE email = ? AND status = 'active'");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Generate session token (just basic auth handling for frontend)
    $token = bin2hex(random_bytes(32));
    
    // In a stateless JWT approach we'd return a JWT, but for simplicity we can use standard PHP session 
    // AND return a token to be saved in localStorage
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = $user['is_admin'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $email,
            'plan' => $user['plan'],
            'is_admin' => (bool)$user['is_admin'],
            'token' => $token // Optional: use this token for Authorization header in future API calls
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}
?>
