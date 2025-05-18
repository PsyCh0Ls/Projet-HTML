// Préchargement du thème pour éviter que ca clignotte de partout
(function() {
    function isDarkMode() {
        return document.cookie.split(';').some(cookie => {
            const trimmed = cookie.trim();
            return trimmed.startsWith('theme=dark');
        });
    }
    
    if (isDarkMode()) {
        // Ajout style inline pour un chargement instantané
        document.write(`
            <style id="preload-dark-style">
                /* Styles de base */
                html, body {
                    background-color: #121212 !important;
                    color: #e0e0e0 !important;
                    transition: none !important;
                }
                /* Navigation et header */
                .main-header {
                    background-color: #0d47a1 !important;
                }
                .main-nav a {
                    color: #fff !important;
                }
                /* Boutons spécifiques */
                .cta-button, .register-button, a.cta-button, a[href="register.php"].cta-button {
                    background-color: #1976D2 !important;
                    color: white !important;
                }
                /* Footer */
                .main-footer {
                    background-color: #1a1a1a !important;
                }
                /* Assurer que le texte dans les zones sombres reste lisible */
                .main-header a, .main-footer a {
                    color: #e0e0e0 !important;
                }
            </style>
        `);
        document.documentElement.classList.add('dark-mode-preload');
    }
})();
