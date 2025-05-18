document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur la page de recherche
    const searchPage = document.querySelector('.search-page');
    if (!searchPage) return;
    
    // Récupérer le conteneur des résultats
    const resultsContainer = document.querySelector('.trip-cards');
    if (!resultsContainer) return;
    
    // Récupérer tous les voyages affichés
    const tripCards = Array.from(resultsContainer.querySelectorAll('.trip-card'));
    if (tripCards.length === 0) return;
    
    // Créer l'interface de tri
    const sortingOptions = document.createElement('div');
    sortingOptions.className = 'sorting-options';
    sortingOptions.innerHTML = `
        <label for="sort-by">Trier par:</label>
        <select id="sort-by">
            <option value="default">Par défaut</option>
            <option value="price-asc">Prix croissant</option>
            <option value="price-desc">Prix décroissant</option>
            <option value="name-asc">Nom A-Z</option>
            <option value="name-desc">Nom Z-A</option>
            <option value="date-asc">Date de départ (plus proche)</option>
            <option value="date-desc">Date de départ (plus lointaine)</option>
        </select>
    `;
    
    // Ajouter du style pour les options de tri
    const style = document.createElement('style');
    style.textContent = `
        .sorting-options {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sorting-options select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .highlight {
            animation: highlight 1s ease-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(255, 215, 0, 0.3); }
            100% { background-color: transparent; }
        }
    `;
    document.head.appendChild(style);
    
    // Insérer les options de tri avant les résultats
    const resultsHeader = searchPage.querySelector('.results h2');
    resultsHeader.after(sortingOptions);
    
    // Fonction pour récupérer les valeurs à comparer
    function extractValue(card, key) {
        switch(key) {
            case 'price':
                const priceText = card.querySelector('.price').textContent;
                return parseInt(priceText.match(/\d+/)[0], 10);
            case 'name':
                return card.querySelector('h3').textContent.trim();
            case 'date':
                // Extrait la date si disponible, sinon retourne une date lointaine
                const dateMatch = card.textContent.match(/Date de départ:\s*([0-9]{4}-[0-9]{2}-[0-9]{2})/);
                if (dateMatch) {
                    return new Date(dateMatch[1]);
                }
                return new Date('2999-12-31');
            default:
                return 0;
        }
    }
    
    // Écouter les changements de tri
    document.getElementById('sort-by').addEventListener('change', function() {
        const sortValue = this.value;
        
        // Trier les cartes
        const sortedCards = [...tripCards].sort((a, b) => {
            switch(sortValue) {
                case 'price-asc':
                    return extractValue(a, 'price') - extractValue(b, 'price');
                case 'price-desc':
                    return extractValue(b, 'price') - extractValue(a, 'price');
                case 'name-asc':
                    return extractValue(a, 'name').localeCompare(extractValue(b, 'name'));
                case 'name-desc':
                    return extractValue(b, 'name').localeCompare(extractValue(a, 'name'));
                case 'date-asc':
                    return extractValue(a, 'date') - extractValue(b, 'date');
                case 'date-desc':
                    return extractValue(b, 'date') - extractValue(a, 'date');
                default:
                    return 0;
            }
        });
        
        // Vider et recréer le conteneur des résultats
        resultsContainer.innerHTML = '';
        
        // Ajouter les cartes triées
        sortedCards.forEach(card => {
            resultsContainer.appendChild(card);
            card.classList.add('highlight');
            setTimeout(() => {
                card.classList.remove('highlight');
            }, 1000);
        });
    });
});