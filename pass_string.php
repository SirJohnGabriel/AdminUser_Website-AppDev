<?php
include 'db_connect.php';

$conn = openConnection();

$users = [
    [
        'first_name' => 'AdminFirstName',
        'last_name' => 'AdminLastName',
        'birthday' => '1990-01-01',
        'contact_number' => '1234567890',
        'email' => 'admin@example.com',
        'username' => 'adminuser',
        'password' => password_hash('AdminPassword123', PASSWORD_BCRYPT),
        'image_bucket_link' => NULL,
        'user_level' => 'admin',
        'status' => 'active'
    ],
    [
        'first_name' => 'InactiveFirstName',
        'last_name' => 'InactiveLastName',
        'birthday' => '1995-05-05',
        'contact_number' => '0987654321',
        'email' => 'inactiveuser@example.com',
        'username' => 'inactiveuser',
        'password' => password_hash('InactivePassword123', PASSWORD_BCRYPT),
        'image_bucket_link' => NULL,
        'user_level' => 'user',
        'status' => 'disabled'
    ],
    [
        'first_name' => 'UserFirstName',
        'last_name' => 'UserLastName',
        'birthday' => '2000-10-10',
        'contact_number' => '1122334455',
        'email' => 'user@example.com',
        'username' => 'activeuser',
        'password' => password_hash('UserPassword123', PASSWORD_BCRYPT),
        'image_bucket_link' => NULL,
        'user_level' => 'user',
        'status' => 'active'
    ]
];

foreach ($users as $user) {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, birthday, contact_number, email, username, password, image_bucket_link, user_level, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssss",
        $user['first_name'],
        $user['last_name'],
        $user['birthday'],
        $user['contact_number'],
        $user['email'],
        $user['username'],
        $user['password'],
        $user['image_bucket_link'],
        $user['user_level'],
        $user['status']
    );

    if ($stmt->execute()) {
        echo "User {$user['username']} registered successfully.<br>";
    } else {
        echo "Error: " . $stmt->error . "<br>";
    }
}

closeConnection($conn);
?>
