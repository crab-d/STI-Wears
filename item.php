<?php
include 'function/session_func.php';
require_once('connect.php');


$account_id = $_SESSION['account_id'];
$student_id = $_SESSION['student_id'];
$ordered_by = $_SESSION['user'];
$section = $_SESSION['section'];

$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id > 0) {

    $sql = "SELECT i.product_name, d.size, d.stock, d.price, im.image_path
            FROM if0_37296747_admin.item i
            JOIN if0_37296747_admin.item_details d ON i.product_id = d.item_id
            JOIN if0_37296747_admin.item_image im ON i.product_id = im.item_id
            WHERE i.product_id = ?";

    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sizes = [];
        $image_path = '';
        $item_name = '';

        while ($row = $result->fetch_assoc()) {
            $item_name = $row['product_name'];
            $sizes[] = $row;
            if (empty($image_path)) {
                $image_path = $row['image_path'];
            }
        }
    } else {
        echo "No item details found.";
        exit;
    }
} else {
    echo "Invalid item ID.";
    exit;
}

$sql_cart = "SELECT * FROM if0_37296747_user.cart WHERE account_id = ?";
$stmt_cart = $conn->prepare($sql_cart);
$stmt_cart->bind_param("i", $account_id);
$stmt_cart->execute();
$result = $stmt_cart->get_result();

$cart_counter = 0;
while ($row = $result->fetch_assoc()) {
    $cart_counter++;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .size-btn.selected {
            background-color: #007bff;
            color: white;
        }

        .color-blue {
            background-color: #ffcc14 !important;
            color: #444 !important;
            font-weight: bold !important;
        }

        .color-yellow {
            background-color: #FFE10F !important color: #0040b0 !important font-weight: bold !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'header.php'; ?>


    <div id="cartNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2" style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-yellow rounded-top-3">
                <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>

            </div>

            <div id="message" class="toast-body p-2">
                Item added to cart successfully
            </div>
        </div>
    </div>


    <div id="quantityNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2"
        style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-yellow rounded-top-3">
                <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>


            </div>

            <div id="message" class="toast-body p-2">
                order quantity must not be greater than 5
            </div>
        </div>
    </div>

    <div id="sizeNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2" style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-yellow rounded-top-3">
                <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>


            </div>

            <div id="message" class="toast-body p-2">
                Please make sure to select an item size
            </div>
        </div>
    </div>


    <div id="cartSizeNotif" class="message-container position-fixed bottom-0 end-0 p-3 rounded-2"
        style="display: none;">
        <div class="bg-white border shadow-sm rounded-3">
            <div class="toast-header border-bottom p-2 color-yellow rounded-top-3">
                <strong class="me-auto"><i class="bi bi-bell-fill"></i>Notification</strong>


            </div>

            <div id="message" class="toast-body p-2">
                Cart exceeded to maximum item limit.
            </div>
        </div>
    </div>
    <div class="p-3">

        <div class="container-lg mt-4">
            <button class="btn btn-outline-dark" onclick="history.back()"><i class="bi bi-arrow-left"></i> back</button>

            <div class="row bg-white border shadow-sm mt-4 p-3">
                <div class="col-sm-6 d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product Image"
                        class="img-fluid border shadow-sm h-100 w-100">
                </div>
                <div class="col-sm-6 m-sm-0 mt-4">
                    <h2 class="text-dark"><?php echo $item_name; ?></h2>
                    <form id="order-form" method="POST">
                        <input type="hidden" name="price" id="price-input">
                        <input type="hidden" name="size" id="size-input" required>
                        <input type="hidden" name="action" id="action-input">
                        <hr>

                        <div class="mb-3 lh-1 m-0">
                            <div class="d-flex justify-content-between">
                                <p class="text-secondary"><strong>Select Size : </strong></p>
                                <p class="text-muted text-end"><i></i></p>
                                <!-- Button trigger modal -->
                                <button type="button" class=" p-6 btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal">
                                    size guide
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <img src="./uploads/guide/sizechart.png" alt="Size Guide"
                                                    class="img-fluid">
                                                <img src="./uploads/guide/guide.png" alt="Size Guide" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php foreach ($sizes as $size): ?>
                                <?php if ($size['stock'] > 0) { ?>
                                    <button type="button" class="btn btn-outline-primary m-1 size-btn"
                                        data-size="<?php echo htmlspecialchars($size['size']); ?>"
                                        data-price="<?php echo htmlspecialchars($size['price']); ?>"
                                        data-stock="<?php echo htmlspecialchars($size['stock']); ?>">
                                        <?php echo strtoupper(htmlspecialchars($size['size'])); ?>
                                    </button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-outline-dark m-1 size-btn"
                                        data-size="<?php echo htmlspecialchars($size['size']); ?>"
                                        data-price="<?php echo htmlspecialchars($size['price']); ?>"
                                        data-stock="<?php echo htmlspecialchars($size['stock']); ?>" disabled>
                                        <?php echo strtoupper(htmlspecialchars($size['size'])); ?>
                                    </button>
                                <?php } ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <p class="lh-1 m-0 text-secondary"><strong>Price :</strong> PHP <span
                                    id="price-display">0.00</span></p>
                            <p class="lh-1 m-0 text-secondary"><strong>Stock Available :</strong> <span
                                    id="stock-display">0</span></p>
                        </div>
                        <hr>


                        <!-- HIDE nA Description SABI NG PANEL  -->
                        <!-- <P class="text-secondary"><strong>Product Description </strong></P>

                        <p aria-disabled="true">
                            <span class="placeholder col-6" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-1" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                        </p>
                        <p aria-disabled="true">
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                            <span class="placeholder col-4" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-3" style="cursor: default !important;"></span>
                            <span class="placeholder col-2" style="cursor: default !important;"></span>
                            <span class="placeholder col-5" style="cursor: default !important;"></span>
                        </p>
                        <hr> -->

                        <div class="mb-3">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1"
                                max="5" required>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="add-to-cart">Add to Cart</button>
                            <button type="button" class="btn btn-success" id="place-order">Order Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const sizeButtons = document.querySelectorAll('.size-btn');
        const priceDisplay = document.getElementById('price-display');
        const stockDisplay = document.getElementById('stock-display'); let cartSize = <?php echo json_encode($cart_counter); ?>;
        const priceInput = document.getElementById('price-input');
        const sizeInput = document.getElementById('size-input');

        sizeButtons.forEach(button => {
            button.addEventListener('click', function () {
                sizeButtons.forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');

                const size = this.getAttribute('data-size');
                const price = this.getAttribute('data-price');
                const stock = this.getAttribute('data-stock');

                priceDisplay.textContent = `${price}`;
                stockDisplay.textContent = stock;
                priceInput.value = price;
                sizeInput.value = size;
            });
        });

        // add to cart
        $('#add-to-cart').on('click', function () {
            const size = sizeInput.value;
            const price = priceInput.value;
            const quantity = $('#quantity').val();

            const cartNotif = $("#cartNotif");
            const quantityNotif = $('#quantityNotif');
            const sizeNotif = $('#sizeNotif');
            const cartSizeNotif = $('#cartSizeNotif');



            // Validation
            if (!size || quantity <= 0) {
                sizeNotif
                    .show();
                hideMessageAfterDelay(sizeNotif);
                return;
            }

            if (quantity > 5) {
                quantityNotif
                    .show();
                hideMessageAfterDelay(quantityNotif);
                return;
            }

            if (cartSize >= 5) {
                cartSizeNotif
                    .show();
                hideMessageAfterDelay(cartSizeNotif);
                return;
            }

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: {
                    action: 'add_to_cart',
                    item_id: '<?php echo $item_id; ?>',
                    account_id: '<?php echo $account_id; ?>',
                    size: size,
                    quantity: quantity,
                    price: price
                },
                success: function (response) {

                    cartNotif
                        //.find("#message").html('Item successfully added to cart!')
                        .show();
                    hideMessageAfterDelay(cartNotif);
                    cartSize += 1;
                },
                error: function (xhr, status, error) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('Failed to add item to cart: ' + error)
                        .show();
                    hideMessageAfterDelay(messageContainer);
                }
            });
        });

        // if buy now click
        $('#place-order').on('click', function () {
            const size = sizeInput.value;
            const price = priceInput.value;
            const quantity = $('#quantity').val();
            const cartNotif = $("#cartNotif")
            const quantityNotif = $('#quantityNotif');
            const sizeNotif = $('#sizeNotif');


            if (!size || quantity <= 0) {
                sizeNotif
                    .show();
                hideMessageAfterDelay(sizeNotif);
                return;
            }

            if (quantity > 5) {
                quantityNotif
                    .show();
                hideMessageAfterDelay(quantityNotif);
                return;
            }

            $.ajax({
                url: 'checkout_data.php',
                method: 'POST',
                data: {
                    item_id: '<?php echo $item_id; ?>',
                    size: size,
                    quantity: quantity,
                    price: price
                },
                success: function (response) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('test ' + size + quantity)
                        .show
                    hideMessageAfterDelay(messageContainer);

                    //window.location.href = 'checkout.php';
                },
                error: function (xhr, status, error) {
                    messageContainer
                        .removeClass()
                        .addClass('alert alert-danger')
                        .html('Failed to set checkout data: ' + error)
                        .show();
                    hideMessageAfterDelay(messageContainer);
                }
            });

            // go to order details
            window.location.href = `checkout.php?item_id=<?php echo $item_id; ?>&size=${size}&quantity=${quantity}&price=${price}`;
        });

        function hideMessageAfterDelay(container) {
            setTimeout(() => {
                container.fadeOut();
            }, 3000);
        }
    </script>
</body>

</html>