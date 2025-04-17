<?php
    $servername = "localhost";
    $user_name = "root";
    $pass = "";
    $user_database = "if0_37296747_user"; //db ng uses side
    $admin_database = "if0_37296747_admin"; //db ng admin side
    
    //connection for user db
    $conn = mysqli_connect($servername, $user_name, $pass, $user_database);
    if (!$conn ) {
        die("Connection failed: " . mysqli_connect_error());
    }

    //connection for admin acc
    $conn2 = mysqli_connect($servername, $user_name, $pass, $admin_database);
    if (!$conn2) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ?>