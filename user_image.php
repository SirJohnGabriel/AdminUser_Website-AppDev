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

$defaultImage = 'images/def_profile.jpeg'; 
$userImage = !empty($user['image_bucket_link']) ? htmlspecialchars($user['image_bucket_link']) : $defaultImage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Image</title>
    <link rel="stylesheet" href="css/user_image_style.css">
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="my-info">
            <h2>My Information</h2>
            <p class="logout-link"><a href="user_home.php">Back</a></p>
            <p><b>Welcome:</b> <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
            <img id="preview" src="<?php echo $userImage; ?>" alt="User Image" class="user-image">
            <p><b>User Level:</b> User</p>
            <p><b>Birthday:</b> <?php echo htmlspecialchars(date("F j, Y", strtotime($user['birthday']))); ?></p>
            <p><b>Contact Details:</b></p>
            <div class="contact-details">
                <p><b>Contact No.:</b> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
        <div class="upload-image">
            <h2>Upload Image</h2>
            <form action="upload_image.php" method="post" enctype="multipart/form-data">
                <input type="file" class="upload-image-button" name="image" accept="image/*" onchange="previewImage(event)">
                <button type="submit">Upload Image</button>
            </form>
        </div>
    </div>
</body>
</html>
