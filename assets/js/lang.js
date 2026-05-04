const LangManager = {
    currentLang: localStorage.getItem('appLang') || 'pt-br',
    translations: {},

    init() {
        this.loadLang(this.currentLang);
        document.getElementById('langSelect').value = this.currentLang;
        document.getElementById('langSelect').addEventListener('change', (e) => {
            this.setLang(e.target.value);
        });
    },

    setLang(lang) {
        this.currentLang = lang;
        localStorage.setItem('appLang', lang);
        this.loadLang(lang);
    },

    async loadLang(lang) {
        try {
            const res = await fetch(`lang/${lang}.json`);
            if (res.ok) {
                this.translations = await res.json();
                this.updateDOM();
            }
        } catch (e) {
            console.error('Failed to load language', e);
        }
    },

    updateDOM() {
        const elements = document.querySelectorAll('[data-i18n]');
        elements.forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (this.translations[key]) {
                if (el.tagName === 'INPUT' && el.getAttribute('placeholder') !== null) {
                    el.setAttribute('placeholder', this.translations[key]);
                } else {
                    el.textContent = this.translations[key];
                }
            }
        });
    },
    
    get(key) {
        return this.translations[key] || key;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    LangManager.init();
});
