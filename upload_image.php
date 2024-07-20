<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $username = $_SESSION['username'];
    $uploadDir = 'images/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        $_SESSION['feedback'] = "File is not an image.";
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $_SESSION['feedback'] = "Sorry, your file is too large.";
    } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $_SESSION['feedback'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    } elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        $conn = openConnection();
        $query = "UPDATE users SET image_bucket_link = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $uploadFile, $username);
        if ($stmt->execute()) {
            $_SESSION['feedback'] = "The file ". htmlspecialchars(basename($_FILES['image']['name'])) ." has been uploaded.";
        } else {
            $_SESSION['feedback'] = "Error updating image in the database: " . $stmt->error;
        }
        closeConnection($conn);
    } else {
        $_SESSION['feedback'] = "Sorry, there was an error uploading your file.";
    }
} else {
    $_SESSION['feedback'] = "No file was uploaded.";
}

if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 'admin') {
    header("Location: admin_home.php");
} else {
    header("Location: user_home.php");
}
exit;
?>
