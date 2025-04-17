<?php
$student_ID = $student_email = $full_name = $section = "";
$errors = [
    'student_ID' => '',
    'student_email' => '',
    'full_name' => '',
    'section' => ''
];
require_once('connect.php');
session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

//echo $_GET["is_logged_in"] . $_GET["name"] . $_GET['email'];

if ($_SESSION["is_logged_in"] == true) {
    $nameAuth = $_SESSION["name"];
    $emailAuth = $_SESSION["email"];
    
    $sql = "SELECT `active_acc` FROM account WHERE `student_email` = '$emailAuth'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row["active_acc"] == 1) {
        header("Location: function/login_func.php");
        } else {
            session_unset();
            header("Location: index.php?status=banned");
            exit();
        }
    }
} else {
   header("Location: index.php");
}


if (isset($_POST["submit"])) {
    $student_ID = $_POST["student_ID"];
   // $student_email = $_POST["student_email"];
    // $full_name = $_POST["full_name"];
    $section = $_POST["section"];
    $active_acc = 1;
    
    if (empty($student_ID) || empty($section)) {
        array_push($errors, "All fields are required");
    }

    /*if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
        $errors['student_email'] = "Email is not valid";
    } elseif (!preg_match('/@cubao\.sti\.edu\.ph$/', $student_email)) {
        $errors['student_email'] = "Only email addresses from @cubao.sti.edu.ph are allowed";
    }*/

    
    if (empty($student_ID)) {
        $errors['student_ID'] = "Student ID is required";
    } elseif (!preg_match('/^[0-9]{11}$/', $student_ID)) {
        $errors['student_ID'] = "Student ID must be exactly 11 characters long and can only contain numbers";
    }

    if (empty($section)) {
        $errors['section'] = "Section is required";
    } elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9 ]{6,12}$/', $section)) {
        $errors['section'] = "Double check your section, must include letters and numbers";
    }

   

  

    require_once "connect.php";

   
    $sql1 = "SELECT * FROM account WHERE student_ID = '$student_ID'";
    $result1 = mysqli_query($conn, $sql1);
    if (mysqli_num_rows($result1) > 0) {
        $errors['student_ID'] = "Student ID already exists! Message us for assistance.";
    }

    if (!array_filter($errors)) {
        $sql = "INSERT INTO account (student_id, full_name, section, student_email, active_acc) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $student_ID, $nameAuth, $section, $emailAuth, $active_acc);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'>Account created successfully</div>";
header("Location: function/login_func.php");
        } else {
            die("Something went wrong");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        .text-danger {
            color: red;
            /* Red color for error messages */
        }

        .small {
            font-size: 0.8rem;
            /* Smaller font size */
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
            /* Space for the eye icon */
        }

        .password-wrapper .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            /* Prevents text selection on click */
        }

        body {
            font-family: 'poppins', sans-serif;
        }

        ::-webkit-scrollbar {
      width: 0;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm" style="background-color: #2198f4; color: #F0F0F0; font-weight: bold;">
        <div class="container-xxl align-items-center">
            <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="index.php">STI Wears</a>

        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <form action="signup.php" method="POST" class="col-11 col-md-7 border p-md-5 p-3 bg-light-subtle shadow form">

                <label class="form-label">Student Email</label>
                <input type="email" value="<?php echo $emailAuth; ?>" class="form-control" name="student_email" disabled/>
                <div class="text-danger small mb-3"><?php echo $errors['student_email']; ?></div>

                <label class="form-label">Full name</label>
                <input type="text" value="<?php echo $nameAuth; ?>" class="form-control" name="full_name" disabled />
                <div class="text-danger small mb-3"><?php echo $errors['full_name']; ?></div>

                <label class="form-label">Student ID</label>
                <input type="number" placeholder="e.g 02000123456" class="form-control" name="student_ID" value="<?php echo $student_ID; ?>" />
                <div class="text-danger small mb-3"><?php echo $errors['student_ID']; ?></div>
                
                <label class="form-label">Strand / Course</label>
                <input type="text" class="form-control" placeholder="e.g ITM101 / BSIT101" name="section" value="<?php echo $section; ?>" />
                <div class="text-danger small mb-3 "><?php echo $errors['section']; ?></div>
                
                <input type="hidden" name="submit">
                <button type="submit" class="bg-success radius-sm text-light border-1 col-12 my-3 p-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Register
                </button>


        
            </form>
        </div>
    </div>

    <?php include 'footer.php' ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        function togglePassword(inputId) {
            var passwordInput = document.getElementsByName(inputId)[0];
            var eyeIcon = passwordInput.nextElementSibling;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<i class="bi bi-eye-slash"></i>'; // Change icon when password is visible
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<i class="bi bi-eye"></i>'; // Change back to eye icon
            }
        }
    </script>
</body>

</html>