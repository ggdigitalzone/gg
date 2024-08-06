<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // Basic input validation (you can extend this)
    if (!empty($name) && !empty($email) && !empty($message)) {
        $sql = "INSERT INTO contact_queries (name, email, message) VALUES ('$name', '$email', '$message')";
        
        if ($conn->query($sql) === TRUE) {
            $success_message = "Thank you for your message. We'll get back to you soon!";
        } else {
            $error_message = "There was an error submitting your message. Please try again.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Custom Styles -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <style>
        /* Additional custom styles */
        .container {
            margin-top: 50px;
            max-width: 800px;
        }

        .map-container {
            height: 400px;
            width: 100%;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Gaming Store</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="client-side.php">client</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
            
        </div>
    </nav>
     <div class="container">
        <h2>Contact Us</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>

        <div class="map-container" id="map"></div>
    </div>

    <script>
        function initMap() {
            // Set the location (Latitude, Longitude)
            var location = {lat: 40.712776, lng: -74.005974}; // Example: New York City
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: location
            });
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
        }

        // Initialize the map
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
</body>
</html>
