<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    echo "Access denied.";
    exit;
}

$conn = openConnection();
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

closeConnection($conn);

$defaultImage = 'images/def_profile.jpeg'; 
$userImage = !empty($user['image_bucket_link']) ? htmlspecialchars($user['image_bucket_link']) : $defaultImage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Home</title>
    <link rel="stylesheet" href="css/admin_home_style.css">
</head>
<body>
    <div class="container">
        <div class="my-info">
            <h2>My Information</h2>
            <p class="logout-link"><a href="logout.php">Logout</a></p>
            <p><b>Welcome:</b> <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
            <img src="<?php echo $userImage; ?>" alt="User Image" class="user-image">
            <p><b>User Level:</b> <?php echo htmlspecialchars($user['user_level']); ?></p>
            <p><b>Birthday:</b> <?php echo htmlspecialchars(date("F j, Y", strtotime($user['birthday']))); ?></p>
            <p><b>Contact Details:</b></p>
            <div class="contact-details">
                <p><b>Contact No.:</b> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="my-info-link-container">
                <p><a href="user_image.php">Upload Image</a></p>
                <p><a href="user_changepass.php">Reset My Password</a></p>
            </div>
        </div>
    </div>
</body>
</html>
