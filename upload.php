<?php
include 'connection.php';

// Check if form is submitted
if(isset($_POST["submit"])) {
    // Check if file is uploaded correctly
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] != UPLOAD_ERR_OK) {
        die("Error: No file uploaded or an upload error occurred.");
    }

    $target_dir = "assets/"; // Ensure this directory exists inside your project
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if it doesn’t exist
    }

    $image_name = basename($_FILES["file"]["name"]);
    $image_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $image_name); // Sanitize filename
    $target_file = $target_dir . $image_name;

    // Allowed file types
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = array("jpg", "jpeg", "png", "gif", "mp4", "avif");

    // Validate if file is an image or allowed video type
    $check = @getimagesize($_FILES["file"]["tmp_name"]);
    if ($check === false && !in_array($imageFileType, ["mp4", "avif"])) {
        die("Error: File is not a valid image or video.");
    }

    // Validate file size (Max: 5MB)
    if ($_FILES["file"]["size"] > 5 * 1024 * 1024) {
        die("Error: File size exceeds 5MB limit.");
    }

    // Move uploaded file
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Use prepared statement for security
            $stmt = $conn->prepare("INSERT INTO images_path (img_path) VALUES (?)");
            $stmt->bind_param("s", $target_file);

            if ($stmt->execute()) {
                echo "Image uploaded successfully! <a href='display.html'>View Images</a>";
            } else {
                echo "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: File upload failed.";
        }
    } else {
        echo "Error: Only JPG, JPEG, PNG, GIF, MP4 & AVIF files are allowed.";
    }
}

$conn->close();
?>
