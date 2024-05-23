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
$sql = "SELECT author_id FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->bind_result($author_id);
$stmt->fetch();
$stmt->close();

// Memastikan hanya admin atau pemilik postingan yang dapat menghapus postingan
if ($_SESSION['role'] != 'admin' && $_SESSION['user_id'] != $author_id) {
    echo "You do not have permission to delete this post.";
    exit();
}

// Menghapus komentar terkait postingan terlebih dahulu
$sql = "DELETE FROM comments WHERE post_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

// Menghapus postingan dari database
$sql = "DELETE FROM posts WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    echo "Post deleted successfully.";
} else {
    echo "Error deleting post: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: index.php");
exit();
?>
