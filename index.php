<?php
session_start();
include('db_connect.php'); // Adjust path as needed

// Fetch products
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

// Fetch offers
$offer_sql = "SELECT * FROM offers WHERE NOW() BETWEEN start_date AND end_date";
$offer_result = $conn->query($offer_sql);

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']); // Assuming user_id is set in session when logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Add your custom styles here -->
    <style>
        .slider-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        .card-img-top {
            height: 200px; /* Adjust height as needed */
            object-fit: cover;
        }
        .disabled {
            pointer-events: none;
            opacity: 0.5;
        }
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            text-align: center;
            z-index: 1050;
            padding: 20px;
        }
        .popup-content {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Check if user is not logged in -->
    <?php if (!$isLoggedIn): ?>
        <div class="popup-overlay" id="popupWarning">
            <div class="popup-content">
                <h2>You need to log in!</h2>
                <p>You will be redirected to the login page shortly.</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <?php include('header.php') ?>

    <!-- Slider -->
    <div id="carouselExampleIndicators" class="carousel slide mt-3">
        <ol class="carousel-indicators">
            <?php if ($offer_result->num_rows > 0): ?>
                <?php for ($i = 0; $i < $offer_result->num_rows; $i++): ?>
                    <li data-target="#carouselExampleIndicators" data-slide-to="<?php echo $i; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>"></li>
                <?php endfor; ?>
            <?php endif; ?>
        </ol>
        <div class="carousel-inner">
            <?php if ($offer_result->num_rows > 0): ?>
                <?php $first = true; ?>
                <?php while ($offer = $offer_result->fetch_assoc()): ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <img class="slider-img" src="assets/discount.jpg" alt="<?php echo htmlspecialchars($offer['discount_percent']); ?>">
                        <div class="carousel-caption d-none d-md-block">
                            <h3 style="text-align:left; font-weight:bold; font-size: 50px; font-style: italic;"><?php echo htmlspecialchars($offer['title']); ?></h3>
                            <h3 style="text-align:left; font-weight:bold; font-style: italic;"><?php echo htmlspecialchars($offer['description']); ?></h3>
                            <h4 style="text-align:left; font-weight:bold; font-style: italic; font-size: 15px;"><?php echo htmlspecialchars($offer['discount_percent']); ?> % Discount Available</h4>
                            <h3 style="text-align:right; font-weight:bold; font-style: italic; font-size: 15px;">Start Date: <?php echo htmlspecialchars($offer['start_date']); ?></h3>
                            <h4 style="text-align:right; font-weight:bold; font-style: italic; font-size: 15px;">End Date: <?php echo htmlspecialchars($offer['end_date']); ?></h4>
                        </div>
                    </div>
                    <?php $first = false; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <img class="slider-img" src="assets/default_slider.jpg" alt="Default">
                </div>
            <?php endif; ?>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Products Section -->
    <div class="container mt-5">
        <h2>Available Products</h2>
        <div class="row">
            <?php if ($product_result->num_rows > 0): ?>
                <?php while ($product = $product_result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <img class="card-img-top" src="<?php echo htmlspecialchars('uploads/' . $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                <a href="product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Gaming Store. All rights reserved.</p>
    </footer>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if the popup is displayed
            var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
            if (!isLoggedIn) {
                // Show the popup
                var popup = document.getElementById('popupWarning');
                popup.style.display = 'block';

                // Disable buttons and navbar
                document.querySelectorAll('a, button').forEach(function(el) {
                    el.classList.add('disabled');
                });

                // Start the timer
                setTimeout(function() {
                    // Redirect to login page
                    window.location.href = 'login.php'; // Adjust path as needed
                }, 10000); // 20 seconds
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
