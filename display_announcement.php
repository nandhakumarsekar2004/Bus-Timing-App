<?php
session_start();
require_once 'db_connect.php';
$query = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://unpkg.com/material-components-web/dist/material-components-web.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .announcement-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 800px;
            width: 90%;
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #ffffff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .announcement {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .announcement:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .announcement h3 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }
        .announcement p {
            margin: 0.5rem 0;
            font-size: 1rem;
            color: #666;
        }
        .announcement img, .announcement video {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            margin-top: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .announcement video {
            height: auto;
        }
        
        /* New styles for the navigation bar */
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
        /* Add padding to the bottom of the body to prevent content from being hidden by the navigation bar */
        body {
            padding-bottom: 70px;
        }
    </style>
</head>
<body>
    <h2>Latest Announcements</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="announcement-container">
                <div class="announcement">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    <?php if (!empty($row['file_path'])): ?>
                        <?php 
                        $file_extension = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                        if (in_array($file_extension, ['mp4', 'mov', 'avi'])): ?>
                            <video controls>
                                <source src="<?php echo $row['file_path']; ?>" type="video/<?php echo $file_extension; ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <a href="<?php echo $row['file_path']; ?>" data-lightbox="announcement-<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>">
                                <img src="<?php echo $row['file_path']; ?>" alt="Announcement Image">
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color: white; text-align: center;">No announcements available.</p>
    <?php endif; ?>
    
    <div class="mdc-bottom-navigation">
        <div class="mdc-bottom-navigation__container">
            <a href="display_buses.php" class="mdc-bottom-navigation__link">
                <i class="material-icons">directions_bus</i>
                <span class="mdc-bottom-navigation__label">Buses</span>
            </a>
            <a href="display_announcement.php" class="mdc-bottom-navigation__link active">
                <i class="material-icons">announcement</i>
                <span class="mdc-bottom-navigation__label">Announcements</span>
            </a>
            <a href="display_notification.php" class="mdc-bottom-navigation__link">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://unpkg.com/material-components-web/dist/material-components-web.min.js"></script>
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