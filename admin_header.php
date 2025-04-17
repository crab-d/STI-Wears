   <?php
        include "function/admin_session_func.php";
   ?>
   
   <head>
       <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   <link rel="icon" type="image/png" href="assets/icon.png"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
     
        body {
            font-family: 'poppins', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 0;
        }

        @font-face {
            font-family: moderniz;
            src: url(font/Moderniz.otf);
        }

    </style>
</head>


  

       <nav class="navbar navbar-expand-sm color-blue sticky-top col-12" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
  <div class="container-xxl align-items-center ">
    <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="admin_dashboard.php">STI Wears</a>
    <div class="d-flex col-6 justify-content-end align-items-center">
      <p class=" col-auto m-0 h-100 align-items-center d-none d-sm-block me-3"> <?php echo $_SESSION[
          "admin"
      ]; ?> | <?php echo $_SESSION["position"]; ?> </p>

      <button class="btn btn-light col-3 px-1 me-2" style="min-width: 75px; max-width:75px; max-height: 50px;" type="offcanvas" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"> Menu</button>
      <a href="function/logout_func.php" class="btn btn-warning col-3 px-1" style="min-width: 75px; max-width:75px; max-height: 50px;">Logout</a>

    </div>
  </div>
</nav>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header color-blue p-3">
                <h5 class="offcanvas-title text-light" id="offcanvasExampleLabel">Admin Profile</h5>
            </div>
  
                <div class="offcanvas-body m-0 p-3">
                 <h6 class="m-0" id="offcanvasExampleLabel"><?php echo $_SESSION[
                     "admin"
                 ]; ?></h6>
                <h6 class="m-0" d="offcanvasExampleLabel"><?php echo $_SESSION[
                    "position"
                ]; ?></h6>
                <hr>
                <div class="row gy-2 px-2">
                    <a href="admin_dashboard.php" class="btn btn-primary"> Dashboard </a>
                    <?php if ($_SESSION["position"] != "cashier") { 
                        echo '<a href="add_item.php" class="btn btn-primary"> Add new item </a>
                              <a href="stock_management.php" class="btn btn-primary"> Stock Management </a>  ';
                    } ?>
                    
                    <a href="order_record.php" class="btn btn-primary"> Order Management </a>

                </div>
            </div>
        </div>