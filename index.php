<?php
include 'includes/config.php';
session_start();
include 'includes/header.php';

// Variabel untuk pencarian
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Menghitung jumlah total postingan untuk paginasi
$sql = "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_posts = $row['total'];
$stmt->close();

// Mengatur jumlah postingan per halaman
$posts_per_page = 5;
$total_pages = ceil($total_posts / $posts_per_page);

// Menentukan halaman saat ini
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($total_pages, $current_page));

// Menentukan offset untuk query SQL
$offset = ($current_page - 1) * $posts_per_page;

// Mengambil postingan dari database dengan paginasi
$sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image_path, users.username, users.id AS user_id
        FROM posts 
        JOIN users ON posts.author_id = users.id 
        WHERE posts.title LIKE ? OR posts.content LIKE ?
        ORDER BY posts.created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $search_param, $search_param, $posts_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<article>
    <div class="header-with-search">
        <h2>Welcome to My Blog</h2>
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h3><a href="post.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></h3>
                <?php if ($row['image_path']): ?>
                    <img src="<?php echo $row['image_path']; ?>" alt="Post Image">
                <?php endif; ?>
                <p><?php echo nl2br($row['content']); ?></p>
                <small>Posted by <a href="profile.php?id=<?php echo $row['user_id']; ?>"><?php echo $row['username']; ?></a> on <?php echo $row['created_at']; ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No posts found</p>
    <?php endif; ?>

    <!-- Paginasi -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $current_page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" <?php if ($i == $current_page) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($current_page < $total_pages): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $current_page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</article>

<?php include 'includes/footer.php'; ?>
