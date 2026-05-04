<?php
session_start();
require_once __DIR__ . '/../api/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['is_admin']) {
    header("Location: dashboard");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password, is_admin FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_admin']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = 1;
            header("Location: dashboard");
            exit;
        } else {
            $error = "Acesso negado. Requer privilégios de administrador.";
        }
    } else {
        $error = "E-mail ou senha inválidos.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Projeto7 App Hub</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-login {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--bg-color);
        }
        .admin-login .modal-content {
            opacity: 1;
            transform: none;
            box-shadow: var(--shadow);
            display: block;
        }
        .error { color: red; text-align: center; margin-bottom: 15px; font-size: 0.9rem; }
    </style>
</head>
<body class="admin-login">
    <div class="modal-content">
        <h3 class="modal-title">Login Administrativo</h3>
        <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block">Entrar no Painel</button>
        </form>
    </div>
</body>
</html>
