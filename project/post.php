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

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$post_sql = "SELECT post.id, post.title, post.content, post.created_at, user.username, post.likes 
             FROM post 
             LEFT JOIN user ON post.user_id = user.id 
             WHERE post.id = ?";
$stmt = $conn->prepare($post_sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post_result = $stmt->get_result();
$post = $post_result->fetch_assoc();

$comment_sql = "SELECT comment.content, comment.created_at, user.username 
                FROM comment 
                LEFT JOIN user ON comment.user_id = user.id 
                WHERE comment.post_id = ? 
                ORDER BY comment.created_at DESC";
$stmt = $conn->prepare($comment_sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comment_result = $stmt->get_result();

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Check if the user has already liked the post
$liked = false;
if ($is_logged_in) {
    $check_like_sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_like_sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $like_result = $stmt->get_result();
    $liked = $like_result->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1><?php echo htmlspecialchars($post['title']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
<small>Posted on: <?php echo $post['created_at']; ?></small>
<p>By <?php echo htmlspecialchars($post['username']); ?> on <?php echo htmlspecialchars($post['created_at']); ?></p>
<p>Likes: <?php echo htmlspecialchars($post['likes']); ?></p>

<!-- Like Button -->
<?php if ($is_logged_in): ?>
    <form action="like.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
        <button type="submit"><?php echo $liked ? 'Unlike' : 'Like'; ?></button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> to like this post.</p>
<?php endif; ?>

<!-- Comment Section -->
<h2>Comments</h2>
<div>
    <?php while ($comment = $comment_result->fetch_assoc()): ?>
        <div class="comment">
            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
            <p><?php echo htmlspecialchars($comment['content']); ?></p>
            <small><?php echo htmlspecialchars($comment['created_at']); ?></small>
        </div>
    <?php endwhile; ?>
</div>

<!-- Add Comment -->
<?php if ($is_logged_in): ?>
    <form action="add_comment.php" method="post">
        <textarea name="content" placeholder="Write your comment..." required></textarea>
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
        <button type="submit">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> to add a comment.</p>
<?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
