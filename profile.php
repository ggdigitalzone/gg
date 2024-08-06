<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found";
    exit();
}

// Handle profile photo upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        $sql = "UPDATE users SET profile_photo = '$target_file' WHERE id = $user_id";
        if ($conn->query($sql) === TRUE) {
            header("Location: profile.php");
        } else {
            echo "Error updating profile photo.";
        }
    } else {
        echo "Error uploading file.";
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $youtube_id = $_POST['youtube_id'];
    $country = $_POST['country'];
    $age = $_POST['age'];

    $sql = "UPDATE users SET name = '$name', mobile = '$mobile', email = '$email', address = '$address', youtube_id = '$youtube_id', country = '$country', age = $age WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully.";
        header("Location: profile.php");
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Handle account deletion
if (isset($_GET['delete_account'])) {
    $sql = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        session_destroy();
        header("Location: index.php");
    } else {
        echo "Error deleting account: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css"> <!-- Add your custom styles here -->
</head>
<body>
    <div class="container mt-5">
        <h2>My Profile</h2>
        <div class="row">
            <div class="col-md-4">
                <h4>User Information</h4>
                <img src="<?php echo $user['profile_photo'] ? $user['profile_photo'] : 'assets/default_profile.png'; ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_photo">Change Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control-file">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Photo</button>
                </form>
                <hr>
                <form method="POST">
                    <h4>Update Profile</h4>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" class="form-control" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="youtube_id">YouTube ID</label>
                        <input type="text" name="youtube_id" class="form-control" value="<?php echo htmlspecialchars($user['youtube_id']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($user['country']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                </form>
                <hr>
                <a href="profile.php?delete_account=true" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
