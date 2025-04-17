<?php



session_start();
            $_SESSION["account_id"] = 10;
            $_SESSION["student_id"] = 2000363495;
            $_SESSION["student_email"] = "barcinilla.363495@cubao.sti.edu.ph";
            $_SESSION["user"] = "john paul barcinilla";

            $_SESSION["section"] = "ITM402";
            header("Location: ../dashboard.php");

if (isset($_SESSION["user"])) {
  header("Location: dashboard.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: admin_dashboard.php");
  exit();
}

$_SESSION["is_logged_in"] = false;

if (isset($_GET["access"]) && $_GET["access"] == "Denied") {
    echo "<div class='alert alert-danger m-0'>Access Denied. Please contact admin for assistance.</div>";
    die();
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>STI Wears</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/icon.png"/>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'poppins', sans-serif;
    margin: 0;
    padding: 0;
}

.loaderDiv {
  display: none;
  opacity: 0;
  position: fixed;
  z-index: 9999999;
  top: 0;
  height: 100vh;
  width: 100vw;
  background: #0000008A;
}

.loaderGif {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  height: 50px;
  width: 50px;
}

    ::-webkit-scrollbar {
      width: 0;
    }

    .bd_lg {
      backdrop-filter: blur(3px);
      background-color: rgba(0, 0, 0, 0.3);
    }
    }
  </style>

<body>

  <?php include "function/login_func.php" ?>
  
  <div class="loaderDiv" id="loaderDiv">
    <img class="loaderGif" id="loaderGif" src="Loader.gif">
  </div>

  <div class="d-block">
    <nav class="navbar navbar-expand-sm bg-light sticky-top border-bottom">
      <div class="container-xxl">
        <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="#">STI Wears</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- (Login/Signup) -->
        <div class="collapse navbar-collapse mt-3" id="navbarNav">
          <ul class="navbar-nav me-2 ms-auto my-1 w-100 justify-content-end align-items-center">
            <a class="nav-item mb-2 mb-sm-0 d-md-none col-12 col-sm-4 btn color-yellow text-dark " href="javascript:void(0)" onclick="redirectLogin()">Student</button>
              <a class="nav-item mb-2 mb-sm-0 d-md-none col-12 col-sm-4 ms-sm-1 btn color-blue text-light " href="admin_login.php">Admin</a> 
           
          </ul>
        </div>
      </div>


    </nav>

   

    <section id="intro">
      <div class="container-fluid p-0">



        <div class="row justify-content-center g-0">
          <!-- Image Section -->
          <div class="col-md-6 col-sm-12">
            <img src="assets/LP_1.png" alt="test" class="img-fluid" />
          </div>

          <!-- Content Section -->
          <div
            class="col-6 col align-items-center justify-content-center d-md-grid d-none p-0">

            <div>
                

             <div class="col-12 p-3 bg-light shadow">
          <a href="javascript:void(0)" onclick="redirectLogin()" class="btn color-yellow col-12">Student log in</a>
          <hr class="mx-2">
          <a href="admin_login.php" class="btn color-blue text-light col-12">Admin log in</a>
       

          
              </div> 
              <div class="d-none col-12 bg-light shadow bg-success mt-2 p-3">
                <a href="admin_login.php" class="btn color-blue text-light col-12">Admin Login</a>
                <!-- <a href="signin.php" class="btn bg-success text-light col-12"> Test ONLY</a> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

<script>


    function redirectLogin() {
        document.getElementById("loaderDiv").style.display = "block";
document.getElementById("loaderDiv").style.opacity = "1";
console.log("test");
        setTimeout(function() {
        window.location.assign("signin.php");
         }, 500);
        
}
</script>

    <section id="about" class="bg-light m-0 p-3 border">
      <h4 class="text-center">Features</h4>
      <div class="container-fluid">
        <div class="row justify-content-center m-2 gy-4 gx-5">
          <div class="col-sm-3 width">
            <div class="card">
              <img src="assets/order_at_ease.png" class="card-img-top img-fluid border" alt="..." />
              <div class="card-body">
                <h5 class="card-title text-center testing">Order at ease</h5>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card">
              <img src="assets/stock_transparency.png" class="card-img-top img-fluid border" alt="..." />
              <div class="card-body">
                <h5 class="card-title text-center">Stock Transparency</h5>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <div class="card">
              <img src="assets/order_form.png" class="card-img-top img-fluid border" alt="..." />
              <div class="card-body">
                <h5 class="card-title text-center">E-order Form</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section id="guide" class="p-4">
      <h4 class="text-center">Order Process</h4>
      <div class="container  p-3">
        <div class="row gy-3 m-2 text-center">
          <div class="col-12 my-4 bg-light p-2">
            <p class="m-0">step 1</p>
            <p class="m-0 text-black-50">Select products you want to order</p>

          </div>
          <div class="col-12 my-4 bg-light p-2">
            <p class="m-0">step 2</p>
            <p class="m-0 text-black-50">Choose payment method</p>
          </div>
          <div class="col-12 my-4 bg-light p-2">
            <p class="m-0">step 3</p>
            <p class="m-0 text-black-50">Proceed to cashier and provide your order details</p>
          </div>
          <div class="col-12 my-4 bg-light p-2 ">
            <p class="m-0">step 4</p>

            <p class="m-0 text-black-50">Claim your items at the proware</p>
          </div>
        </div>
      </div>
    </section>

    <section id="inventory" class="bg-dark p-5 m-0">
      <div class="container bg-light p-3 ">
        <div class="row justify-content-center my-3">
          <div class="col-sm-6 p-3 m-0">
            <h2 class="lead fw-bold border text-center bg-white shadow-sm p-3">Available</h2>
            <table class="table table-striped table-responsive shadow-sm">
              <tr>
                <th>Product Name</th>
                <th>Size</th>
              </tr>
              <?php
              require_once 'connect.php';
              $query = "SELECT i.product_name, id.size 
             FROM item i 
             JOIN item_details id ON i.product_id = id.item_id 
             WHERE id.stock > 0";
              $result = mysqli_query($conn2, $query);

              $products = array();
              while ($row = mysqli_fetch_assoc($result)) {
                if (!isset($products[$row['product_name']])) {
                  $products[$row['product_name']] = array();
                }
                $products[$row['product_name']][] = $row['size'];
              }

              foreach ($products as $productName => $sizes) {
                echo "<tr>
            <td> $productName </td>
            <td> " . implode(', ', $sizes) . " </td>
            </tr>";
              }
              ?>
            </table>
          </div>

          <div class="col-sm-6 p-3 m-0">
            <h2 class="lead fw-bold border text-center bg-white shadow-sm p-3">Not Available</h2>
            <table class="table table table-striped table-responsive shadow-sm">
              <tr>
                <th>Product Name</th>
                <th>Size</th>
              </tr>
              <?php
              $query = "SELECT i.product_name, id.size 
             FROM item i 
             JOIN item_details id ON i.product_id = id.item_id 
             WHERE id.stock <= 0";
              $result = mysqli_query($conn2, $query);

              $products = array();
              while ($row = mysqli_fetch_assoc($result)) {
                if (!isset($products[$row['product_name']])) {
                  $products[$row['product_name']] = array();
                }
                $products[$row['product_name']][] = $row['size'];
              }
              foreach ($products as $productName => $sizes) {
                echo "<tr>
            <td> $productName </td>
            <td> " . implode(', ', $sizes) . " </td>
            </tr>";
              }
              ?>
            </table>
          </div>
        </div>
      </div>
    </section>
    <?php include 'footer.php' ?>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>