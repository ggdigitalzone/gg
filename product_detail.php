<?php
session_start();
include('db_connect.php');

if (!isset($_GET['id'])) {
    echo "Product ID not provided.";
    exit();
}

$product_id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Gaming Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Custom styles -->
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>

    <!-- Product Detail -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars('uploads/' . $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($product['category']); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <h4 class="text-success">$<?php echo number_format($product['price'], 2); ?></h4>
                <form method="POST" action="checkout.php">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <input type="hidden" name="price" value="<?php echo htmlspecialchars($product['price']); ?>">
                    <button type="submit" class="btn btn-primary btn-lg">Buy Now</button>
                </form>
                <a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Gaming Store. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
