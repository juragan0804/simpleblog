<?php
include 'includes/config.php';
session_start();

$login_error = '';
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Proses login
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $hashed_password, $role);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;
                header("Location: index.php");
                exit();
            } else {
                $login_error = "Invalid username or password.";
            }
        } else {
            $login_error = "Invalid username or password.";
        }
    } elseif (isset($_POST['register'])) {
        // Proses pendaftaran
        $username = $_POST['reg_username'];
        $password = password_hash($_POST['reg_password'], PASSWORD_BCRYPT);
        $role = 'visitor'; // Default role

        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $password, $role);

        if ($stmt->execute()) {
            $register_success = "Registration successful. You can now log in.";
        } else {
            $register_error = "Error: " . $stmt->error;
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<article>
    <h2>Login</h2>
    <form action="login.php" method="POST" id="login-form">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <div class="form-buttons">
            <button type="submit" name="login">Login</button>
            <button type="button" id="show-register-form">Register</button>
        </div>
        <?php if ($login_error): ?>
            <p style="color:red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
    </form>

    <div id="register-section" style="display:none;">
        <h2>Register</h2>
        <form action="login.php" method="POST" id="register-form">
            <label for="reg_username">Username:</label>
            <input type="text" id="reg_username" name="reg_username" required>
            <label for="reg_password">Password:</label>
            <input type="password" id="reg_password" name="reg_password" required>
            <button type="submit" name="register">Register</button>
            <?php if ($register_error): ?>
                <p style="color:red;"><?php echo $register_error; ?></p>
            <?php elseif ($register_success): ?>
                <p style="color:green;"><?php echo $register_success; ?></p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        document.getElementById('show-register-form').addEventListener('click', function() {
            document.getElementById('register-section').style.display = 'block';
            document.getElementById('login-form').style.display = 'none';
        });
    </script>
</article>
<?php include 'includes/footer.php'; ?>
