<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/cart_functions.php';

// Définir le header pour le type de contenu JSON
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

// Initialiser le panier si nécessaire
initialize_cart();

// Renvoyer le nombre d'articles dans le panier
echo json_encode(['count' => $_SESSION['cart']['count']]);
?>