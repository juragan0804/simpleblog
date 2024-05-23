<?php
include 'includes/config.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = intval($_GET['id']);

// Mengambil informasi postingan
$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.author_id = users.id 
        WHERE posts.id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: index.php");
    exit();
}

// Mengambil komentar untuk postingan
$sql = "SELECT comments.id, comments.comment, comments.created_at, users.username, comments.user_id, users.role 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments_result = $stmt->get_result();
$stmt->close();

include 'includes/header.php';
?>

<article>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    <p><em>Posted by <?php echo htmlspecialchars($post['username']); ?> on <?php echo $post['created_at']; ?></em></p>

    <h3>Comments</h3>
    <?php if ($comments_result->num_rows > 0): ?>
        <?php while ($comment = $comments_result->fetch_assoc()): ?>
            <div class="comment">
                <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                <p><em>Commented by <?php echo htmlspecialchars($comment['username']); ?> on <?php echo $comment['created_at']; ?></em></p>
                <?php if ($_SESSION['role'] == 'admin' || ($_SESSION['role'] == 'user' && ($comment['user_id'] == $_SESSION['user_id'] || $comment['role'] == 'visitor'))): ?>
                    <a href="delete_comment.php?id=<?php echo $comment['id']; ?>&post_id=<?php echo $post_id; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['username'])): ?>
        <form action="add_comment.php" method="POST">
            <textarea name="comment" required></textarea>
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <button type="submit">Add Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to add a comment.</p>
    <?php endif; ?>
</article>

<?php include 'includes/footer.php'; ?>
