<?php
session_start();
require_once 'db_connect.php';

$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and escape inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // Hash the password using MD5

    // Prepare SQL query to check if the user exists
    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the user is found, verify the password
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Compare hashed password with the one in the database
        if ($password == $row['password']) {
            // Password is correct, start session and redirect to admin dashboard
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin'] = $row['username'];


            // Check if headers are already sent
            if (!headers_sent()) {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "Headers already sent, cannot redirect.";
            }
        } else {
            $error_message = "Invalid username or password";
        }
    } else {
        $error_message = "Invalid username or password";
    }
}

if ($error_message) {
    echo $error_message;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://unpkg.com/material-components-web/dist/material-components-web.min.css">
    <script src="https://unpkg.com/material-components-web/dist/material-components-web.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            max-width: 400px;
            width: 90%;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 600;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: 500;
            color: #34495e;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 10px;
        }
        .mdc-bottom-navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .mdc-bottom-navigation__container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 56px;
        }
        .mdc-bottom-navigation__link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #757575;
            transition: color 0.3s ease;
        }
        .mdc-bottom-navigation__link:hover,
        .mdc-bottom-navigation__link.active {
            color: #3498db;
        }
        .mdc-bottom-navigation__link .material-icons {
            font-size: 24px;
            margin-bottom: 4px;
        }
        .mdc-bottom-navigation__label {
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <?php
        if (!empty($error_message)) {
            echo "<p class='error-message'>" . htmlspecialchars($error_message) . "</p>";
        }
        ?>
    </div>

    <div class="mdc-bottom-navigation">
        <div class="mdc-bottom-navigation__container">
            <a href="display_buses.php" class="mdc-bottom-navigation__link">
                <i class="material-icons">directions_bus</i>
                <span class="mdc-bottom-navigation__label">Buses</span>
            </a>
            <a href="display_announcement.php" class="mdc-bottom-navigation__link">
                <i class="material-icons">announcement</i>
                <span class="mdc-bottom-navigation__label">Announcements</span>
            </a>
            <a href="display_notification.php" class="mdc-bottom-navigation__link">
                <i class="material-icons">notifications</i>
                <span class="mdc-bottom-navigation__label">Notifications</span>
            </a>
            <a href="admin_login.php" class="mdc-bottom-navigation__link active">
                <i class="material-icons">admin_panel_settings</i>
                <span class="mdc-bottom-navigation__label">Admin</span>
            </a>
            <a href="contact.php" class="mdc-bottom-navigation__link">
                <i class="material-icons">more_horiz</i>
                <span class="mdc-bottom-navigation__label">More</span>
            </a>
        </div>
    </div>

    <script>
        mdc.autoInit();
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.mdc-bottom-navigation__link');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
