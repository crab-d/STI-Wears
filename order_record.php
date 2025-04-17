<?php
include "function/admin_session_func.php";
require("connect.php");

 
if (isset($_GET["filter"])) {  $status = $_GET["filter"]; } 
else { $status = "pending"; }

if (isset($_POST['search_id']) && ($_SERVER["REQUEST_METHOD"] == "POST")) {

    if (isset($_POST["search_id"])) {
        $sql = "SELECT order_id FROM `order` WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $_POST["search_id"]);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                header("Location: order_details.php?order_id=" . $_POST["search_id"]);
                exit;
            } else {
                echo "<div class='alert alert-danger text-center'>NO ORDER FOUND</div>";
            }
            mysqli_stmt_close($stmt);
        }
    }

    
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["status"])) {
    $status = $_POST["status"] ?? "pending"; 
    header("Location: order_record.php?filter=". $_POST["status"]); 
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>
<body>

<?php include "admin_header.php"; ?>
<div class="container mt-4 p-4">
    <div class="row">
        <div class="bg-dark col-12 col-sm-3 border text-center">
            <p class="w-100 p-1 text-center text-light">Filter</p>
            <form method="POST" action="order_record.php">
                <button type="submit" name="status" value="pending" class="btn btn-primary my-1 text-center col-3 col-sm-12">Pending</button>
                <button type="submit" name="status" value="paid" class="btn btn-primary my-1 col-3 col-sm-12">Paid</button>
                <button type="submit" name="status" value="cancelled" class="btn btn-primary my-1 col-3 col-sm-12">Cancelled</button>
            </form>
        </div>
        <div class="bg-light col-12 col-sm-9 border p-0">
            <div class="bg-warning row m-0 p-2">
                <div class="col-6 m-0 p-0">
                    <p class="m-0 p-0">Order Management</p>
                </div>
                <div class="col-6 m-0 text-end">
                    <form action="order_record.php" method="POST" class="m-0">
                        <input class="m-0 p-0 w-50" type="number" name="search_id" placeholder="Order ID">
                        <button type="submit" class="m-0 p-1 btn btn-success">Search</button>
                    </form>
                </div>
            </div>
            
            <div class="p-2">
                <script>
                $(document).ready(function() {
                    var order_count = 0;
                    var filter = <?php echo json_encode($status); ?>;  
                    var max_rows = 0; 
                    var currentrow = $("#table tbody tr").length;
                    if (currentrow < 10) {
                      $("#next").prop("disabled", true);
                    }
                    
                    
                    if (order_count == 0) {
                    $("#prev").prop("disabled", true);
                    }
                
                    function loadTable() {
                        $("#table").load("ajax/table_query.php", {
                            offset_count: order_count,
                            current_status: filter
                        }, function(response, status, xhr) {
                            if (status == "success") {
                                var rowsReturned = $("#table tbody tr").length; 
                                if (rowsReturned < 10) {
                                    max_rows = rowsReturned;  
                                    $("#next").prop("disabled", true);
                                    
                                
                                } else {
                                    max_rows = 0;
                                    $("#next").prop("disabled", false);
                                }
                            }
                        });
                    }

                
   
             
                    $("#next").click(function() {
                      console.log(order_count);
                        if (max_rows === 0) {  
                            order_count += 10;
                            loadTable();
                            $("#prev").prop("disabled", false);
                            
                           
                        }
                    });

                  
                    $("#prev").click(function() {
                      console.log(order_count);
                      if (order_count == 0) {
  $("#prev").prop("disabled", true);
}
                        if (order_count >= 10) {
                            order_count -= 10;
                            loadTable();
                            $("#next").prop("disabled", false);
                        }
                      
                     
                    });
                });
                </script>

                <div class="table-responsive">
                    <table id="table" class="table table-bordered table-sm text-center table-striped">
                        <thead>
                            <tr class="fs-6" style="vertical-align: middle">
                                <th class="col-1">Order Number</th>
                                <th class="col-5">Buyer</th>
                                <th class="col-2">Payment Method</th>
                                <th class="col-2">Total</th>
                                <th class="col-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        
                        $sql = "SELECT * FROM `order` WHERE status = ? ORDER BY order_id DESC LIMIT ?, 10"; 
                        $stmt = mysqli_prepare($conn, $sql);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "si", $status, $offset_count); 
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='text-truncate'>
                                    <td>" . $row["order_id"] . "</td>
                                    <td style='max-width: 20px; overflow: scroll;'>" . $row["ordered_by"] . "</td>
                                    <td>" . $row["payment_method"] . "</td>
                                    <td>" . $row["total_price"] . "</td>
                                    <td>
                                        <a href='order_details.php?order_id=" . $row["order_id"] . "' class='btn btn-success btn-sm'>Details</a>
                                    </td>
                                  </tr>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <button id="prev" class="btn btn-primary">Previous</button>
                <button id="next" class="btn btn-primary">Next</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>