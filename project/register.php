<?php
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
   
    $sql = "INSERT INTO user(username, email, password) VALUES ('$username', '$email', '$password' )";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! <a href='login.php'>Login now</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
