<?php
session_start();
include('db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$errors = [];
$product_name = '';
$price = 0;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // Validate product_id
    if ($product_id <= 0) {
        $errors[] = "Invalid product ID.";
    } else {
        // Fetch product details
        $product_sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($product_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product_result = $stmt->get_result();

        if ($product_result->num_rows > 0) {
            $product = $product_result->fetch_assoc();
            $product_name = $product['name'];
        } else {
            $errors[] = "Product not found.";
        }
    }

    // Validate form data
    if (empty($shipping_address)) {
        $errors[] = "Shipping address is required.";
    }
    if (empty($payment_method)) {
        $errors[] = "Payment method is required.";
    }

    if (empty($errors)) {
        // Insert order into the orders table
        $order_sql = "INSERT INTO orders (user_id, shipping_address, payment_method, total_price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("issd", $user_id, $shipping_address, $payment_method, $price);
        if ($stmt->execute()) {
            // Get the last inserted order ID
            $order_id = $stmt->insert_id;

            // Save product to the order_items table
            $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $order_id, $product_id);
            $stmt->execute();

            // Redirect to payment gateway
            $payment_gateway_url = "https://example-payment-gateway.com/checkout?amount=" . urlencode($price) . "&order_id=" . urlencode($order_id);
            header("Location: $payment_gateway_url");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Custom styles -->
</head>
<body>
    <!-- Navbar -->
    <?php include('header.php'); ?>
    
      <!-- Checkout Form -->
      <div class="container mt-5">
        <h2>Checkout</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


<form method="POST" action="">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
    <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">

    <div class="row">
        <div class="col-md-6">
            <h4>Shipping Information</h4>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="shipping_address">Shipping Address</label>
                <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="text" class="form-control" id="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" disabled>
            </div>
        </div>

        <div class="col-md-6">
            <h4>Payment Information</h4>
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="" disabled selected>Select payment method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Order Summary</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($product_name); ?></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="1" class="text-right"><strong>Total:</strong></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-success">Confirm Order</button>
        </div>
    </div>
</form>
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