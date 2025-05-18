document.addEventListener('DOMContentLoaded', function() {
    // Créer le bouton de thème sans modifier la mise en page
    function createThemeButton() {
        // Vérifier si le bouton existe déjà
        if (document.getElementById('theme-switcher')) return;
        
        // Créer le bouton
        const themeButton = document.createElement('button');
        themeButton.id = 'theme-switcher';
        themeButton.className = 'theme-button';
        themeButton.innerHTML = getCookie('theme') === 'dark' ? '☀️' : '🌙';
        themeButton.title = getCookie('theme') === 'dark' ? 'Mode clair' : 'Mode sombre';
        
        // Ajouter le style pour le bouton uniquement
        const styleElement = document.createElement('style');
        styleElement.textContent = `
            .theme-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                border-radius: 50%;
                background-color: rgba(30, 136, 229, 0.8);
                color: white;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                z-index: 999;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
                transition: all 0.3s ease;
            }
            .theme-button:hover {
                background-color: rgba(30, 136, 229, 1);
                transform: scale(1.05);
            }
            .theme-button:active {
                transform: scale(0.95);
            }
        `;
        document.head.appendChild(styleElement);
        
        // Ajouter au DOM sans modifier la structure existante
        document.body.appendChild(themeButton);
        
        // Ajouter l'écouteur d'événement
        themeButton.addEventListener('click', toggleTheme);
        
        // Appliquer le thème actuel au chargement
        if (getCookie('theme') === 'dark') {
            loadDarkStylesheet();
            // Supprimer la classe de préchargement une fois le CSS chargé
            setTimeout(() => {
                const preloadStyle = document.getElementById('preload-dark-style');
                if (preloadStyle) preloadStyle.remove();
            }, 100);
        } else {
            // S'assurer que le mode sombre est complètement désactivé
            removeDarkStylesheet();
            document.documentElement.classList.remove('dark-mode-preload');
            const preloadStyle = document.getElementById('preload-dark-style');
            if (preloadStyle) preloadStyle.remove();
        }
    }
    
    // Fonction pour récupérer un cookie
    function getCookie(name) {
        const cookies = document.cookie.split(';');
        for (let i = 0; i < cookies.length; i++) {
            let cookie = cookies[i].trim();
            if (cookie.startsWith(name + '=')) {
                return cookie.substring(name.length + 1);
            }
        }
        return '';
    }
    
    // Fonction pour définir un cookie
    function setCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
    }
    
    // Fonction pour basculer entre les thèmes
    function toggleTheme() {
        const currentTheme = getCookie('theme') === 'dark' ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        setCookie('theme', newTheme, 365); // Cookie conservé 1 an
        
        if (newTheme === 'dark') {
            loadDarkStylesheet();
            document.getElementById('theme-switcher').innerHTML = '☀️';
            document.getElementById('theme-switcher').title = 'Mode clair';
        } else {
            // Amélioration du passage au mode clair
            const darkStylesheet = document.getElementById('dark-theme-stylesheet');
            if (darkStylesheet) {
                // Supprimer avec un petit délai pour éviter le clignotement
                darkStylesheet.disabled = true;
                setTimeout(() => darkStylesheet.remove(), 50);
            }
            
            // Nettoyer toutes les classes liées au mode sombre
            document.documentElement.classList.remove('dark-mode', 'dark-mode-preload');
            document.body.classList.remove('dark-mode', 'dark-mode-preload');
            
            // Supprimer le style de préchargement
            const preloadStyle = document.getElementById('preload-dark-style');
            if (preloadStyle) preloadStyle.remove();
            
            // Mettre à jour le bouton
            document.getElementById('theme-switcher').innerHTML = '🌙';
            document.getElementById('theme-switcher').title = 'Mode sombre';
        }
    }
    
    // Fonction pour charger la feuille de style du mode sombre
    function loadDarkStylesheet() {
        document.documentElement.classList.add('dark-mode');
        document.body.classList.add('dark-mode');
        
        if (!document.getElementById('dark-theme-stylesheet')) {
            const link = document.createElement('link');
            link.id = 'dark-theme-stylesheet';
            link.rel = 'stylesheet';
            link.href = 'styles/dark-mode.css';
            document.head.appendChild(link);
        }
    }
    
    // Fonction pour supprimer la feuille de style du mode sombre
    function removeDarkStylesheet() {
        document.documentElement.classList.remove('dark-mode');
        document.body.classList.remove('dark-mode');
        
        const darkStylesheet = document.getElementById('dark-theme-stylesheet');
        if (darkStylesheet) {
            darkStylesheet.remove();
        }
    }
    
    // Initialiser le bouton de thème
    createThemeButton();
});