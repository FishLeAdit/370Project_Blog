<?php
$servername = "localhost";
$user_name = "root"; 
$user_password = "";    
$db_name = "blog";

$conn = new mysqli($servername, $user_name, $user_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful";
}
?>