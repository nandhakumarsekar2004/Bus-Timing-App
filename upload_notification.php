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
    <style>
        /* General page background */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #acb6e5 100%);
            padding: 50px;
        }

        /* Main form container */
        form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            margin: 0 auto;
            border: 2px solid #6c63ff;
        }

        /* Form header */
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Label styles */
        label {
            font-weight: bold;
            color: #333;
        }

        /* Input and Textarea styling */
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            display: inline-block;
            border: 2px solid #6c63ff;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #f0f0f0;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Submit button styling */
        button {
            width: 100%;
            background-color: #6c63ff;
            color: white;
            padding: 14px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #5a54d4;
        }

        /* Add a border box for the form section */
        form {
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            border: 2px solid #6c63ff;
        }

        /* Hover effect for inputs */
        input[type="text"]:focus,
        textarea:focus {
            border-color: #74ebd5;
            outline: none;
        }
</style>
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
