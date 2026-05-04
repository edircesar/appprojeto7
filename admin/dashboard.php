<?php
session_start();
require_once __DIR__ . '/../api/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login");
    exit;
}

// Handle Add App
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $icon = $_POST['icon'];
    $category = $_POST['category'];
    $is_public = isset($_POST['is_public']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO apps (name, description, url, icon, category, is_public) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $url, $icon, $category, $is_public]);
    header("Location: dashboard");
    exit;
}

// Handle Delete App
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM apps WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard");
    exit;
}

$apps = $pdo->query("SELECT * FROM apps ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
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
        .grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .card { background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); height: fit-content; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9f9f9; }
        .btn-small { padding: 5px 10px; font-size: 0.8rem; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        
        @media (max-width: 1024px) {
            .grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .admin-layout { flex-direction: column; }
            .sidebar { width: 100%; padding: 10px 0; }
        }
        
        /* Icon Selector Styles */
        .icon-selector { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 8px; }
        .icon-option input { display: none; }
        .icon-option span {
            padding: 10px; border: 1px solid var(--border-color, #ddd); border-radius: 6px; cursor: pointer;
            transition: all 0.2s; background: #f9f9f9; color: #666; display: inline-flex; align-items: center; justify-content: center;
        }
        .icon-option input:checked + span {
            border-color: var(--primary-color, #096D7F); background: var(--primary-color, #096D7F); color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
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
                <li><a href="dashboard" class="active"><span class="material-icons">dashboard</span> Gerenciar Apps</a></li>
                <li><a href="/" target="_blank"><span class="material-icons">launch</span> Ver Site / Cadastro</a></li>
                <li><a href="perfil"><span class="material-icons">person</span> Minha Conta</a></li>
                <li style="margin-top: 20px;"><a href="../api/logout.php" style="color: #e74c3c;"><span class="material-icons">logout</span> Sair</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="grid">
                <div class="card">
                    <h3>Adicionar Novo App</h3>
                    <form method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Nome do App</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <input type="text" name="description" required>
                        </div>
                        <div class="form-group">
                            <label>URL (Link do App)</label>
                            <input type="url" name="url" required>
                        </div>
                        <div class="form-group">
                            <label>Ícone</label>
                            <div class="icon-selector">
                                <label class="icon-option" title="Código"><input type="radio" name="icon" value="code" required><span class="material-icons">code</span></label>
                                <label class="icon-option" title="Terminal"><input type="radio" name="icon" value="terminal"><span class="material-icons">terminal</span></label>
                                <label class="icon-option" title="Vídeo"><input type="radio" name="icon" value="videocam"><span class="material-icons">videocam</span></label>
                                <label class="icon-option" title="Filme"><input type="radio" name="icon" value="movie"><span class="material-icons">movie</span></label>
                                <label class="icon-option" title="Imagem"><input type="radio" name="icon" value="image"><span class="material-icons">image</span></label>
                                <label class="icon-option" title="Paleta de Cores"><input type="radio" name="icon" value="palette"><span class="material-icons">palette</span></label>
                                <label class="icon-option" title="PDF"><input type="radio" name="icon" value="picture_as_pdf"><span class="material-icons">picture_as_pdf</span></label>
                                <label class="icon-option" title="Documento"><input type="radio" name="icon" value="description"><span class="material-icons">description</span></label>
                                <label class="icon-option" title="Extensão"><input type="radio" name="icon" value="extension"><span class="material-icons">extension</span></label>
                                <label class="icon-option" title="Apps"><input type="radio" name="icon" value="apps"><span class="material-icons">apps</span></label>
                                <label class="icon-option" title="Ferramentas"><input type="radio" name="icon" value="build"><span class="material-icons">build</span></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Categoria</label>
                            <select name="category" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem; color: var(--text-dark); background: #f9f9f9;">
                                <option value="Code">Code</option>
                                <option value="Video">Video</option>
                                <option value="Imagem">Imagem</option>
                                <option value="PDF">PDF</option>
                                <option value="App">App</option>
                            </select>
                        </div>
                        <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                            <input type="checkbox" name="is_public" id="is_public" value="1" style="width:auto;">
                            <label for="is_public" style="margin:0;">É Público (Acesso Gratuito)</label>
                        </div>
                        <button type="submit" class="btn btn-block">Adicionar App</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Gerenciar Apps</h3>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ícone</th>
                                    <th>Nome</th>
                                    <th>Acesso</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apps as $app): ?>
                                <tr>
                                    <td><?php echo $app['id']; ?></td>
                                    <td><span class="material-icons"><?php echo htmlspecialchars($app['icon']); ?></span></td>
                                    <td><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td><?php echo $app['is_public'] ? 'Público' : 'Premium'; ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $app['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
