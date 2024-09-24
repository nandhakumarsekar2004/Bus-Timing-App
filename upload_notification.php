<?php
// Include the database connection
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch the form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert notification into the database
    $sql = "INSERT INTO notifications (title, message) VALUES ('$title', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo "Notification added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Notification</title>
</head>
<body>
    <h2>Send Notification</h2>
    <form method="POST" action="upload_notification.php">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="message">Message:</label><br>
        <textarea id="message" name="message" required></textarea><br><br>

        <button type="submit">Send Notification</button>
    </form>
</body>
</html>
