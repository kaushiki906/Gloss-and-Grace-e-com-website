<?php
include 'connection.php';

// Step 1: Delete the file (replace '1' with the file ID to delete)
$file_id_to_delete = 1;  // Change this to the actual file ID you want to delete
$sql_delete = "DELETE FROM images_path WHERE id = $file_id_to_delete";

if ($conn->query($sql_delete) === TRUE) {
    echo "File deleted successfully.<br>";

    // Step 2: Reassign IDs
    // First, initialize the @id variable
    $sql_init_id = "SET @id := 0";
    if ($conn->query($sql_init_id) === TRUE) {
        // Then, update the IDs
        $sql_reassign = "UPDATE images_path SET id = (@id := @id + 1)";
        if ($conn->query($sql_reassign) === TRUE) {
            echo "IDs re-assigned successfully.<br>";
        } else {
            echo "Error re-assigning IDs: " . $conn->error . "<br>";
        }
    } else {
        echo "Error initializing ID variable: " . $conn->error . "<br>";
    }

    // Step 3: Reset the auto-increment value
    $sql_reset_auto_increment = "ALTER TABLE images_path AUTO_INCREMENT = 1";

    if ($conn->query($sql_reset_auto_increment) === TRUE) {
        echo "Auto-increment value reset successfully.<br>";
    } else {
        echo "Error resetting auto-increment: " . $conn->error . "<br>";
    }
} else {
    echo "Error deleting file: " . $conn->error . "<br>";
}

// Close the connection
$conn->close();
?>
