<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] == 'visitor') {
    header("Location: index.php");
    exit();
}

include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'];
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = $target_file;
    }

    $sql = "INSERT INTO posts (title, content, author_id, image_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $content, $author_id, $image_path);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}
?>

<article>
    <h2>New Post</h2>
    <form action="new_post.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content</label>
        <textarea id="content" name="content" required></textarea>

        <label for="image">Image</label>
        <input type="file" id="image" name="image">

        <button type="submit">Post</button>
    </form>
</article>

<?php include 'includes/footer.php'; ?>
