<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/icon.png"/>
  <style>
    body {
      font-family: 'poppins', sans-serif;
    }

    ::-webkit-scrollbar {
      width: 0;
    }

    @font-face {
      font-family: moderniz;
      src: url(../font/Moderniz.otf);
    }
  </style>
</head>


<nav class="navbar navbar-expand-sm color-blue sticky-top" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
  <div class="container-xxl align-items-center ">
    <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="index.php">STI Wears</a>
    <div class="d-flex col-6 justify-content-end align-items-center">
      <p class=" col-auto m-0 h-100 align-items-center d-none d-sm-block me-3"> <?php echo $_SESSION['user']; ?> | <?php echo $_SESSION['section']; ?> </p>
      <a href="cart.php" class="btn btn-light col-3 px-1 mx-1 text-dark" style="min-width: 75px; max-width:75px; max-height: 50px;">Cart</a>
      <button class="btn btn-light col-3 px-1 me-2" style="min-width: 75px; max-width:75px; max-height: 50px;" type="offcanvas" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"> Menu</button>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header color-blue p-3">
    <h5 class="offcanvas-title text-light" id="offcanvasExampleLabel">User Profile</h5>
   <!-- <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button> -->
  </div>
  

  <div class="offcanvas-body m-0 p-3">

     <h6 class="m-0" id="offcanvasExampleLabel"><?php echo $_SESSION['user']; ?></h6>
  <h6 class="m-0" d="offcanvasExampleLabel"><?php echo $_SESSION['student_id']; ?></h6>
  <h6 class="m-0" id="offcanvasExampleLabel"><?php echo $_SESSION['section']; ?></h6>


    <hr>
    <div class="row gy-2 px-2">
      <a href="dashboard.php" class="btn btn-primary"> Dashboard </a>

      <a href="order_history.php" class="btn btn-primary"> Order History </a>
      <a href="function/logout_func.php" class="btn btn-warning ">Logout</a>

    
</div>
  </div>
</div>