<?php
session_start();
require_once 'includes/functions.php';
require_admin();

$data = read_json('data/users.json');
$users = $data['users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $action = $_POST['action'] ?? '';
    foreach ($users as &$user) {
        if ($user['id'] == $user_id) {
            if ($action === 'make_vip') {
                $user['role'] = 'vip';
            } elseif ($action === 'ban') {
                $user['role'] = 'banned';
            } elseif ($action === 'unban') {
                $user['role'] = 'normal';
            }
            break;
        }
    }
    write_json('data/users.json', $data);
    header("Location: admin.php");
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5;
$total_users = count($users);
$total_pages = ceil($total_users / $per_page);
$offset = ($page - 1) * $per_page;
$paginated_users = array_slice($users, $offset, $per_page);
?>
<?php include 'includes/header.php'; ?>
<main>
    <div class="admin-container">
        <h2>Administration</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginated_users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['login']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <?php if ($user['role'] === 'normal'): ?>
                                    <button type="submit" name="action" value="make_vip" class="action-button">VIP</button>
                                    <button type="submit" name="action" value="ban" class="action-button">Bannir</button>
                                <?php elseif ($user['role'] === 'banned'): ?>
                                    <button type="submit" name="action" value="unban" class="action-button">Débannir</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            </div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>