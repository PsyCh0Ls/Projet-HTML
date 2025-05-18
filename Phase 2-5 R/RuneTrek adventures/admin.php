<?php
session_start();
require_once 'includes/functions.php';

if (!is_authenticated() || !is_admin()) {
    header('Location: index.php');
    exit;
}

// Gestion des voyages
$data = read_json('data/trips.json');
$trips = isset($data['trips']) && is_array($data['trips']) ? $data['trips'] : [];

// Gestion des utilisateurs
$users_data = read_json('data/users.json');
$users = isset($users_data['users']) && is_array($users_data['users']) ? $users_data['users'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Gestion des voyages
        if ($_POST['action'] === 'add_trip') {
            $new_trip = [
                'id' => count($trips) + 1,
                'title' => isset($_POST['title']) ? $_POST['title'] : '',
                'region' => isset($_POST['region']) ? $_POST['region'] : '',
                'description' => isset($_POST['description']) ? $_POST['description'] : '',
                'start_date' => isset($_POST['start_date']) ? $_POST['start_date'] : '',
                'duration' => (int)(isset($_POST['duration']) ? $_POST['duration'] : 0),
                'price' => (int)(isset($_POST['price']) ? $_POST['price'] : 0)
            ];
            $trips[] = $new_trip;
            $data['trips'] = $trips;
            write_json('data/trips.json', $data);
        } elseif ($_POST['action'] === 'delete_trip' && isset($_POST['trip_id'])) {
            $trip_id = (int)$_POST['trip_id'];
            $trips = array_filter($trips, function($trip) use ($trip_id) {
                return $trip['id'] != $trip_id;
            });
            $trips = array_values($trips);
            $data['trips'] = $trips;
            write_json('data/trips.json', $data);
        } elseif ($_POST['action'] === 'edit_trip' && isset($_POST['trip_id'])) {
            $trip_id = (int)$_POST['trip_id'];
            foreach ($trips as &$trip) {
                if ($trip['id'] == $trip_id) {
                    $trip['title'] = isset($_POST['title']) ? $_POST['title'] : $trip['title'];
                    $trip['region'] = isset($_POST['region']) ? $_POST['region'] : $trip['region'];
                    $trip['description'] = isset($_POST['description']) ? $_POST['description'] : $trip['description'];
                    $trip['start_date'] = isset($_POST['start_date']) ? $_POST['start_date'] : $trip['start_date'];
                    $trip['duration'] = (int)(isset($_POST['duration']) ? $_POST['duration'] : $trip['duration']);
                    $trip['price'] = (int)(isset($_POST['price']) ? $_POST['price'] : $trip['price']);
                    break;
                }
            }
            $data['trips'] = $trips;
            write_json('data/trips.json', $data);
        }
        // Gestion des utilisateurs
        elseif ($_POST['action'] === 'delete_user' && isset($_POST['user_id'])) {
            $user_id = (int)$_POST['user_id'];
            // Ne pas permettre à un admin de se supprimer lui-même
            if ($user_id == $_SESSION['user_id']) {
                $error = "Vous ne pouvez pas supprimer votre propre compte.";
            } else {
                $users = array_filter($users, function($user) use ($user_id) {
                    return $user['id'] != $user_id;
                });
                $users = array_values($users);
                $users_data['users'] = $users;
                write_json('data/users.json', $users_data);
            }
        } elseif ($_POST['action'] === 'change_role' && isset($_POST['user_id']) && isset($_POST['role'])) {
            $user_id = (int)$_POST['user_id'];
            $new_role = $_POST['role'] === 'admin' ? 'admin' : 'normal';
            // Ne pas permettre à un admin de modifier son propre rôle
            if ($user_id == $_SESSION['user_id']) {
                $error = "Vous ne pouvez pas modifier votre propre rôle.";
            } else {
                foreach ($users as &$u) {
                    if ($u['id'] == $user_id) {
                        $u['role'] = $new_role;
                        break;
                    }
                }
                $users_data['users'] = $users;
                write_json('data/users.json', $users_data);
            }
        }
        header('Location: admin.php');
        exit;
    }
}

// Si on édite un voyage, récupérer ses données
$edit_trip = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $trip_id = (int)$_GET['id'];
    foreach ($trips as $trip) {
        if ($trip['id'] == $trip_id) {
            $edit_trip = $trip;
            break;
        }
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="admin-page">
    <main>
        <h1>Panel Admin</h1>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <section class="manage-trips">
            <h2><?php echo $edit_trip ? 'Modifier le voyage' : 'Ajouter un voyage'; ?></h2>
            <form method="POST">
                <input type="hidden" name="action" value="<?php echo $edit_trip ? 'edit_trip' : 'add_trip'; ?>">
                <?php if ($edit_trip): ?>
                    <input type="hidden" name="trip_id" value="<?php echo $edit_trip['id']; ?>">
                <?php endif; ?>
                <div>
                    <label>Titre:</label>
                    <input type="text" name="title" value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['title']) : ''; ?>" required>
                </div>
                <div>
                    <label>Région:</label>
                    <input type="text" name="region" value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['region']) : ''; ?>" required>
                </div>
                <div>
                    <label>Description:</label>
                    <textarea name="description" required><?php echo $edit_trip ? htmlspecialchars($edit_trip['description']) : ''; ?></textarea>
                </div>
                <div>
                    <label>Date de départ:</label>
                    <input type="date" name="start_date" value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['start_date']) : ''; ?>" required>
                </div>
                <div>
                    <label>Durée (jours):</label>
                    <input type="number" name="duration" min="1" value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['duration']) : ''; ?>" required>
                </div>
                <div>
                    <label>Prix (PO):</label>
                    <input type="number" name="price" min="0" value="<?php echo $edit_trip ? htmlspecialchars($edit_trip['price']) : ''; ?>" required>
                </div>
                <button type="submit"><?php echo $edit_trip ? 'Modifier' : 'Ajouter'; ?> le voyage</button>
            </form>
            <h3>Liste des voyages</h3>
            <ul>
                <?php if (empty($trips)): ?>
                    <li>Aucun voyage disponible.</li>
                <?php else: ?>
                    <?php foreach ($trips as $trip): ?>
                        <li>
                            <?php echo htmlspecialchars($trip['title'] ?? ''); ?> - <?php echo htmlspecialchars($trip['region'] ?? ''); ?>
                            (<?php echo htmlspecialchars($trip['start_date'] ?? ''); ?>, <?php echo htmlspecialchars($trip['duration'] ?? ''); ?> jours, <?php echo htmlspecialchars($trip['price'] ?? ''); ?> PO)
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_trip">
                                <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
                                <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer ce voyage ?');">Supprimer</button>
                            </form>
                            <a href="admin.php?action=edit&id=<?php echo $trip['id']; ?>">Modifier</a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>
        <section class="manage-users">
            <h2>Gérer les utilisateurs</h2>
            <h3>Liste des utilisateurs</h3>
            <ul>
                <?php if (empty($users)): ?>
                    <li>Aucun utilisateur disponible.</li>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <li>
                            <?php echo htmlspecialchars($user['login'] ?? ''); ?> (<?php echo htmlspecialchars($user['email'] ?? ''); ?>) - Rôle: <?php echo htmlspecialchars($user['role'] ?? ''); ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">Supprimer</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="change_role">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role">
                                    <option value="normal" <?php echo ($user['role'] ?? '') === 'normal' ? 'selected' : ''; ?>>Utilisateur</option>
                                    <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit">Changer le rôle</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>
    </main>
</div>
<?php include 'includes/footer.php'; ?>