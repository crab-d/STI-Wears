<?php
include "function/session_func.php";
require_once "connect.php";

$category = null;
$items = [];
$category_id = null;

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);

    $sql = 'SELECT category_name FROM `if0_37296747_admin`.`category` WHERE category_id = ?';
    $stmt = mysqli_prepare($conn2, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $category = mysqli_fetch_assoc($result);

    if ($category) {
        $sql_items = 'SELECT i.product_name, i.stock, i.price, i.product_id, im.image_path
                          FROM item i
                          LEFT JOIN item_image im ON i.product_id = im.item_id
                          WHERE i.category_id = ?';
        $stmt_items = mysqli_prepare($conn2, $sql_items);
        mysqli_stmt_bind_param($stmt_items, 'i', $category_id);
        mysqli_stmt_execute($stmt_items);
        $result_items = mysqli_stmt_get_result($stmt_items);

        while ($row = mysqli_fetch_assoc($result_items)) {
            $items[] = $row;
        }
    } else {
        echo "No category found for ID: " . $category_id . "<br>";
        exit;
    }
} else {
    echo "No valid category ID in URL.<br>";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet" />
</head>

<body class="bg-light justify-content-center">
    <?php include 'header.php'; ?>
<div class="p-3">



    <section id="category-items" class="container-lg mt-4">
    <button class="btn btn-outline-dark" onclick="history.back()"><i class="bi bi-arrow-left"></i> back</button>
        <div class="border shadow-sm p-0 mt-4">
        <p class="m-0 text-center p-1 fw-bold" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important;"><?php echo htmlspecialchars($category['category_name']); ?></p>
        <div class="d-flex flex-wrap col-12 border justify-content-center color-white p-5">
            <?php if (!empty($items)) { ?>
                <?php foreach ($items as $item) { ?>
                    <a href="item.php?id=<?php echo $item['product_id']; ?>" class="card col-2 border m-2" style="min-width: 235px; min-height: 100px; text-decoration: none; color: inherit;">
                        <div class="row m-0 p-0">
                            <div class="col-sm-6 p-0">
                                <img src="<?php echo htmlspecialchars($item['image_path'] ?? 'assets/default_image.png'); ?>" class="border img-fluid h-100 w-100" alt="Item Image">
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
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">No items available in this category.</p>
            <?php } ?>
        </div>
        </div
    </section>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>