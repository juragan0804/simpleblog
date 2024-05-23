<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = intval($_GET['id']);

// Mengambil informasi pengguna
$sql = "SELECT username, role, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: index.php");
    exit();
}

// Membatasi akses ke profil admin untuk pengguna biasa
if ($user['role'] == 'admin' && $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Mengambil postingan pengguna (atau semua postingan jika pengguna adalah admin dan melihat profil admin)
if ($user['role'] == 'admin' && $_SESSION['role'] == 'admin') {
    // Admin melihat profil admin, tampilkan semua postingan
    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username, posts.author_id 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            ORDER BY posts.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    // Tampilkan postingan pengguna biasa
    $sql = "SELECT id, title, content, created_at, author_id FROM posts WHERE author_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$posts_result = $stmt->get_result();
$stmt->close();

include 'includes/header.php';
?>

<article>
    <h2>Profile of <?php echo htmlspecialchars($user['username']); ?></h2>
    <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
    <p>Member since: <?php echo htmlspecialchars($user['created_at']); ?></p>

    <h3>list Post <?php echo htmlspecialchars($user['username']); ?></h3>
    <?php if ($posts_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <?php if ($user['role'] == 'admin' && $_SESSION['role'] == 'admin'): ?>
                        <th>Author</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($post = $posts_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></td>
                        <td><?php echo nl2br(htmlspecialchars($post['content'])); ?></td>
                        <td><?php echo $post['created_at']; ?></td>
                        <?php if ($user['role'] == 'admin' && $_SESSION['role'] == 'admin'): ?>
                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <?php endif; ?>
                        <td>
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">Edit</a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                | <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>
</article>

<?php include 'includes/footer.php'; ?>
