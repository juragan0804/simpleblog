<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } else {
        $user_id = $_SESSION['user_id'];

        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($current_password, $hashed_password)) {
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_hashed_password, $user_id);

            if ($stmt->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Error updating password: " . $stmt->error;
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<article>
    <h2>Change Password</h2>
    <form action="change_password.php" method="POST">
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Change Password</button>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php elseif ($success): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>
    </form>
</article>
<?php include 'includes/footer.php'; ?>
