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

// Fetch categories for the dropdown
$categories_sql = "SELECT id, name FROM Category ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $_SESSION['error_message'] = "Title and content cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO Post (title, content, category_id, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $title, $content, $category_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Post created successfully.";
        header("Location: homepage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write New Post - Fishblog</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><a href="homepage.php" style="color: white; text-decoration: none;">Fishblog</a></h1>
        </div>
    </header>

    <div class="container">
        <h2>Write New Post</h2>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error_message']; ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="post-form">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required maxlength="150">
            </div>

            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required rows="10"></textarea>
            </div>

            <button type="submit">Publish Post</button>
        </form>
    </div>

</body>
</html>

<?php
$conn->close();
?>
