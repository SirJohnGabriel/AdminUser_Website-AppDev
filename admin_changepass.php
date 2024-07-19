<?php
session_start();
include 'db_connect.php';

// Ensure the user is authenticated and is an admin
if (!isset($_SESSION['username']) || $_SESSION['user_level'] != 'admin') {
    echo "Access denied.";
    exit;
}

// Fetch user details from the database
$conn = openConnection();
$username = $_SESSION['username'];
$query = "SELECT first_name, last_name, birthday, contact_number, email FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
closeConnection($conn);

$feedback = "";

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        $feedback = "New passwords do not match.";
    } else {
        // Verify current password
        $conn = openConnection();
        $query = "SELECT password FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $userPassword = $result->fetch_assoc()['password'];
        if (password_verify($currentPassword, $userPassword)) {
            // Update password
            $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $newPasswordHash, $username);
            if ($stmt->execute()) {
                $feedback = "Password updated successfully.";
            } else {
                $feedback = "Error updating password: " . $stmt->error;
            }
        } else {
            $feedback = "Current password is incorrect.";
        }
        closeConnection($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Change Password</title>
    <link rel="stylesheet" href="css/admin_changepass_style.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const feedbackElement = document.querySelector('.feedback');
            if (feedbackElement) {
                // Remove the class to restart the animation
                feedbackElement.classList.remove('fade-animation');
                // Force reflow to restart the animation
                void feedbackElement.offsetWidth;
                // Reapply the class to trigger the animation
                feedbackElement.classList.add('fade-animation');
            }
        });
    </script>
</head>
<body>
    <div class="form-card">
        <p><b>Welcome:</b> <?php echo htmlspecialchars($username); ?></p>
        <a class="back" href="admin_home.php">Back</a>
        <p><b>User Level:</b> Admin</p>
        <p><b>Birthday:</b> <?php echo htmlspecialchars(date("F j, Y", strtotime($user['birthday']))); ?></p>
        <p><b>Contact Details:</b></p>
        <div class="contact-details">
            <p><b>Contact No.:</b> <?php echo htmlspecialchars($user['contact_number']); ?></p>
            <p><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <form action="admin_changepass.php" method="POST">
            <label for="current_password">Enter Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>
    
            <label for="new_password">Enter New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
    
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
    
            <button type="submit">Change Password</button>
        </form>
        <?php if (!empty($feedback)) : ?>
            <p class="feedback"><?php echo htmlspecialchars($feedback); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
