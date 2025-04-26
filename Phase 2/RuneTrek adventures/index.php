<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

// 1) Lecture du JSON
$json = read_json(__DIR__ . '/data/trips.json');

// DEBUG
var_dump($json);
exit;

// 2) Vérification d'erreur de parsing
if (json_last_error() !== JSON_ERROR_NONE) {
    // Affiche l'erreur et stoppe proprement
    die('<p>Erreur lors de la lecture des voyages : ' 
        . htmlspecialchars(json_last_error_msg()) 
        . '</p>');
}

// 3) Récupération sécurisée du tableau "trips"
$trips = [];
if (isset($json['trips']) && is_array($json['trips'])) {
    $trips = $json['trips'];
}

// 4) Découpe pour ne prendre que les 3 premiers voyages
$featured_trips = array_slice($trips, 0, 3);

include __DIR__ . '/includes/header.php';
?>
<main>
  <h1>Voyages à la une</h1>
  <div class="trips-grid">
    <?php if (empty($featured_trips)): ?>
      <p>Aucun voyage disponible pour le moment.</p>
    <?php else: ?>
      <?php foreach ($featured_trips as $trip): ?>
        <article class="trip-card">
          <h2><?= htmlspecialchars($trip['title']) ?></h2>
          <p>Durée : <?= intval($trip['duration_days']) ?> jours</p>
          <p>Prix : <?= number_format($trip['total_price'], 2, ',', ' ') ?> €</p>
          <a href="trip_details.php?id=<?= urlencode($trip['id']) ?>">Voir le détail</a>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>

