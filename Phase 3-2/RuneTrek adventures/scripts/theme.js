// Fonction pour appliquer un thème
function setTheme(theme) {
    const themeLink = document.getElementById('theme-style');
    if (themeLink) {
        themeLink.href = theme === 'dark' ? 'styles/darkmode.css' : 'styles/runeTrek adventures.css';
        document.cookie = `theme=${theme};path=/;max-age=31536000`; // Cookie valide 1 an
    } else {
        console.error('Balise theme-style introuvable');
    }
}

// Fonction pour récupérer le thème depuis les cookies
function getTheme() {
    const cookies = document.cookie.split(';').reduce((acc, cookie) => {
        const [key, value] = cookie.trim().split('=');
        acc[key] = value;
        return acc;
    }, {});
    return cookies.theme || 'light'; // Par défaut : mode clair
}

// Au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    // Appliquer le thème sauvegardé
    setTheme(getTheme());

    // Ajouter un écouteur sur le bouton de bascule
    const toggleButton = document.getElementById('theme-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', () => {
            const currentTheme = getTheme();
            setTheme(currentTheme === 'light' ? 'dark' : 'light');
        });
    } else {
        console.error('Bouton theme-toggle introuvable');
    }
});