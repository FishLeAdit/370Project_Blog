<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category_sql = "SELECT id, name FROM category ORDER BY name ASC";
$category_result = $conn->query($category_sql);

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$post_sql = "SELECT post.id, post.title, post.content, post.created_at, user.username, 
            category.id AS category_id, category.name AS category, post.likes 
            FROM post 
            LEFT JOIN user ON post.user_id = user.id 
            LEFT JOIN category ON post.category_id = category.id";

if ($category_id) {
    $post_sql .= " WHERE post.category_id = $category_id";
}
$post_sql .= " ORDER BY post.created_at DESC LIMIT 5";
$post_result = $conn->query($post_sql);

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$user_name = ($is_logged_in && isset($_SESSION['username'])) ? $_SESSION['username'] : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishblog</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><a href="homepage.php" style="color: white; text-decoration: none;">Fishblog</a></h1>
            <div>
                <div>
                    <?php if ($is_logged_in && $user_name !== "Guest"): ?>
                        <span>Hello, <?php echo htmlspecialchars($user_name); ?>!</span>
                        <a href="profile.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <span>Hello, Guest!</span>
                        <a href="login.php">Login</a>
                        <a href="signup.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <?php if ($is_admin == 1): ?>
        <div class="container">
            <a href="write_post.php" class="write-post">Write New Post</a>
        </div>
    <?php endif; ?>

    <section class="categories container">
        <h2>Categories</h2>
        <?php if ($category_result->num_rows > 0): ?>
            <?php while ($cat = $category_result->fetch_assoc()): ?>
                <a href="?category_id=<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>
    </section>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p style="color: red;"> <?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p style="color: green;"> <?php echo $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <section class="container">
        <h2>Latest Posts</h2>
        <?php if ($post_result->num_rows > 0): ?>
            <?php while ($row = $post_result->fetch_assoc()): ?>
                <div class="post">
                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                    <small>
                        By <?php echo htmlspecialchars($row['username'] ?? "Unknown Author"); ?>
                        in <a href="?category_id=<?php echo $row['category_id']; ?>" style="color: #1abc9c;">
                            <?php echo htmlspecialchars($row['category'] ?? "Uncategorized"); ?>
                        </a>
                        on <?php echo htmlspecialchars($row['created_at']); ?> 
                        | Likes: <?php echo htmlspecialchars($row['likes'] ?? 0); ?>
                    </small>
                    <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 200))); ?>...</p>
                    <a href="post.php?id=<?php echo $row['id']; ?>" style="color: #1abc9c;">Read More</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts available.</p>
        <?php endif; ?>
    </section>

</body>
</html>

<?php $conn->close(); ?>