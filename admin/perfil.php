<?php
session_start();
require_once __DIR__ . '/../api/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($new_password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashed_password, $_SESSION['user_id']])) {
            $message = "Senha alterada com sucesso!";
        } else {
            $error = "Erro ao atualizar a senha.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Painel Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; padding: 20px 0; }
        .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid #3e4f5f; margin-bottom: 20px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li a { 
            display: flex; align-items: center; gap: 10px; padding: 12px 20px; 
            color: #bdc3c7; text-decoration: none; transition: 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #34495e; color: white; }
        .main-content { flex: 1; padding: 30px; background: #f0f2f5; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); max-width: 500px; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body style="padding-top: 0;">

    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Projeto7 Hub</h3>
                <small>Administrador</small>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard"><span class="material-icons">dashboard</span> Dashboard</a></li>
                <li><a href="/" target="_blank"><span class="material-icons">launch</span> Ver Site / Cadastro</a></li>
                <li><a href="perfil" class="active"><span class="material-icons">person</span> Minha Conta</a></li>
                <li style="margin-top: 20px;"><a href="../api/logout.php" style="color: #e74c3c;"><span class="material-icons">logout</span> Sair</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="card">
                <h2>Alterar Senha</h2>
                <p style="color: #666; margin-bottom: 25px;">Mantenha sua conta de administrador segura.</p>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="password" name="new_password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Senha</label>
                        <input type="password" name="confirm_password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-block">Atualizar Senha</button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
