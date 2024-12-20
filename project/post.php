<?php
include 'connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$post_id = $_GET['id'];

// Fetch the post
$post_query = "SELECT * FROM posts WHERE id = ?";
$stmt = $conn->prepare($post_query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

// Count likes
$likes_query = "SELECT COUNT(*) AS total_likes FROM likes WHERE post_id = ?";
$stmt = $conn->prepare($likes_query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$total_likes = $stmt->get_result()->fetch_assoc()['total_likes'];

// Count comments
$comments_query = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = ?";
$stmt = $conn->prepare($comments_query);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$total_comments = $stmt->get_result()->fetch_assoc()['total_comments'];

// Fetch all comments
$fetch_comments = "SELECT c.content, c.created_at, u.username 
                   FROM comments c 
                   JOIN users u ON c.user_id = u.id 
                   WHERE c.post_id = ? ORDER BY c.created_at DESC";
$stmt = $conn->prepare($fetch_comments);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$comments = $stmt->get_result();
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
<p><?php echo htmlspecialchars($post['content']); ?></p>
<small>Posted on: <?php echo $post['created_at']; ?></small>

<!-- Likes and Comments -->
<div>
    <form action="like_post.php" method="post" style="display:inline;">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <button type="submit" name="like_btn">
             Like (<?php echo $total_likes; ?>)
        </button>
    </form>

    <span style="margin-left:10px;">
         Comments (<?php echo $total_comments; ?>)
    </span>
</div>

<!-- Comment Section -->
<h2>Comments</h2>
<div>
    <?php while ($comment = $comments->fetch_assoc()): ?>
        <div class="comment">
            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
            <p><?php echo htmlspecialchars($comment['content']); ?></p>
            <small><?php echo $comment['created_at']; ?></small>
        </div>
    <?php endwhile; ?>
</div>

<!-- Add Comment -->
<?php if ($user_id): ?>
    <form action="add_comment.php" method="post">
        <textarea name="content" placeholder="Write your comment..." required></textarea>
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <button type="submit">Post Comment</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Log in</a> to add a comment.</p>
<?php endif; ?>

</body>
</html>
