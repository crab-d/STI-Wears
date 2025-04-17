<?php
include 'function/admin_session_func.php'; 
require_once('connect.php');

$user_id = $_SESSION['admin'];

function getCategories($conn2) {
    $sql = "SELECT category_id, category_name FROM `if0_37296747_admin`.`category`";
    $result = $conn2->query($sql);
    $categories = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    } else {
        echo "Error fetching categories: " . $conn2->error;
    }
    
    return $categories;
}

$categories = getCategories($conn2) ?? [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $sizes = $_POST['sizes'];
    $image_name = $_FILES['item_image']['name'];
    $image_path = 'uploads/' . $image_name;

    $total_stock = 0;
    $min_price = PHP_INT_MAX;

    foreach ($sizes as $size) {
        $total_stock += (int)$size['stock'];
        $min_price = min($min_price, (float)$size['price']);
    }

    $stmt = $conn2->prepare("INSERT INTO item (product_name, stock, price, date, creator, category_id) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("sdisi", $product_name, $total_stock, $min_price, $user_id, $category_id);
    $stmt->execute();
    $item_id = $stmt->insert_id;

    $stmt = $conn2->prepare("INSERT INTO item_details (item_id, size, stock, price) VALUES (?, ?, ?, ?)");
    foreach ($sizes as $size) {
        $stmt->bind_param("isii", $item_id, $size['size'], $size['stock'], $size['price']);
        $stmt->execute();
    }

    if (move_uploaded_file($_FILES['item_image']['tmp_name'], $image_path)) {
        $stmt = $conn2->prepare("INSERT INTO item_image (item_id, image_name, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $item_id, $image_name, $image_path);
        $stmt->execute();
    } 

    header('Location: admin_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add New Item</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .category-btn { margin: 5px; }
        .selected-category { font-weight: bold; }
        .size-selection { margin-bottom: 15px; }
        .card { max-width: 800px; margin: auto; }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<div class="container mt-5">
    <div class="card shadow p-4">
        <h2 class="text-center mb-4">Add New Item</h2>
        <form method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" class="form-control" name="product_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category:</label>
                <div id="category-buttons">
                    <?php foreach ($categories as $category) { ?>
                        <button type="button" class="btn btn-outline-primary category-btn" 
                                data-id="<?= $category['category_id']; ?>">
                            <?= $category['category_name']; ?>
                        </button>
                    <?php } ?>
                </div>
                <input type="hidden" id="selected_category_id" name="category_id" required>
            </div>

            <div id="size-container">
                <div class="row mb-3 size-row">
                    <div class="col-md-4">
            <label class="form-label">Size:</label>
            <input type="text" class="form-control" name="sizes[0][size]" required>
        </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock:</label>
                        <input type="number" class="form-control" name="sizes[0][stock]" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price:</label>
                        <input type="text" class="form-control" name="sizes[0][price]" required>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" id="add-size-btn">+ Add Another Size</button>

            <div class="mb-3 mt-3">
                <label class="form-label">Upload Item Image:</label>
                <input type="file" class="form-control" name="item_image" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>

        </form>
    </div>
</div>

<script>
document.getElementById('category-buttons').addEventListener('click', function (e) {
    if (e.target.classList.contains('category-btn')) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('selected-category'));
        e.target.classList.add('selected-category');
        document.getElementById('selected_category_id').value = e.target.getAttribute('data-id');
    }
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('size-btn')) {
        e.preventDefault();
        if (e.target.classList.contains('disabled')) return;

        e.target.closest('.btn-group').querySelectorAll('.size-btn').forEach(btn => btn.classList.remove('selected'));
        e.target.classList.add('selected');

        const sizeInput = e.target.closest('.btn-group').querySelector('.selected-size');
        sizeInput.value = e.target.getAttribute('data-size');

        document.querySelectorAll('.size-btn').forEach(btn => {
            if (btn.classList.contains('selected')) btn.classList.add('disabled');
            else btn.classList.remove('disabled');
        });
    }
});

document.getElementById('add-size-btn').addEventListener('click', function () {
    const sizeContainer = document.getElementById('size-container');
    const newSizeRow = document.createElement('div');
    newSizeRow.classList.add('row', 'mb-3', 'size-row');
    const index = document.querySelectorAll('.size-row').length;
    newSizeRow.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Size:</label>
            <input type="text" class="form-control" name="sizes[${index}][size]" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Stock:</label>
            <input type="number" class="form-control" name="sizes[${index}][stock]" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Price:</label>
            <input type="text" class="form-control" name="sizes[${index}][price]" required>
        </div>
    `;
    sizeContainer.appendChild(newSizeRow);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
