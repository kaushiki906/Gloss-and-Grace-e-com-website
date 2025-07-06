<?php
session_start();
include 'db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: front_page.php");
    exit();
}

// Get current user info
$username = $_SESSION['username'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' LIMIT 1");

if (!$result || mysqli_num_rows($result) === 0) {
    die("User not found!");
}

$user = mysqli_fetch_assoc($result);

// Handle profile update
if (isset($_POST['update'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET username='$new_username', email='$new_email', password='$hashed_password' WHERE username='$username'";
    } else {
        $query = "UPDATE users SET username='$new_username', email='$new_email' WHERE username='$username'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['username'] = $new_username;
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #ffe9f0, #ffffff);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .profile-box {
            background: #fff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 420px;
        }
        .profile-box h2 {
            color: #ff1774;
            text-align: center;
            margin-bottom: 25px;
        }
        form label {
            display: block;
            margin: 10px 0 6px;
            font-weight: bold;
            color: #333;
        }
        form input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
        }
        form button {
            margin-top: 20px;
            width: 100%;
            background: #ff1774;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background: #e31367;
        }
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            background: #ddd;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: black;
            font-size: 14px;
            text-align: center;
        }
        .back-btn:hover {
            background: #ccc;
        }
    </style>
</head>
<body>
    <div class="profile-box">
        <h2>Edit Profile</h2>
        <form method="POST">
            <label>Username</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>New Password (leave blank to keep old)</label>
            <input type="password" name="password" placeholder="Enter new password (optional)">

            <button type="submit" name="update">Update Profile</button>
        </form>
        <a href="front_page.php" class="back-btn">← Back to Home</a>
    </div>
</body>
</html>
