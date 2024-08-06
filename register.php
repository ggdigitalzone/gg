<?php
session_start();
include('db_connect.php');

$errors = [];
$success = false;
$new_user_id = null;
$new_username = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $youtube_id = trim($_POST['youtube_id']);
    $country = trim($_POST['country']);
    $age = trim($_POST['age']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate input
    if (empty($name) || empty($mobile) || empty($email) || empty($address) || empty($country) || empty($age) || empty($password) || empty($confirm_password)) {
        $errors[] = 'All fields are required.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    // Check if email or mobile already exists
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE email = ? OR mobile = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $email, $mobile);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'Email or mobile number already registered.';
        }
    }

    if (empty($errors)) {
        $username = strtolower(explode('@', $email)[0]) . rand(1000, 9999);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, name, mobile, email, address, youtube_id, country, age)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssi', $username, $hashed_password, $name, $mobile, $email, $address, $youtube_id, $country, $age);

        if ($stmt->execute()) {
            $success = true;
            $new_user_id = $stmt->insert_id;
            $new_username = $username;
        } else {
            $errors[] = 'An error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .popup.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mobile</label>
                <input type="text" name="mobile" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>YouTube ID</label>
                <input type="text" name="youtube_id" class="form-control">
            </div>
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <p>IF HAVE ACCOUNT <a href="login.php">Login Now</a></p>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <!-- Popup -->
    <div id="popup" class="popup">
        <h4>Registration Successful!</h4>
        <p>Your username: <span id="popup-username"><?php echo htmlspecialchars($new_username); ?></span></p>
        <p>Your user ID: <span id="popup-user-id"><?php echo htmlspecialchars($new_user_id); ?></span></p>
        <button id="popup-close" class="btn btn-secondary">Close</button>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('popup');
            const popupClose = document.getElementById('popup-close');

            <?php if ($success): ?>
                popup.classList.add('active');
            <?php endif; ?>

            popupClose.addEventListener('click', function() {
                popup.classList.remove('active');
            });
        });
    </script>
</body>
</html>
