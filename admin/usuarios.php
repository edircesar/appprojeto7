<?php
session_start();
require_once __DIR__ . '/../api/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login");
    exit;
}

// Handle Toggle Admin
if (isset($_GET['toggle_admin'])) {
    $id = (int)$_GET['toggle_admin'];
    // Prevent self-demotion for safety
    if ($id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: usuarios");
    exit;
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: usuarios");
    exit;
}

$users = $pdo->query("SELECT id, name, email, plan, is_admin, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários - Painel Admin</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #2c3e50; color: white; padding: 20px 0; flex-shrink: 0; }
        .sidebar-header { padding: 0 20px 20px; border-bottom: 1px solid #3e4f5f; margin-bottom: 20px; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li a { 
            display: flex; align-items: center; gap: 10px; padding: 12px 20px; 
            color: #bdc3c7; text-decoration: none; transition: 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #34495e; color: white; }
        .main-content { flex: 1; padding: 30px; background: #f0f2f5; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; color: #666; font-weight: 600; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-admin { background: #e8f5e9; color: #2e7d32; }
        .badge-user { background: #e3f2fd; color: #1565c0; }
        .btn-small { padding: 6px 10px; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 4px; }
        .btn-outline { border: 1px solid #ddd; background: white; color: #666; }
        .btn-outline:hover { background: #f5f5f5; }
        .btn-danger { background: #fee2e2; color: #991b1b; }
        .btn-danger:hover { background: #fecaca; }
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
                <li><a href="dashboard"><span class="material-icons">dashboard</span> Gerenciar Apps</a></li>
                <li><a href="usuarios" class="active"><span class="material-icons">people</span> Gerenciar Usuários</a></li>
                <li><a href="/" target="_blank"><span class="material-icons">launch</span> Ver Site / Cadastro</a></li>
                <li><a href="perfil"><span class="material-icons">person</span> Minha Conta</a></li>
                <li style="margin-top: 20px;"><a href="../api/logout.php" style="color: #e74c3c;"><span class="material-icons">logout</span> Sair</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2>Gestão de Usuários</h2>
                    <span style="color: #666;"><?php echo count($users); ?> usuários registrados</span>
                </div>
                
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome / E-mail</th>
                                <th>Plano</th>
                                <th>Nível</th>
                                <th>Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong><br>
                                    <small style="color: #888;"><?php echo htmlspecialchars($user['email']); ?></small>
                                </td>
                                <td><span style="text-transform: capitalize;"><?php echo $user['plan']; ?></span></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge badge-admin">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">Usuário Básico</span>
                                    <?php endif; ?>
                                </td>
                                <td><small><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></small></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <a href="?toggle_admin=<?php echo $user['id']; ?>" class="btn btn-small btn-outline" title="Alternar Nível de Acesso">
                                                <span class="material-icons" style="font-size: 16px;">manage_accounts</span>
                                                <?php echo $user['is_admin'] ? 'Rebaixar' : 'Promover'; ?>
                                            </a>
                                            <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?')" title="Excluir Usuário">
                                                <span class="material-icons" style="font-size: 16px;">delete</span>
                                            </a>
                                        <?php else: ?>
                                            <small style="color: #aaa; font-style: italic;">Você</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
