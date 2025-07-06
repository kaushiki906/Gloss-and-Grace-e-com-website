<?php
session_start();
require_once 'db_connection.php'; // or wherever your DB connection is

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: admin_login.php");
    exit();
}

// Set active section from URL
$activeSection = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

$orders = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");
if (!$orders) {
    echo "Query Error: " . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Gloss & Grace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff1774;
            --primary-hover: #e61368;
            --secondary-color: #2c3e50;
            --secondary-hover: #1a252f;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --border-color: #dee2e6;
            --sidebar-width: 250px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--secondary-color);
            color: white;
            position: fixed;
            height: 100%;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .sidebar-header h2 {
            color: white;
            font-size: 1.5rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            list-style: none;
            margin: 10px 0;
        }

        .sidebar-menu li a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background 0.3s;
        }

        .sidebar-menu li a:hover {
            background: #ff1774;
            color: white;
        }

        .sidebar-menu li.active a {
            background: #ff1774;
            color: white;
            font-weight: bold;
        }


        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 20px;
            transition: all 0.3s;
        }

        /* Topbar Styles */
        .topbar {
            background: white;
            color: var(--dark-color);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px;
        }

        .topbar h1 {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .logout-btn {
            background-color: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 15px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Section Styles */
        .section {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }

        .section.active {
            display: block;
        }

        .section h2 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 23, 116, 0.2);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        /* Image Styles */
        .thumbnail {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid var(--success-color);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--danger-color);
            color: var(--danger-color);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-crown"></i> Gloss & Grace</h2>
        </div>
        <ul class="sidebar-menu">
            <li class="<?php echo $activeSection == 'dashboard' ? 'active' : ''; ?>">
                <a href="admin.php?section=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="<?php echo $activeSection == 'products' ? 'active' : ''; ?>">
                <a href="admin.php?section=products"><i class="fas fa-box-open"></i> Products</a>
            </li>
            <li class="<?php echo $activeSection == 'users' ? 'active' : ''; ?>">
                <a href="admin.php?section=users"><i class="fas fa-users"></i> Users</a>
            </li>
            <li class="<?php echo $activeSection == 'orders' ? 'active' : ''; ?>">
                <a href="admin.php?section=orders"><i class="fas fa-shopping-cart"></i> Orders</a>
            </li>
            <li class="<?php echo $activeSection == 'banners' ? 'active' : ''; ?>">
                <a href="admin.php?section=banners"><i class="fas fa-images"></i> Banners</a>
            </li>
        </ul>
    </div>



    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <h1><i class="fas fa-cog"></i> Admin Panel</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Dashboard Section -->
        <div id="dashboard" class="section <?php echo $activeSection == 'dashboard' ? 'active' : ''; ?>">
            <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
            <div class="row" style="display: flex; gap: 20px; margin-bottom: 20px;">
                <div style="flex: 1; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3><i class="fas fa-box"></i> Total Products</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc();
                    echo "<p style='font-size: 2rem; color: var(--primary-color);'>{$count['total']}</p>";
                    ?>
                </div>
                <div style="flex: 1; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3><i class="fas fa-users"></i> Total Users</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc();
                    echo "<p style='font-size: 2rem; color: var(--primary-color);'>{$count['total']}</p>";
                    ?>
                </div>
                <div style="flex: 1; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <h3><i class="fas fa-shopping-cart"></i> Total Orders</h3>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc();
                    echo "<p style='font-size: 2rem; color: var(--primary-color);'>{$count['total']}</p>";
                    ?>
                </div>
            </div>

            <h3><i class="fas fa-chart-line"></i> Recent Activity</h3>
        </div>

        <!-- Products Section -->
        <div id="products" class="section <?php echo $activeSection == 'products' ? 'active' : ''; ?>">
            <h2><i class="fas fa-box-open"></i> Product Management</h2>

            <?php
            // If editing
            if (isset($_GET['edit_id'])) {
                $edit_id = (int)$_GET['edit_id'];
                $edit_data = $conn->query("SELECT * FROM products WHERE id=$edit_id")->fetch_assoc();
            ?>
                <form method="POST" enctype="multipart/form-data" class="mb-4">
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($edit_data['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($edit_data['price']); ?>" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($edit_data['stock']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" required><?php echo htmlspecialchars($edit_data['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Current Image</label>
                        <img src='uploads/<?php echo htmlspecialchars($edit_data['image']); ?>' class="thumbnail">
                    </div>
                    <div class="form-group">
                        <label>New Image (Leave blank to keep current)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="update_product" class="btn btn-primary"><i class="fas fa-save"></i> Update Product</button>
                    <a href="admin.php?section=products" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
                </form>
            <?php
            } else {
            ?>
                <form method="POST" enctype="multipart/form-data" class="mb-4">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" placeholder="Description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</button>
                </form>
            <?php } ?>

            <?php
            if (isset($_POST['add_product'])) {
                $name = $conn->real_escape_string($_POST['name']);
                $price = (float)$_POST['price'];
                $stock = (int)$_POST['stock'];
                $desc = $conn->real_escape_string($_POST['description']);

                $image_name = $_FILES['image']['name'];
                $image_tmp = $_FILES['image']['tmp_name'];
                $target = "uploads/" . basename($image_name);

                if (move_uploaded_file($image_tmp, $target)) {
                    $conn->query("INSERT INTO products (name, price, stock, description, image) VALUES ('$name', '$price', '$stock', '$desc', '$image_name')");
                    echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Product added successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Image upload failed!</div>";
                }
            }

            if (isset($_POST['update_product'])) {
                $id = (int)$_POST['id'];
                $name = $conn->real_escape_string($_POST['name']);
                $price = (float)$_POST['price'];
                $stock = (int)$_POST['stock'];
                $desc = $conn->real_escape_string($_POST['description']);

                if (!empty($_FILES['image']['name'])) {
                    $image_name = $_FILES['image']['name'];
                    $image_tmp = $_FILES['image']['tmp_name'];
                    move_uploaded_file($image_tmp, "uploads/" . $image_name);
                    $conn->query("UPDATE products SET name='$name', price='$price', stock='$stock', description='$desc', image='$image_name' WHERE id=$id");
                } else {
                    $conn->query("UPDATE products SET name='$name', price='$price', stock='$stock', description='$desc' WHERE id=$id");
                }
                echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Product updated successfully!</div>";
                echo "<script>setTimeout(() => window.location='admin.php?section=products', 1500);</script>";
            }

            if (isset($_POST['delete_id'])) {
                $id = (int)$_POST['delete_id'];
                $conn->query("DELETE FROM products WHERE id=$id");
                echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Product deleted successfully!</div>";
                echo "<script>setTimeout(() => window.location='admin.php?section=products', 1500);</script>";
            }

            $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
            echo "<h3><i class='fas fa-list'></i> All Products</h3>";
            echo "<div style='overflow-x: auto;'>";
            echo "<table>";
            echo "<tr><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Description</th><th>Actions</th></tr>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                <td><img src='uploads/{$row['image']}' class='thumbnail'></td>
                <td>{$row['name']}</td>
                <td>$" . number_format($row['price'], 2) . "</td>
                <td>{$row['stock']}</td>
                <td>" . substr($row['description'], 0, 50) . "...</td>
                <td>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='delete_id' value='{$row['id']}'>
                        <button type='submit' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> Delete</button>
                    </form>
                    <a href='admin.php?edit_id={$row['id']}&section=products' class='btn btn-primary btn-sm'><i class='fas fa-edit'></i> Edit</a>
                </td>
                </tr>";
            }
            echo "</table>";
            echo "</div>";
            ?>
        </div>

        <!-- Users Section -->
        <div id="users" class="section <?php echo $activeSection == 'users' ? 'active' : ''; ?>">
            <h2><i class="fas fa-users"></i> User Management</h2>
            <div style="overflow-x: auto;">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered On</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $users = $conn->query("SELECT * FROM users ORDER BY id DESC");
                    while ($user = $users->fetch_assoc()) {
                        echo "<tr>
                        <td>{$user['id']}</td>
                        <td>{$user['username']}</td>
                        <td>{$user['email']}</td>
                        <td>{$user['created_at']}</td>
                        <td>" . ($user['is_admin'] ? '<span style="color: var(--primary-color);">Admin</span>' : 'User') . "</td>
                        </tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <!-- Orders Section -->

        <div id="orders" class="section <?php echo $activeSection == 'orders' ? 'active' : ''; ?>">
            <h2><i class="fas fa-shopping-cart"></i> Orders Management</h2>
            <div style="overflow-x: auto;">
                <table border="1" cellspacing="0" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Shipping Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $conn->query("SELECT * FROM orders ORDER BY order_id DESC");
                        if (!$orders) {
                            die("Query Error: " . $conn->error);
                        }
                        while ($order = $orders->fetch_assoc()) :
                        ?>
                            <tr>
                                <td><?= $order['order_id'] ?></td>
                                <td><?= $order['user_id'] ?></td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                <td><?= date("d M Y, h:i A", strtotime($order['order_date'])) ?></td>
                                <td>
                                    <span style="background: <?= getStatusColor($order['status']) ?>; color: white; padding: 3px 8px; border-radius: 10px;">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= $order['payment_method'] ?? 'N/A' ?></td>
                                <td><?= substr($order['address'] ?? 'N/A', 0, 30) ?>...</td>
                                <td>
                                    <form onsubmit="updateStatus(event, <?= $order['order_id'] ?>)">
                                        <select name="status" id="status-<?= $order['order_id'] ?>">
                                            <option value="pending" <?= ($order['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                            <option value="completed" <?= ($order['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= ($order['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        // Helper function to get the status color
        function getStatusColor($status)
        {
            switch ($status) {
                case 'pending':
                    return '#ffc107'; // Yellow for pending
                case 'completed':
                    return '#28a745'; // Green for completed
                case 'cancelled':
                    return '#dc3545'; // Red for cancelled
                default:
                    return '#6c757d'; // Default gray for unknown status
            }
        }
        ?>



        <!-- Banners Section -->
        <div id="banners" class="section <?php echo $activeSection == 'banners' ? 'active' : ''; ?>">
            <h2><i class="fas fa-images"></i> Banner Management</h2>

            <form method="POST" enctype="multipart/form-data" class="mb-4">
                <div class="form-group">
                    <label>Upload Banner (Image or Video)</label>
                    <input type="file" name="banner_media" class="form-control" accept="image/*,video/*" required>
                </div>
                <button type="submit" name="upload_banner" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Banner</button>
            </form>

            <?php
            if (isset($_POST['upload_banner'])) {
                $file = $_FILES['banner_media']['name'];
                $tmp = $_FILES['banner_media']['tmp_name'];
                $error = $_FILES['banner_media']['error'];

                if ($error !== UPLOAD_ERR_OK) {
                    echo "<div class='alert alert-danger'>File upload failed. Please try again.</div>";
                } else {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    $type = in_array(strtolower($ext), ['mp4', 'webm', 'ogg']) ? 'video' : 'image';

                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'])) {
                        $upload_dir = "uploads/";
                        $target_file = $upload_dir . basename($file);

                        if (is_writable($upload_dir)) {
                            if (move_uploaded_file($tmp, $target_file)) {
                                $conn->query("INSERT INTO banners (image, type) VALUES ('$file', '$type')");
                                echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Banner uploaded successfully!</div>";
                            } else {
                                echo "<div class='alert alert-danger'>Failed to move uploaded file. Please check file permissions.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>Uploads directory is not writable. Please check the directory permissions.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Invalid file type. Please upload a valid image or video file.</div>";
                    }
                }
            }

            // Show all banners
            $banners = $conn->query("SELECT * FROM banners ORDER BY id DESC");

            if ($banners && $banners->num_rows > 0) {
                echo "<h3><i class='fas fa-list'></i> Current Banners</h3>";
                echo "<div style='overflow-x: auto;'>";
                echo "<table class='table table-bordered'>";
                echo "<tr><th>Preview</th><th>Type</th><th>Actions</th></tr>";

                while ($b = $banners->fetch_assoc()) {
                    echo "<tr><td>";
                    if ($b['type'] == 'video') {
                        echo "<video src='uploads/{$b['image']}' width='200' controls></video>";
                    } else {
                        echo "<img src='uploads/{$b['image']}' width='200'>";
                    }
                    echo "</td><td>{$b['type']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='delete_banner' value='{$b['id']}'>
                            <button type='submit' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i> Delete</button>
                        </form>
                    </td>
                  </tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-warning'>No banners found.</div>";
            }

            // Handle banner deletion
            if (isset($_POST['delete_banner'])) {
                $bid = (int)$_POST['delete_banner'];
                $result = $conn->query("SELECT * FROM banners WHERE id = $bid");
                $banner = $result->fetch_assoc();
                $file_to_delete = "uploads/{$banner['image']}";

                if (file_exists($file_to_delete)) {
                    unlink($file_to_delete); // Delete the file from the server
                }

                $conn->query("DELETE FROM banners WHERE id = $bid");

                echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Banner deleted successfully!</div>";
                echo "<script>setTimeout(() => location.href='admin.php?section=banners', 1500);</script>";
            }
            ?>
        </div>

    </div>

    <script>
        // Define the showSection function in the global scope
        window.showSection = function(id) {
            // Hide all sections
            var sections = document.getElementsByClassName('section');
            for (var i = 0; i < sections.length; i++) {
                sections[i].classList.remove('active');
            }

            // Show the selected section
            var activeSection = document.getElementById(id);
            if (activeSection) {
                activeSection.classList.add('active');
            }

            // Update the URL
            window.history.pushState(null, null, '?section=' + id);

            // Update active menu item
            var menuItems = document.querySelectorAll('.sidebar-menu li');
            menuItems.forEach(function(item) {
                item.classList.remove('active');
                if (item.getAttribute('onclick') && item.getAttribute('onclick').includes("showSection('" + id + "')")) {
                    item.classList.add('active');
                }
            });
        };

        // Initialize the page when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set the initial section from URL or default to dashboard
            var urlParams = new URLSearchParams(window.location.search);
            var section = urlParams.get('section') || 'dashboard';
            showSection(section);

            // Handle browser back/forward buttons
            window.addEventListener('popstate', function() {
                var urlParams = new URLSearchParams(window.location.search);
                var section = urlParams.get('section') || 'dashboard';
                showSection(section);
            });
        });
    </script>

    <script>
        function updateStatus(event, orderId) {
            event.preventDefault(); // prevent form reload

            const status = document.getElementById('status-' + orderId).value;

            fetch('update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `order_id=${orderId}&status=${status}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    alert("Order status updated successfully.");
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Something went wrong!");
                });
        }
    </script>


</body>

</html>