# Documentação Técnica - Projeto7 Hub App SaaS

Este documento fornece uma visão geral técnica do sistema, arquitetura, tecnologias e estrutura de dados para futuras manutenções.

## 1. Visão Geral
O **Projeto7 Hub** é uma plataforma SaaS (Software as a Service) centralizadora de aplicativos. Ele permite que um administrador gerencie um catálogo de ferramentas (apps), controlando quem pode acessá-los com base em login e níveis de acesso.

## 2. Stack Tecnológica
*   **Linguagem Backend:** PHP 8.1+
*   **Banco de Dados:** MySQL (compatível com MariaDB)
*   **Frontend:** HTML5, CSS3 (Vanilla) e JavaScript (Vanilla ES6+)
*   **Servidor Web:** Apache (configurado via `.htaccess`)
*   **Versionamento:** Git / GitHub
*   **Deploy:** CI/CD via GitHub Webhooks direto para Hostinger

## 3. Arquitetura de Pastas
```text
/
├── admin/            # Painel Administrativo (Dashboard, Gestão de Usuários e Apps)
├── api/              # Endpoints PHP para comunicação com o Frontend (Login, Token, Apps)
├── assets/
│   ├── css/          # Folhas de estilo (Design System, Layout responsivo)
│   └── js/           # Lógica do lado do cliente (Lidando com Auth e renderização dinâmica)
├── lang/             # Arquivos JSON de internacionalização (PT-BR, EN, ES)
├── .htaccess         # Regras de reescrita de URL (Remove .php das URLs)
├── database.sql      # Script de criação das tabelas do banco de dados
└── router.php        # Roteador para o servidor de desenvolvimento local do PHP
```

## 4. Banco de Dados (Schema)
O sistema utiliza um banco de dados relacional com as seguintes tabelas principais:

### Tabela: `users`
Armazena todos os usuários do sistema, incluindo administradores.
*   `id`: Identificador único (Primary Key).
*   `name`: Nome completo.
*   `email`: E-mail (Unique).
*   `password`: Hash da senha (Bcrypt).
*   `plan`: Nível do plano (`free`, `premium`).
*   `is_admin`: Booleano (1 para Admin, 0 para Usuário Comum).
*   `created_at`: Data de cadastro.

### Tabela: `apps`
Armazena o catálogo de aplicativos disponíveis no Hub.
*   `id`: Identificador único.
*   `name`: Nome do aplicativo.
*   `description`: Descrição breve.
*   `url`: Link real do aplicativo (protegido pelo backend).
*   `icon`: Nome do ícone (Material Icons).
*   `category`: Categoria (Code, Video, Imagem, etc).
*   `is_public`: Define se o app é gratuito ou exige plano premium.

### Tabela: `user_sessions`
(Opcional/Futuro) Utilizada para controle de tokens e expiração de sessões.

## 5. Fluxo de Segurança e Acesso
1.  **Exibição:** O `index.php` consome a `api/apps.php`. Se o usuário não estiver logado, a API oculta as URLs reais e o Frontend aplica uma máscara visual de "bloqueado" (grayscale + cadeado).
2.  **Autenticação:** O login é processado em `api/login.php`, que gera uma sessão e armazena os dados básicos no `localStorage` do navegador para persistência.
3.  **Acesso aos Apps:** Quando um usuário clica em um app, o sistema verifica via `api/token.php` se o usuário está logado e tem permissão. Somente após a validação, a URL real é revelada dentro de um `iframe` seguro.

## 6. Pipeline de Desenvolvimento
*   **Local:** Desenvolvimento feito com servidor embutido do PHP (`php -S localhost:8000 router.php`).
*   **GitHub:** Versionamento e backup do código.
*   **Hostinger:** Produção. A cada `git push` no GitHub, a Hostinger puxa os arquivos automaticamente via Webhook.

## 7. Manutenção Futura
*   **Adicionar Campos:** Caso precise adicionar novas colunas (ex: foto de perfil), use o phpMyAdmin da Hostinger e atualize o `database.sql` local.
*   **Novas Linguagens:** Para adicionar um novo idioma, basta criar o arquivo `.json` correspondente na pasta `/lang` e adicioná-lo ao `langSelect` no `index.php`.

---
*Documentação gerada em 03/05/2026.*
