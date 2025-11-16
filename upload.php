<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_POST["user_id"];
    $file = $_FILES["image"];

    // Basic validation
    $allowed = ["image/jpeg", "image/png", "image/jpg"];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($file["type"], $allowed)) {
        die("Invalid file type! Only JPG and PNG allowed.");
    }

    if ($file["size"] > $maxSize) {
        die("File too large! Max size is 2MB.");
    }

    // Upload directory
    $uploadDir = "uploads/";

    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($file["name"]);
    $filePath = $uploadDir . $fileName;

    // Move file to upload folder
    if (move_uploaded_file($file["tmp_name"], $filePath)) {

        // Save path in database
        $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $fileName, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Image uploaded and saved successfully!";
        } else {
            echo "Database update failed!";
        }

        mysqli_stmt_close($stmt);

    } else {
        echo "File upload failed!";
    }
}
?>
