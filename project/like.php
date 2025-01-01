<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Check if already liked
$check_like = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_like);
$stmt->bind_param('ii', $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Add like
    $insert_like = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_like);
    $stmt->bind_param('ii', $post_id, $user_id);
    $stmt->execute();

    // Increment like count in post table
    $update_post = "UPDATE post SET likes = likes + 1 WHERE id = ?";
    $stmt = $conn->prepare($update_post);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
} else {
    // Remove like
    $delete_like = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_like);
    $stmt->bind_param('ii', $post_id, $user_id);
    $stmt->execute();

    // Decrement like count in post table
    $update_post = "UPDATE post SET likes = likes - 1 WHERE id = ?";
    $stmt = $conn->prepare($update_post);
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
}

header("Location: post.php?id=$post_id");
exit;
?>
