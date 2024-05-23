<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $_POST['comment'];
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    // Menyimpan komentar baru ke database
    $sql = "INSERT INTO comments (comment, post_id, user_id, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("sii", $comment, $post_id, $user_id);

    if ($stmt->execute()) {
        header("Location: post.php?id=$post_id");
        exit();
    } else {
        echo "Error adding comment: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
