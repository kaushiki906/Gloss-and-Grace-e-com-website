<?php
include 'connection.php';

$sql = "SELECT img_path FROM images_path where id >= 3";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<img src='" . $row['img_path'] . "' alt='Uploaded Image' style='width:200px; height:auto; margin:10px; border:1px solid #ddd; padding:5px;'>";
    }
} else {
    echo "No images found.";
}

$conn->close();
?>
