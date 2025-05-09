<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$users = read_json('data/users.json');
$user = null;
foreach ($users['users'] as &$u) {
    if ($u['id'] == $_SESSION['user_id']) {
        $user = &$u;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Veuillez remplir tous les champs avec un email valide.';
    } else {
        $user['name'] = $name;
        $user['email'] = $email;
        write_json('data/users.json', $users);
        header('Location: profile.php');
        exit;
    }
}
?>
<?php include 'includes/header.php'; ?>
<div class="edit-profile">
    <h2>Modifier mon profil</h2>
    <form method="POST">
        <div>
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <button type="submit">Enregistrer</button>
    </form>
</div>
<?php include 'includes/footer.php'; ?>