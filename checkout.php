<?php
include 'function/session_func.php';
require_once 'connect.php';
if (isset($_SESSION['order_complete']) && $_SESSION['order_complete'] === true) {
    unset($_SESSION['order_complete']);
    header("Location: dashboard.php");
    exit();
}

$account_id = $_SESSION['account_id'];
$student_id = $_SESSION['student_id'];
$ordered_by = $_SESSION['user'];
$section = $_SESSION['section'];


$item_id = isset($_GET['item_id']) ? intval($_GET['item_id']) : null;
$checkout_data = $_SESSION['checkout_data'] ?? null;
$total_price = 0;

if ($item_id) {
    $sql_bn = "SELECT i.product_name, d.size, d.price, im.image_path
               FROM `if0_37296747_admin`.`item` i
               JOIN `if0_37296747_admin`.`item_details` d ON i.product_id = d.item_id
               JOIN `if0_37296747_admin`.`item_image` im ON i.product_id = im.item_id
               WHERE i.product_id = ? LIMIT 1";
    $stmt_bn = $conn2->prepare($sql_bn);
    $stmt_bn->bind_param('i', $item_id);
    $stmt_bn->execute();
    $result_bn = $stmt_bn->get_result();

    $item_details = $result_bn->fetch_assoc();

    $size = $_SESSION['checkout_data']['size'] ?? $checkout_data['size'];
    $quantity = $_SESSION['checkout_data']['quantity'] ?? $_;
    $price = $item_details['price'];
    $total_price = $price * $quantity;
    $image_path = $item_details['image_path'];

    $cart_items = [
        [
            'product_name' => $item_details['product_name'],
            'size' => $size,
            'quantity' => $quantity,
            'price' => $price,
            'total_price' => $total_price,
            'image_path' => $image_path
        ]
    ];
} else {
  
    $sql_cart = "SELECT c.id, i.product_name, c.size, c.quantity, c.price, (c.quantity * c.price) AS total_price, im.image_path
                 FROM `if0_37296747_user`.`cart` c
                 JOIN `if0_37296747_admin`.`item` i ON c.item_id = i.product_id
                 JOIN `if0_37296747_admin`.`item_image` im ON i.product_id = im.item_id
                 WHERE c.account_id = ?";
    $stmt_cart = $conn2->prepare($sql_cart);
    $stmt_cart->bind_param('i', $account_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    $cart_items = [];
    while ($row = $result_cart->fetch_assoc()) {
        $cart_items[] = $row;
        $total_price += $row['total_price'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $payment_method = $_POST['payment_method'];
    $gcash_receipt_path = null;

    if ($payment_method === 'gcash' && empty($_FILES['gcash_receipt']['name'])) {
        echo "<div class='alert alert-danger'>Error: You must upload a GCash receipt.</div>";
    } else {
    
        if ($payment_method === 'gcash' && isset($_FILES['gcash_receipt']) && $_FILES['gcash_receipt']['name']) {
            $gcash_receipt = $_FILES['gcash_receipt'];
            $target_dir = "uploads/gcash_receipts/";
            $target_file = $target_dir . basename($gcash_receipt["name"]);

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
 
            if (!in_array($imageFileType, $allowedTypes)) {
                header("Location: gcash_error.php");
                exit;
            }

            $maxFileSize = 10 * 1024 * 1024; // 2 MB

            if ($gcash_receipt["size"] > $maxFileSize) {
                header("Location: gcash_error.php");
                exit;
            }

            if (move_uploaded_file($gcash_receipt["tmp_name"], $target_file)) {
               
                $gcash_receipt_path = $target_file;
            } else {
                header("Location: gcash_error.php");
                exit;
            }
        }

        $sql_order = "INSERT INTO `order` (account_id, student_id, ordered_by, section, total_price, status, ordered_date, payment_method, gcash_receipt) 
                      VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)";
        if ($stmt_order = $conn->prepare($sql_order)) {
            $stmt_order->bind_param("iissdss", $account_id, $student_id, $ordered_by, $section, $total_price, $payment_method, $gcash_receipt_path);

            if ($stmt_order->execute()) {
       
                $order_id = $stmt_order->insert_id;

                foreach ($cart_items as $item) {
                    $sql_order_detail = "INSERT INTO order_detail (order_id, size, quantity, price, product_name) 
                                         VALUES (?, ?, ?, ?, ?)";
                    if ($stmt_detail = $conn->prepare($sql_order_detail)) {
                        $stmt_detail->bind_param("isids", $order_id, $item['size'], $item['quantity'], $item['price'], $item['product_name']);
                        $stmt_detail->execute();
                    }
                }
                $_SESSION['order_complete'] = true;
                $cart_items = [];
            
                if (!$item_id) {
                    $clear_cart_sql = "DELETE FROM `if0_37296747_user`.`cart` WHERE account_id = ?";
                    $stmt_clear_cart = $conn2->prepare($clear_cart_sql);
                    $stmt_clear_cart->bind_param('i', $account_id);
                    $stmt_clear_cart->execute();
                }

                unset($_SESSION['checkout_data']);
                $cart_items = [];
                header("Location: order.php?order_id=" . $order_id);
                exit();
            } else {
                echo "Error creating order: " . $stmt_order->error;
            }
        }
    }
}

if (empty($cart_items)) {
    header("Location: dashboard.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .file,
        #btn {
            cursor: pointer;
        }

        .btn-check:focus+.btn {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.5);
    
        }

        #payment_method_cashier:focus+.btn-warning {
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.5);
        }

        #payment_method_gcash:focus+.btn-primary {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.5);
        }
    </style>
    <script>
        function toggleGcashReceipt() {
            var paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            var gcashReceiptField = document.getElementById("gcash_receipt_field");
            if (paymentMethod === "gcash") {
                gcashReceiptField.style.display = "block";
            } else {
                gcashReceiptField.style.display = "none";
            }
        }
    </script>
</head>

<body class="bg-light">
    <?php include 'header.php' ?>
    <div class="p-3">



    <div class="container-lg mt-4 p-0">
    <button class="btn btn-outline-dark mb-4" onclick="history.back()"><i class="bi bi-arrow-left"></i> back</button>
        <h2>Check Out</h2>
        <h6 class="border shadow-sm p-2 m-0 rounded-top-3 fw-bold text-center mt-4" style="background-color: #FFE10F  !important;  color: #333 !important;">Student Info</h5>
            <div class="col-12 bg-white align-items-center p-2 border shadow-sm">


                <p class="m-0"><b>Name: </b><?php echo htmlspecialchars($_SESSION["user"]) ?></p>
                <p class="m-0"><b>Student ID: </b><?php echo htmlspecialchars($_SESSION["student_id"]) ?></p>
                <p class="m-0"><b>Section: </b><?php echo htmlspecialchars($_SESSION["section"]) ?></p>
                <p class="m-0"><b>Student Email: </b><?php echo htmlspecialchars($_SESSION["student_email"]) ?></p>
            </div>
    </div>

    <div class="container-lg mt-3">
        <div class="row align-items-start justify-content-between">

            <div class="col-sm-6 col-12 p-0 mt-5 mt-sm-0">

                <h6 class="border bg-white shadow-sm p-2 m-0 text-center fw-bold rounded-top-3" style="background-color: #2198f4 !important;  color: #F0F0F0 !important;">Product Ordered</h6>

                <table class="table">

                    <tr class=" bg-white shadow-sm p-2">
                        <th class=" bg-white shadow-sm p-2">Product Name</th>
                        <th class=" bg-white p-2">Size</th>
                        <th class=" bg-white p-2">Quantity</th>
                        <th class=" bg-white  p-2">Price
                    </tr>

                    <?php foreach ($cart_items as $item): ?>
                        <tr class=" bg-white shadow-sm p-2">
                            <td style="vertical-align: middle;" class=" bg-white shadow-sm p-2">
                                <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $row['product_name']; ?>" width="60" height="60">
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </td>

                            <td style="vertical-align: middle;" class=" bg-white  p-2"><?php echo  htmlspecialchars($item['size']); ?></td>
                            <td style="vertical-align: middle;" class=" bg-white p-2"><?php echo  htmlspecialchars($item['quantity']); ?></td>
                            <td style="vertical-align: middle;" class=" bg-white  p-2"><?php echo  htmlspecialchars($item['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>


                </table>
            </div>
            <div class="col-sm-5 col-12 p-0 mt-5 mt-sm-0">
                <h6 class="border bg-white shadow-sm p-2 fw-bold rounded-top-3 m-0 text-center" style="background-color: #2198f4 !important;  color: #F0F0F0 !important;">Payment Details</h6>
                <div class=" p-0 border m-0 ">

                    <form action="checkout.php<?php echo $item_id ? '?item_id=' . $item_id : ''; ?>" method="POST" enctype="multipart/form-data">
                        <div class="bg-white border shadow-sm p-2 py-4 d-flex overflow-scroll">
                            <h6 class="me-2">Payment Option:</h6>

                            <!-- Cashier Option -->
                            <input type="radio" class="btn-check me-2" name="payment_method" id="payment_method_cashier" value="cashier" autocomplete="off" onclick="toggleGcashReceipt()" required>
                            <label class="btn btn-warning  me-2" for="payment_method_cashier">Cashier</label>

                            <!-- GCash Option -->
                            <input type="radio" class="btn-check" name="payment_method" id="payment_method_gcash" value="gcash" autocomplete="off" onclick="toggleGcashReceipt()" required>
                            <label class="btn btn-primary" for="payment_method_gcash">GCash</label>
                        </div>

                        <div id="gcash_receipt_field" style="display:none;" class="mt-2">

                            <label class="border bg-light shadow-sm p-2 my-2 d-flex justify-content-center" for="gcash_receipt" id="btn">Upload GCash Receipt</label>
                            <input type="file" name="gcash_receipt" id="gcash_receipt" class="d-none">

                            <!-- Button trigger modal -->
                            <button type="button" class="small bg-light shadow-0 border-0 text-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                Pay using gcash guide
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Step by Step Gcash Process</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <h5>STEP 1 </h5>
                                                <p>Open Gcash and look for Bills</p>
                                                <img src="uploads/gcash_tutorial//step1.jpg" class="img-fluid" alt="step1" />

                                            </div>
                                            <div class="row">
                                                <h5>STEP 2 </h5>
                                                <p>Navigate to STI</p>
                                                <img src="uploads/gcash_tutorial//step2.jpg" class="img-fluid" alt="step1" />
                                            </div>
                                            <div class="row">
                                                <h5>STEP 3 </h5>
                                                <p>Fill up your information correctly</p>
                                                <img src="uploads/gcash_tutorial//step3.jpg" class="img-fluid" alt="step1" />
                                            </div>
                                            <div class="row">
                                                <h5>STEP 4 </h5>
                                                <p>Screenshot the receipt and upload it here</p>

                                                <img src="uploads/gcash_tutorial//Example_receipt.jpg" class="img-fluid" alt="step1" />
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border bg-white shadow-sm p-2 d-flex justify-content-between align-items-center">
                            <p class="m-0">Total Price: PHP <?php echo number_format($total_price, 2); ?></p>
                            <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>