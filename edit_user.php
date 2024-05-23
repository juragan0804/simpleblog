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
$username = '';
$role = '';

$edit_successful = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];

    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $user_id);

    if ($stmt->execute()) {
        $edit_successful = true;
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
} else {
    $sql = "SELECT username, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    $stmt->fetch();
    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>
<article>
    <h2>Edit User Role</h2>
    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" disabled>
        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="user" <?php if ($role == 'user') echo 'selected'; ?>>User</option>
            <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="visitor" <?php if ($role == 'visitor') echo 'selected'; ?>>Visitor</option>
        </select>
        <button type="submit">Update Role</button>
    </form>
</article>

<?php if ($edit_successful): ?>
<script>
    alert('User role updated successfully');
    window.location.href = 'manage_users.php';
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
