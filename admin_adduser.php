<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['user_level'] != 'admin') {
    echo "Access denied.";
    exit;
}

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $contactNumber = $_POST['contact_number'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userLevel = $_POST['user_level'];
    $status = isset($_POST['status']) ? $_POST['status'] : 'active';

    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password)) {
        $feedback = "Password must be at least 8 characters long and contain at least one uppercase letter.";
    } else {
        $conn = openConnection();

        $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $feedback = "Username or email already exists.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $insertQuery = "INSERT INTO users (first_name, last_name, contact_number, email, birthday, username, password, user_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("sssssssss", $firstName, $lastName, $contactNumber, $email, $birthday, $username, $passwordHash, $userLevel, $status);

            if ($stmt->execute()) {
                $feedback = "User added successfully.";
            } else {
                $feedback = "Error adding user: " . $stmt->error;
            }
        }
        closeConnection($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="css/admin_adduser_style.css">
</head>
<body>
    <form action="admin_adduser.php" method="POST">
        <h2>Add New User</h2>
        <a class="back" href="admin_home.php">Back</a>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>
        
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>
        
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" required>
        
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required>
        
        <label for="birthday">Birthday:</label>
        <input type="date" id="birthday" name="birthday" required>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <label for="user_level">User Level:</label>
        <select id="user_level" name="user_level" required>
            <option value="admin">Admin</option>
            <option value="user">User</option>
        </select>
        
        <label>Status:</label>
        <div class="status-group">
            <input type="radio" id="active" name="status" value="active" checked>
            <label for="active">Active</label>
            <input type="radio" id="disabled" name="status" value="disabled">
            <label for="disabled">Disabled</label>
        </div>
        
        <button type="submit">Add User</button>

        <?php if (!empty($feedback)) : ?>
            <p class="error-message"><?php echo htmlspecialchars($feedback); ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
