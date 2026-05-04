const AppManager = {
    user: JSON.parse(localStorage.getItem('appUser')) || null,

    init() {
        this.updateNav();
        this.loadApps();
        this.bindEvents();
    },

    bindEvents() {
        // Modals
        document.getElementById('navLoginBtn').addEventListener('click', () => this.openModal('loginModal'));
        document.getElementById('navRegisterBtn').addEventListener('click', () => this.openModal('registerModal'));
        document.getElementById('navLogoutBtn').addEventListener('click', () => this.logout());
        
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.modal-overlay').classList.remove('active');
            });
        });

        document.getElementById('switchToRegister').addEventListener('click', () => {
            this.closeModal('loginModal');
            this.openModal('registerModal');
        });
        document.getElementById('switchToLogin').addEventListener('click', () => {
            this.closeModal('registerModal');
            this.openModal('loginModal');
        });

        document.getElementById('closeAppViewerBtn')?.addEventListener('click', () => {
            document.getElementById('appViewerContainer').classList.add('hidden');
            document.getElementById('appIframe').src = '';
            document.querySelector('.hero').classList.remove('hidden');
            document.querySelector('.container').classList.remove('hidden');
        });

        // Forms
        document.getElementById('loginForm').addEventListener('submit', (e) => this.handleLogin(e));
        document.getElementById('registerForm').addEventListener('submit', (e) => this.handleRegister(e));
    },

    openModal(id) {
        document.getElementById(id).classList.add('active');
    },

    closeModal(id) {
        document.getElementById(id).classList.remove('active');
    },

    updateNav() {
        if (this.user) {
            document.getElementById('navLoginBtn').classList.add('hidden');
            document.getElementById('navRegisterBtn').classList.add('hidden');
            document.getElementById('navLogoutBtn').classList.remove('hidden');
            document.getElementById('userInfo').classList.remove('hidden');
            document.getElementById('userNameDisplay').textContent = this.user.name;
            if(this.user.is_admin) {
                document.getElementById('navAdminBtn').classList.remove('hidden');
            }
        } else {
            document.getElementById('navLoginBtn').classList.remove('hidden');
            document.getElementById('navRegisterBtn').classList.remove('hidden');
            document.getElementById('navLogoutBtn').classList.add('hidden');
            document.getElementById('userInfo').classList.add('hidden');
            document.getElementById('userNameDisplay').textContent = '';
            document.getElementById('navAdminBtn').classList.add('hidden');
        }
    },

    async handleLogin(e) {
        e.preventDefault();
        const email = e.target.email.value;
        const password = e.target.password.value;

        try {
            const res = await fetch('api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();
            if (data.success) {
                this.user = data.user;
                localStorage.setItem('appUser', JSON.stringify(this.user));
                this.closeModal('loginModal');
                this.updateNav();
                this.loadApps();
                e.target.reset();
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
        }
    },

    async handleRegister(e) {
        e.preventDefault();
        const name = e.target.name.value;
        const email = e.target.email.value;
        const password = e.target.password.value;

        try {
            const res = await fetch('api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, password })
            });
            const data = await res.json();
            if (data.success) {
                alert('Registration successful! Please login.');
                this.closeModal('registerModal');
                this.openModal('loginModal');
                e.target.reset();
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
        }
    },

    logout() {
        this.user = null;
        localStorage.removeItem('appUser');
        this.updateNav();
        this.loadApps();
    },

    async loadApps() {
        const headers = {};
        if (this.user && this.user.token) {
            headers['Authorization'] = 'Bearer ' + this.user.token;
        }

        try {
            const res = await fetch('api/apps', { headers });
            const data = await res.json();
            if (data.success) {
                this.renderApps(data.data);
            }
        } catch (err) {
            console.error(err);
        }
    },

    renderApps(apps) {
        const grid = document.getElementById('appGrid');
        grid.innerHTML = '';
        
        // Remove appGrid class to act as a wrapper, as we'll create inner grids
        grid.className = '';

        // Group by category
        const categories = {};
        apps.forEach(app => {
            if (!categories[app.category]) {
                categories[app.category] = [];
            }
            categories[app.category].push(app);
        });

        for (const [categoryName, categoryApps] of Object.entries(categories)) {
            const categoryGroup = document.createElement('div');
            categoryGroup.className = 'category-group';
            
            const title = document.createElement('h2');
            title.className = 'category-title';
            const langKey = 'cat_' + categoryName.toLowerCase();
            title.setAttribute('data-i18n', langKey);
            title.textContent = LangManager.get(langKey) || categoryName;
            categoryGroup.appendChild(title);

            const innerGrid = document.createElement('div');
            innerGrid.className = 'app-grid';

            categoryApps.forEach(app => {
                let premiumBadge = '';
                if (app.is_public == 0) {
                    premiumBadge = `<span class="premium-badge">Premium</span>`;
                }

                const card = document.createElement('a');
                card.className = 'app-item btn-access';
                card.setAttribute('data-appid', app.id);
                card.setAttribute('data-appname', app.name);
                
                card.innerHTML = `
                    <span class="material-icons" style="pointer-events: none;">${app.icon}</span>
                    <span class="app-name" style="pointer-events: none;">${app.name}</span>
                    ${premiumBadge}
                `;
                innerGrid.appendChild(card);
            });

            categoryGroup.appendChild(innerGrid);
            grid.appendChild(categoryGroup);
        }

        // Rebind app access buttons
        document.querySelectorAll('.btn-access').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.target.closest('.btn-access');
                const appId = target.getAttribute('data-appid');
                const appName = target.getAttribute('data-appname');
                if(appId) {
                    this.accessApp(appId, appName);
                }
            });
        });
    },

    async accessApp(appId, appName) {
        if (!this.user) {
            this.openModal('loginModal');
            return;
        }

        try {
            const res = await fetch('api/token', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + this.user.token
                },
                body: JSON.stringify({ app_id: appId, user_id: this.user.id })
            });
            const data = await res.json();
            
            if (data.success) {
                // Show iframe viewer instead of new tab
                document.querySelector('.hero').classList.add('hidden');
                document.querySelector('.container').classList.add('hidden');
                
                document.getElementById('appViewerContainer').classList.remove('hidden');
                document.getElementById('viewerAppName').textContent = appName || 'App';
                document.getElementById('appIframe').src = data.target_url;
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    AppManager.init();
});
