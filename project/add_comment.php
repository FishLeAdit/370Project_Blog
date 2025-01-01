<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "You must be logged in to post a comment.";
        header("Location: login.php");
        exit();
    }

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $comment_content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if ($post_id && !empty($comment_content)) {
        include("connect.php");

        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("INSERT INTO comment (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");

        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            $_SESSION['error_message'] = "Failed to prepare the SQL statement.";
            header("Location: post.php?id=" . $post_id);
            exit();
        }

        $stmt->bind_param("iis", $post_id, $user_id, $comment_content);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Comment added successfully.";
        } else {
            error_log("Execute failed: " . $stmt->error);
            $_SESSION['error_message'] = "Failed to add comment.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error_message'] = "Invalid post ID or empty comment.";
    }

    header("Location: post.php?id=" . $post_id);
    exit();
} else {
    header("Location: homepage.php");
    exit();
}
?>