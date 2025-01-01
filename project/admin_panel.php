<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    $_SESSION['error_message'] = "You must be an admin to access this page.";
    header("Location: homepage.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion and admin actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_post'])) {
        $post_id = $_POST['post_id'];
        $conn->query("DELETE FROM Post WHERE id = $post_id");
        $conn->query("DELETE FROM Comment WHERE post_id = $post_id");
        $conn->query("DELETE FROM Likes WHERE post_id = $post_id");
    } elseif (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];
        $conn->query("DELETE FROM Comment WHERE id = $comment_id");
    } elseif (isset($_POST['delete_like'])) {
        $like_id = $_POST['like_id'];
        $conn->query("DELETE FROM Likes WHERE id = $like_id");
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $conn->query("DELETE FROM User WHERE id = $user_id");
        $conn->query("DELETE FROM Post WHERE user_id = $user_id");
        $conn->query("DELETE FROM Comment WHERE user_id = $user_id");
        $conn->query("DELETE FROM Likes WHERE user_id = $user_id");
    } elseif (isset($_POST['make_admin'])) {
        $user_id = $_POST['user_id'];
        $conn->query("UPDATE User SET is_admin = 1 WHERE id = $user_id");
    } elseif (isset($_POST['remove_admin'])) {
        $user_id = $_POST['user_id'];
        $conn->query("UPDATE User SET is_admin = 0 WHERE id = $user_id");
    }
}

// Fetch data for display
$posts = $conn->query("SELECT id, title FROM Post");
$users = $conn->query("SELECT id, username, is_admin FROM User");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Fishblog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="homepage.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="admin-panel">
            <h2>Manage Posts</h2>
            <ul>
                <?php while ($post = $posts->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" name="delete_post">Delete Post</button>
                        </form>
                        <ul>
                            <li><strong>Comments:</strong></li>
                            <?php
                            $comments = $conn->query("SELECT id, content FROM Comment WHERE post_id = " . $post['id']);
                            while ($comment = $comments->fetch_assoc()): ?>
                                <li>
                                    <?php echo htmlspecialchars($comment['content']); ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <button type="submit" name="delete_comment">Delete Comment</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                            <li><strong>Likes:</strong></li>
                            <?php
                            $likes = $conn->query("SELECT id FROM Likes WHERE post_id = " . $post['id']);
                            while ($like = $likes->fetch_assoc()): ?>
                                <li>
                                    Like ID: <?php echo $like['id']; ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="like_id" value="<?php echo $like['id']; ?>">
                                        <button type="submit" name="delete_like">Delete Like</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                <?php endwhile; ?>
            </ul>

            <h2>Manage Users</h2>
            <ul>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($user['username']); ?> (Admin: <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?>)
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                            <?php if ($user['is_admin']): ?>
                                <button type="submit" name="remove_admin">Remove Admin</button>
                            <?php else: ?>
                                <button type="submit" name="make_admin">Make Admin</button>
                            <?php endif; ?>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Fishblog. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>