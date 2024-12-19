<?php
   $servername = 'localhost';
   $db_name = 'blog';
   $user_name = 'root';
   $user_password = '';

   $conn = new mysqli($servername, $user_name, $user_password);

    
   if ($conn->connect_error){
      die("failed:".$conn->connect_error);
   }
   else{
      mysqli_select_db($conn,$db_name);
      
   }
?>