<?php


session_start();

if (isset($_SESSION["admin"])) {
  header("Location: admin_dashboard.php");
  exit();
}

if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
  }


require_once('connect.php'); 

if(isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    require_once ('connect.php');
    $sql = "SELECT * FROM if0_37296747_admin.admin_acc WHERE username = '$username'";
    $result = $conn->query($sql);
    $admin = mysqli_fetch_array($result,  MYSQLI_ASSOC);
    if ($admin) {
        if ($password ==  $admin['password']) {
            session_start();
            $_SESSION['admin_id'] = $admin['ID'];
            $_SESSION['admin'] = $admin['name'];
            $_SESSION['position'] = $admin['position'];
            header("Location: admin_dashboard.php");
            die();
        } else {
            echo "<div class='alert alert-danger m-0'>Username or Password doesn't match</div>";
        }

    } else {
        echo "<div class='alert alert-danger m-0'>Username or Password doesn't match</div>";
    }


}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
      body {
        font-family: 'poppins', sans-serif;
      }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-sm bg-white border-bottom mt-0" > 
        <div class="container-xxl align-items-center">
            <a id="logo" class="navbar-brand mx-4 border px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="index.php">STI Wears</a>
        </div>
    </nav>
<div class="container col-sm-6 col-12 mt-4">
<h3 class="text-start">Admin Login</h3>
<div class="mt-2 border bg-white shadow-sm">
    <div class="p-4">
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="text-end">
        <input type="submit" value="login" name="login" class="btn btn-primary">
        </div>
    </form>

    </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
