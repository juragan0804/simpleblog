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

$post_id = intval($_GET['id']);

// Mengambil informasi postingan
$sql = "SELECT id, title, content, author_id FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($id, $title, $content, $author_id);
$stmt->fetch();
$stmt->close();

// Memastikan hanya admin atau pemilik postingan yang dapat mengedit postingan
if ($_SESSION['role'] != 'admin' && $_SESSION['user_id'] != $author_id) {
    echo "You do not have permission to edit this post.";
    exit();
}

// Memproses pembaruan postingan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_title = $_POST['title'];
    $new_content = $_POST['content'];

    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ssi", $new_title, $new_content, $post_id);

    if ($stmt->execute()) {
        header("Location: post.php?id=$post_id");
        exit();
    } else {
        echo "Error updating post: " . $conn->error;
    }

    $stmt->close();
}

include 'includes/header.php';
?>

<article>
    <h2>Edit Post</h2>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="POST">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        <label for="content">Content</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($content); ?></textarea>
        <button type="submit">Update Post</button>
    </form>
</article>

<?php include 'includes/footer.php'; ?>
