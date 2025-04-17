<?php
include "function/session_func.php";
require_once "connect.php";
$_SESSION["order_complete"] = false;

$sql_cancel_orders = "SELECT order_id 
                      FROM `if0_37296747_user`.`order` 
                      WHERE ordered_date < (CURRENT_TIMESTAMP - INTERVAL 24 HOUR) 
                      AND status = 'pending' 
                      AND payment_method = 'cashier'";
$stmt_cancel_orders = $conn2->prepare($sql_cancel_orders);
$stmt_cancel_orders->execute();
$result_cancel_orders = $stmt_cancel_orders->get_result();

while ($row = $result_cancel_orders->fetch_assoc()) {
    $order_id = $row['order_id'];
    $sql_update_status = "UPDATE `if0_37296747_user`.`order` SET status = 'cancelled' WHERE order_id = ?";
    $stmt_update_status = $conn2->prepare($sql_update_status);
    $stmt_update_status->bind_param('i', $order_id);
    $stmt_update_status->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
    <link href="style.css" rel="stylesheet" />
<style>
 .category {
     transition : all 0.5s !important;
}
 .category:hover {
 background-color: #14A2D8 !important;
color: #ffffff !important;
}
</style>
</head>

<body class="bg-light justify-content-center">
    <?php include 'header.php'; ?>

    <div class="p-3">
        <section id="new-item" class=" justify-content-center d-flex">
            <div class="container-lg row p-2 m-0 d-flex justify-content-center ">

                <?php

                $sql1 = 'SELECT * FROM `if0_37296747_admin`.`department`';
                $result1 = mysqli_query($conn2, $sql1);
                $departments = mysqli_fetch_all($result1, MYSQLI_ASSOC);
                ?>

                <section class="justify-content-center d-flex m-0 p-0 mb-3">
                    <div class="container-lg row p-0 m-0 d-flex justify-content-center mt-md-5 mt-3">
                        <?php foreach ($departments as $department) { ?>

                            <div class="col-sm-4 row d-flex justify-content-center m-0 p-1 ">
                                <p class="color-yellow m-0 p-0 text-center border border-primary" style="height: 25px;"><?php echo htmlspecialchars($department['department_name']); ?></p>

                                <div class="row border d-flex justify-content-between p-2 color-white border h-100 ">
                                    <?php
                               
                                    $sql2 = 'SELECT * FROM `if0_37296747_admin`.`category` WHERE `department_id` = ?';
                                    $stmt = mysqli_prepare($conn2, $sql2);
                                    mysqli_stmt_bind_param($stmt, 'i', $department['department_id']);
                                    mysqli_stmt_execute($stmt);
                                    $result2 = mysqli_stmt_get_result($stmt);
                                    $categories = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                                    ?>

                                    <?php if (!empty($categories)) { ?>
                                        <?php foreach ($categories as $category) { ?>
                                            <a href="category.php?category_id=<?php echo urlencode($category['category_id']); ?>" style="vertical-align: middle; height: auto; display: inline-block;" class="category col-md-5 border my-2 bg-light text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($category['category_name']) ?>
                                            </a>

                                        <?php } ?>
                                    <?php } else { ?>
                                        <p class="text-center">No categories found for this department.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>

                <hr class="mt-4">

              

                <div class="bg-white m-0 p-0 rounded-4 border shadow-sm overflow-hidden">
                    <p class="m-0 text-center p-1" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important;">New Item</p>

                    <div class="d-flex my-2 flex-wrap col-12  justify-content-center color-white">
                        <?php
                        // SQL query to get product details along with their image paths from item_image
                        $sql3 = 'SELECT i.product_name, i.stock, i.price, i.product_id, im.image_path 
                            FROM item i 
                            LEFT JOIN item_image im ON i.product_id = im.item_id
                            ORDER BY `product_id` DESC';
                        $result3 = mysqli_query($conn2, $sql3);
                        $items = mysqli_fetch_all($result3, MYSQLI_ASSOC);
                        ?>

                        <?php
                        $item_count = 0;

                        foreach ($items as $item) {
                            if ($item_count <= 5) { ?>

                                <a href="item.php?id=<?php echo $item['product_id']; ?>" class="card col-2 border m-2" style="min-width: 235px; min-height: 100px; text-decoration: none; color: inherit;">
                                    <div class="row m-0  p-0">
                                        <div class="col-sm-6 p-0">

                                            <img src="<?php echo htmlspecialchars($item['image_path'] ?? 'assets/default_image.png'); ?>" class="border h-100 w-100 img-fluid" alt="Item Image">
                                        </div>
                                        <div class="col-sm-6 d-flex align-items-center border justify-content-center p-0 m-0 shadow-sm">
                                            <div class="card-body px-2 p-0">
                                                <h6 class="lh-1 fs-md-1 my-2" style="vertical-align: middle;"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                <hr class="m-0">
                                                <p class="lh-1 text-grey small p-0 my-2" style="">Stock: <?php echo htmlspecialchars($item['stock']); ?></p>
                                                <p class="small lh-1 text-grey p-0 my-2">PHP <?php echo htmlspecialchars($item['price']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                        <?php
                                $item_count++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
    </div>
    </section>


    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>