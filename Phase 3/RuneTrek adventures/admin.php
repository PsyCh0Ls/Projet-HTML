<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$trips = read_json('data/trips.json');
$users = read_json('data/users.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $new_trip = [
            'id' => count($trips['trips']) + 1,
            'title' => $_POST['title'],
            'region' => $_POST['region'],
            'description' => $_POST['description'],
            'date' => $_POST['date'],
            'duration' => (int)$_POST['duration'],
            'base_price' => (float)$_POST['base_price'],
            'guide_price' => 50,
            'mount_price' => 30
        ];
        $trips['trips'][] = $new_trip;
        write_json('data/trips.json', $trips);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        foreach ($trips['trips'] as &$trip) {
            if ($trip['id'] == $_POST['id']) {
                $trip['title'] = $_POST['title'];
                $trip['region'] = $_POST['region'];
                $trip['description'] = $_POST['description'];
                $trip['date'] = $_POST['date'];
                $trip['duration'] = (int)$_POST['duration'];
                $trip['base_price'] = (float)$_POST['base_price'];
                break;
            }
        }
        write_json('data/trips.json', $trips);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $trips['trips'] = array_filter($trips['trips'], fn($trip) => $trip['id'] != $_POST['id']);
        write_json('data/trips.json', $trips);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'change_role') {
        foreach ($users['users'] as &$user) {
            if ($user['id'] == $_POST['user_id']) {
                $user['role'] = $_POST['role'];
                break;
            }
        }
        write_json('data/users.json', $users);
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="admin">
    <h2>Panel Admin</h2>
    <h3>Gérer les voyages</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Région</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trips['trips'] as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['id']); ?></td>
                    <td><?php echo htmlspecialchars($trip['title']); ?></td>
                    <td><?php echo htmlspecialchars($trip['region']); ?></td>
                    <td>
                        <button onclick="editTrip(<?php echo json_encode($trip); ?>)">Modifier</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $trip['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Ajouter un voyage</h3>
    <form id="admin-form" method="POST" class="admin-form">
        <input type="hidden" name="action" value="add">
        <div><label>Titre :</label><input type="text" name="title" required></div>
        <div><label>Région :</label><input type="text" name="region" required></div>
        <div><label>Description :</label><input type="text" name="description" required></div>
        <div><label>Date :</label><input type="date" name="date" required></div>
        <div><label>Durée (jours) :</label><input type="number" name="duration" required></div>
        <div><label>Prix de base (PO) :</label><input type="number" name="base_price" step="0.01" required></div>
        <button type="submit">Ajouter</button>
    </form>
    <h3>Gérer les utilisateurs</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users['users'] as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['login']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="action" value="change_role">
                            <select name="role" onchange="this.form.submit()">
                                <option value="normal" <?php echo $user['role'] === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
function editTrip(trip) {
    document.querySelector('input[name="title"]').value = trip.title;
    document.querySelector('input[name="region"]').value = trip.region;
    document.querySelector('input[name="description"]').value = trip.description;
    document.querySelector('input[name="date"]').value = trip.date;
    document.querySelector('input[name="duration"]').value = trip.duration;
    document.querySelector('input[name="base_price"]').value = trip.base_price;
    document.querySelector('input[name="action"]').value = 'update';
    document.querySelector('input[name="id"]').value = trip.id;
}
</script>
<?php include 'includes/footer.php'; ?>