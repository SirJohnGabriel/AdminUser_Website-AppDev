<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['user_level'] != 'admin') {
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

$usersQuery = "SELECT id, first_name, last_name, contact_number, email, birthday, username, user_level, status FROM users";
$usersResult = $conn->query($usersQuery);

closeConnection($conn);

$defaultImage = 'images/def_profile.jpeg'; 
$userImage = !empty($user['image_bucket_link']) ? htmlspecialchars($user['image_bucket_link']) : $defaultImage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Home</title>
    <link rel="stylesheet" href="css/admin_home_style.css">
</head>
<body>
    <div class="container">
        <div class="my-info">
            <h2>My Information</h2>
            <p class="logout-link"><a href="logout.php">Logout</a></p>
            <p><b>Welcome:</b> <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?></p>
            <img src="<?php echo $userImage; ?>" alt="User Image" class="user-image">
            <p><b>User Level:</b> Admin</p>
            <p><b>Birthday:</b> <?php echo htmlspecialchars(date("F j, Y", strtotime($user['birthday']))); ?></p>
            <p><b>Contact Details:</b></p>
            <div class="contact-details">
                <p><b>Contact No.:</b> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="my-info-link-container">
                <p><a href="admin_image.php">Upload Image</a></p>
                <p><a href="admin_changepass.php">Reset My Password</a></p>
            </div>
        </div>
        <div class="records">
            <h2>Records</h2>
            <p><a href="admin_adduser.php">Add New User</a></p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Birthday</th>
                        <th>Username</th>
                        <th>Access Level</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $usersResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars(date("F j, Y", strtotime($row['birthday']))); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_level']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
