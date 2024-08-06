<?php
session_start();
include('db_connect.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch User Orders with Products and Add-Ons
$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT o.id AS order_id, p.name AS product_name, p.image, p.description, a.addon_name, a.addon_file 
                        FROM orders o 
                        JOIN products p ON o.product_id = p.id 
                        LEFT JOIN addons a ON a.product_id = p.id 
                        WHERE o.user_id = $user_id");
// Only show if status is 'delivered'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Your Downloads</h2>
        <div class="row">
            <?php while ($order = $orders->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="uploads/<?php echo $order['image']; ?>" class="card-img-top" alt="<?php echo $order['product_name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $order['product_name']; ?></h5>
                        <p class="card-text"><?php echo $order['description']; ?></p>
                        <a href="addons/<?php echo $order['addon_file']; ?>" class="btn btn-primary" download>Download Add-On</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
