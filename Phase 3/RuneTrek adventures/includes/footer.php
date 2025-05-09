</main>
<footer>
    <p>© 2025 RuneTrek Adventures | Tous droits réservés</p>
</footer>
<script src="scripts/payment.js"></script>
<script src="scripts/admin.js"></script>
<script src="scripts/auth.js"></script>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>