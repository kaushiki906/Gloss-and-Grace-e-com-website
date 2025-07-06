<?php
session_start();

// If already logged in, redirect to admin panel
if (isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: admin.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // In a real application, you would verify against database
    $valid_username = 'admin';
    $valid_password = 'password'; // In production, use password_hash()
    
    if ($username === $valid_username && $password === $valid_password) {
        // Set session variables that match admin.php's checks
        $_SESSION['user_id'] = 1; // Should come from DB in real app
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = 1;
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Redirect to admin panel
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Gloss & Grace</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #ff1774;
            margin-bottom: 25px;
        }
        .error-message {
            color: #ff1774;
            margin-bottom: 20px;
            padding: 10px;
            background: #ffebf2;
            border-radius: 5px;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus {
            border-color: #ff1774;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #ff1774;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #e01566;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>