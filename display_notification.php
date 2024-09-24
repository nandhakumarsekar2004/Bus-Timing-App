<?php
// Include the database connection
include 'db_connect.php';

// Fetch all notifications from the database
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <!-- Include Material Icons and Components -->
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
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
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
        .notification {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .notification:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .notification h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .notification p {
            color: #34495e;
            margin-bottom: 10px;
        }
        .notification small {
            color: #7f8c8d;
            font-style: italic;
        }
        .no-notifications {
            text-align: center;
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 30px;
        }

        /* Navigation bar styles */
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
        /* Add padding to the bottom of the container to prevent content from being hidden by the navigation bar */
        .container {
            padding-bottom: 70px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Notifications</h2>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='notification'>";
                echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
                echo "<p>" . htmlspecialchars($row['message']) . "</p>";
                echo "<small>Sent on: " . $row['created_at'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p class='no-notifications'>No notifications found.</p>";
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
            <a href="display_notification.php" class="mdc-bottom-navigation__link active">
                <i class="material-icons">notifications</i>
                <span class="mdc-bottom-navigation__label">Notifications</span>
            </a>
            <a href="admin_login.php" class="mdc-bottom-navigation__link">
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
        // Initialize Material Components
        mdc.autoInit();

        // Add active class to current page
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