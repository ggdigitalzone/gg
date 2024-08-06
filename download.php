<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$file = $_GET['file'];

// Ensure the file is valid and exists
$file_path = 'uploads/' . basename($file);
if (!file_exists($file_path)) {
    die("File not found.");
}

// Verify if the user has purchased the corresponding addon
$sql = "SELECT 1 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        JOIN addons a ON oi.addon_id = a.id 
        WHERE o.user_id = $user_id 
        AND a.addon_file = '$file'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Unauthorized access.");
}

// Serve the file for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit();
