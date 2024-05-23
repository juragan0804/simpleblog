<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    header("Location: index.php");
    exit();
}

$comment_id = intval($_GET['id']);
$post_id = intval($_GET['post_id']);

// Mengambil informasi komentar
$sql = "SELECT user_id, role FROM comments JOIN users ON comments.user_id = users.id WHERE comments.id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$stmt->bind_result($comment_user_id, $comment_user_role);
$stmt->fetch();
$stmt->close();

// Memastikan hanya admin atau pemilik komentar atau pengguna biasa yang dapat menghapus komentar pengunjung
if ($_SESSION['role'] != 'admin' && $_SESSION['user_id'] != $comment_user_id && ($comment_user_role != 'visitor' || $_SESSION['role'] != 'user')) {
    header("Location: post.php?id=$post_id");
    exit();
}

// Menghapus komentar dari database
$sql = "DELETE FROM comments WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    echo "Comment deleted successfully.";
} else {
    echo "Error deleting comment: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: post.php?id=$post_id");
exit();
?>
