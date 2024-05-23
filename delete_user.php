<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid request');
}

$user_id = intval($_GET['id']);

// Pastikan admin tidak menghapus dirinya sendiri
if ($user_id == $_SESSION['user_id']) {
    die('You cannot delete yourself.');
}

$delete_successful = false;

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    $delete_successful = true;
} else {
    echo "<p>Error: " . $stmt->error . "</p>";
}
?>

<?php if ($delete_successful): ?>
<script>
    alert('User deleted successfully');
    window.location.href = 'manage_users.php';
</script>
<?php else: ?>
<script>
    alert('Error deleting user');
    window.location.href = 'manage_users.php';
</script>
<?php endif; ?>
