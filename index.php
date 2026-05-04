<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto7 App Hub</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <div class="logo">
            <a href="/" style="display: flex; align-items: center;">
                <img src="logo.png" alt="7Apps Logo" style="height: 40px; object-fit: contain;">
            </a>
        </div>
        <nav class="nav-links">
            <a href="#" id="navAdminBtn" class="hidden nav-item" data-i18n="nav_admin"><span class="material-icons">admin_panel_settings</span> Admin</a>
            <div class="header-right">
                <div class="lang-selector">
                    <span class="material-icons">language</span>
                    <select id="langSelect">
                        <option value="pt-br">PT</option>
                        <option value="en">EN</option>
                        <option value="es">ES</option>
                    </select>
                </div>
                <div class="auth-buttons">
                    <a href="#" id="navLoginBtn" data-i18n="nav_login">Entrar</a>
                    <a href="#" id="navRegisterBtn" data-i18n="nav_register">Cadastrar</a>
                    
                    <div id="userInfo" class="hidden user-info">
                        <span class="material-icons">account_circle</span>
                        <span id="userNameDisplay"></span>
                    </div>
                    <a href="#" id="navLogoutBtn" class="hidden" data-i18n="nav_logout">Sair</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h5 data-i18n="hero_title">Ajudando no Seu Dia a Dia</h5>
            <!-- Removed the subtitle paragraph to keep it clean, as requested -->
        </section>

        <section class="container">
            <div class="app-grid" id="appGrid">
                <!-- Dynamically loaded -->
            </div>
        </section>

        <section id="appViewerContainer" class="hidden">
            <div style="background: var(--bg-color); padding: 10px 5%; display: flex; align-items: center; border-bottom: 1px solid var(--border-color);">
                <button id="closeAppViewerBtn"><span class="material-icons" style="font-size: 15px;">arrow_back</span> <span data-i18n="btn_back">Voltar</span></button>
                <span id="viewerAppName" style="font-weight: 500; font-size: 0.95rem; color: var(--text-dark);"></span>
            </div>
            <iframe id="appIframe"></iframe>
        </section>
    </main>

    <!-- Login Modal -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-content">
            <span class="material-icons modal-close">close</span>
            <h3 class="modal-title" data-i18n="login_title">Faça seu Login</h3>
            <form id="loginForm">
                <div class="form-group">
                    <label data-i18n="email">E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label data-i18n="password">Senha</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-block" data-i18n="nav_login">Entrar</button>
            </form>
            <div class="form-switch">
                <span data-i18n="register_prompt">Não tem uma conta?</span> <a id="switchToRegister" data-i18n="nav_register">Cadastre-se</a>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal-overlay" id="registerModal">
        <div class="modal-content">
            <span class="material-icons modal-close">close</span>
            <h3 class="modal-title" data-i18n="register_title">Crie sua Conta</h3>
            <form id="registerForm">
                <div class="form-group">
                    <label data-i18n="name">Nome</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label data-i18n="email">E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label data-i18n="password">Senha</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-block" data-i18n="nav_register">Cadastrar</button>
            </form>
            <div class="form-switch">
                <span data-i18n="login_prompt">Já tem uma conta?</span> <a id="switchToLogin" data-i18n="nav_login">Faça login</a>
            </div>
        </div>
    </div>

    <script src="assets/js/lang.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
