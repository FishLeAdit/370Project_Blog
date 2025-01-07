<?php
session_start();
require 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information from the database
$sql = "SELECT username, email, is_admin FROM User WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>blog</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="profile">
            <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo $user['is_admin'] ? "Admin" : "User"; ?></p>

            <?php if ($user['is_admin']): ?>
                <a href="write_post.php" class="btn">Write a Post</a>
                <a href="admin_panel.php" class="btn">Admin Panel</a>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>
