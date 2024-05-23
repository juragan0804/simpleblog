<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blog kelompok 1</title>
    <link rel="stylesheet" href="/simpleblog/css/styles.css">
</head>
<body>
<header>
    <h1>My Simple Blog Kelompok 1</h1>
    <nav>
        <ul>
            <li><a href="/simpleblog/index.php">Home</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li class="dropdown">
                    <a href="javascript:void(0)" class="dropbtn">Posts</a>
                    <div class="dropdown-content">
                        <a href="/simpleblog/new_post.php">New Post</a>
                        <a href="/simpleblog/profile.php?id=<?php echo $_SESSION['user_id']; ?>">Manage Posts</a>
                    </div>
                </li>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <li><a href="/simpleblog/manage_users.php">Manage Users</a></li>
                <?php endif; ?>
                <li><a href="/simpleblog/logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a></li>
            <?php endif; ?>
            <li><a href="/simpleblog/about.php">About</a></li>
            <?php if (!isset($_SESSION['username'])): ?>
                <li><a href="/simpleblog/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
