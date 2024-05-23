<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';

// Variabel untuk pencarian
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Menghitung jumlah total postingan untuk paginasi
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // Admin bisa melihat semua postingan
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?";
} else {
    // Pengguna biasa hanya bisa melihat postingan mereka sendiri
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE (title LIKE ? OR content LIKE ?) AND author_id = ?";
}
$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $stmt->bind_param("ss", $search_param, $search_param);
} else {
    $stmt->bind_param("ssi", $search_param, $search_param, $_SESSION['user_id']);
}
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
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // Admin bisa melihat semua postingan
    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image_path, users.username, posts.author_id 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            WHERE posts.title LIKE ? OR posts.content LIKE ?
            ORDER BY posts.created_at DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $search_param, $search_param, $posts_per_page, $offset);
} else {
    // Pengguna biasa hanya bisa melihat postingan mereka sendiri
    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image_path, users.username, posts.author_id 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            WHERE (posts.title LIKE ? OR posts.content LIKE ?) AND posts.author_id = ?
            ORDER BY posts.created_at DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $search_param, $search_param, $_SESSION['user_id'], $posts_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<article>
    <div class="header-with-search">
        <h2>Manage Posts</h2>
        <form action="manage_posts.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><a href="post.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
                        <td><?php echo nl2br($row['content']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td>
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                | <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';

// Variabel untuk pencarian
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Menghitung jumlah total postingan untuk paginasi
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // Admin bisa melihat semua postingan
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?";
} else {
    // Pengguna biasa hanya bisa melihat postingan mereka sendiri
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE (title LIKE ? OR content LIKE ?) AND author_id = ?";
}
$stmt = $conn->prepare($sql);
$search_param = "%" . $search . "%";
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $stmt->bind_param("ss", $search_param, $search_param);
} else {
    $stmt->bind_param("ssi", $search_param, $search_param, $_SESSION['user_id']);
}
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
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // Admin bisa melihat semua postingan
    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image_path, users.username, posts.author_id 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            WHERE posts.title LIKE ? OR posts.content LIKE ?
            ORDER BY posts.created_at DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $search_param, $search_param, $posts_per_page, $offset);
} else {
    // Pengguna biasa hanya bisa melihat postingan mereka sendiri
    $sql = "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.image_path, users.username, posts.author_id 
            FROM posts 
            JOIN users ON posts.author_id = users.id 
            WHERE (posts.title LIKE ? OR posts.content LIKE ?) AND posts.author_id = ?
            ORDER BY posts.created_at DESC 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $search_param, $search_param, $_SESSION['user_id'], $posts_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<article>
    <div class="header-with-search">
        <h2>Manage Posts</h2>
        <form action="manage_posts.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Created At</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><a href="post.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a></td>
                        <td><?php echo nl2br($row['content']); ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td>
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                | <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
