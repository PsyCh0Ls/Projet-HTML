# RuneTrek Adventures

Plateforme de réservation de voyages dans l'univers de Runeterra.

## Prérequis
- PHP 7.4+
- Serveur local (ex. XAMPP, WAMP, ou `php -S localhost:8000`)
- ngrok pour tester l'intégration CY Bank

## Installation
1. Cloner le dépôt ou décompresser le fichier ZIP dans le dossier du serveur (ex. `htdocs` pour XAMPP).
2. Créer un dossier `data` avec les fichiers JSON : `users.json`, `trips.json`, `bookings.json`, `payments.json`.
3. Créer un dossier `logs` pour les journaux (ex. `logs/payments.log`).
4. Vérifier ou créer un dossier `images` avec les fichiers suivants :
   - `runeterra-landscape.jpg`
   - `piltover.jpg`
   - `demacia.jpg`
   - `ionia.jpg`
   - Si les images sont manquantes, utiliser des placeholders (ex. `https://via.placeholder.com/1920x600` pour `runeterra-landscape.jpg`).
5. Assurer que les dossiers `data/` et `logs/` ont des permissions en écriture (ex. `chmod 775 data logs` sur Linux).

## Structure des dossiers