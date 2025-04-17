<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["email"])) {
     $student_email = $_SESSION["email"];
        require_once "connect.php";
        $sql = "SELECT * FROM account WHERE student_email = '$student_email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($user["active_acc"] == 1) {
        if ($user) {
            session_start();
            $_SESSION["account_id"] = $user["account_id"];
            $_SESSION["student_id"] = $user["student_id"];
            $_SESSION["student_email"] = $user["student_email"];
            $_SESSION["user"] = $user["full_name"];

            $_SESSION["section"] = $user["section"];
            header("Location: ../dashboard.php");

            //FOR TEST
            // session_start();
            // $_SESSION["account_id"] = 10;
            // $_SESSION["student_id"] = 2000363495;
            // $_SESSION["student_email"] = "barcinilla.363495@cubao.sti.edu.ph";
            // $_SESSION["user"] = "john paul barcinilla";

            // $_SESSION["section"] = "ITM402";
            // header("Location: ../dashboard.php");
            die();
        } 
    } else {
        header("Location: ../index.php?access=Denied");
        die();
    }
}

      

?>