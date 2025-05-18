// Script to toggle password visibility on forms
function addPasswordToggle(passwordInputId) {
    const passwordInput = document.getElementById(passwordInputId);
    if (!passwordInput) return;

    // Style parent as relative for absolute positioning
    passwordInput.style.paddingRight = '36px';
    passwordInput.parentNode.style.position = 'relative';

    // Create toggle button (eye icon)
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'password-toggle-btn';
    toggleBtn.setAttribute('aria-label', 'Afficher/Cacher le mot de passe');
    toggleBtn.innerHTML = `
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" 
            stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <ellipse cx="12" cy="12" rx="9" ry="5"/>
            <circle cx="12" cy="12" r="2"/>
        </svg>
    `;
    // Style the button
    toggleBtn.style.position = 'absolute';
    toggleBtn.style.top = '75%';
    toggleBtn.style.right = '8px';
    toggleBtn.style.transform = 'translateY(-50%)';
    toggleBtn.style.background = 'none';
    toggleBtn.style.border = 'none';
    toggleBtn.style.cursor = 'pointer';
    toggleBtn.style.padding = '0';
    toggleBtn.style.display = 'flex';
    toggleBtn.style.alignItems = 'center';

    let isVisible = false;
    toggleBtn.onclick = function (e) {
        e.preventDefault();
        isVisible = !isVisible;
        passwordInput.type = isVisible ? 'text' : 'password';
        toggleBtn.innerHTML = isVisible
            ? `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" 
                stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <ellipse cx="12" cy="12" rx="9" ry="5"/>
                <circle cx="12" cy="12" r="2"/>
                <line x1="4" y1="20" x2="20" y2="4" stroke="#555" stroke-width="2"/>
            </svg>`
            : `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" 
                stroke="#555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <ellipse cx="12" cy="12" rx="9" ry="5"/>
                <circle cx="12" cy="12" r="2"/>
            </svg>`;
    };
    // Insert button absolutely inside parent
    passwordInput.parentNode.appendChild(toggleBtn);
}

document.addEventListener('DOMContentLoaded', function () {
    addPasswordToggle('password');
    addPasswordToggle('confirm_password'); // Pour le champ de confirmation si présent
});