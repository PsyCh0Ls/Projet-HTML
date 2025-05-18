<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/cart_functions.php';

// Vérifier si l'utilisateur est connecté
if (!is_authenticated()) {
    header('Location: login.php');
    exit;
}

// Initialiser le panier si nécessaire
initialize_cart();

// Vérifier si un voyage a été spécifié
if (isset($_POST['trip_id'])) {
    $trip_id = (int)$_POST['trip_id'];
    $trip = get_trip_by_id($trip_id);
    
    if ($trip) {
        // Récupérer les options si elles sont présentes
        $options = isset($_POST['options']) ? $_POST['options'] : [];
        
        // Ajouter au panier
        add_to_cart($trip_id, $options);
        
        // Rediriger vers la page précédente ou vers le panier
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'trip_details.php') !== false) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: cart.php');
        }
        exit;
    }
}

// Si on arrive ici, c'est qu'il y a eu un problème
header('Location: search.php');
exit;