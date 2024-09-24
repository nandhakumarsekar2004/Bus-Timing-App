<?php
session_start();
require_once 'db_connect.php'; // Ensure this file connects to your database

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch recent bus records, announcements, and notifications
$recent_buses_query = "SELECT * FROM buses ORDER BY created_at DESC LIMIT 5";
$recent_announcements_query = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5";
$recent_notifications_query = "SELECT * FROM notifications ORDER BY created_at DESC LIMIT 5";

// Fetch contact data
$contact_data_query = "SELECT * FROM contact_data ORDER BY created_at DESC LIMIT 10";

$recent_buses = $conn->query($recent_buses_query);
$recent_announcements = $conn->query($recent_announcements_query);
$recent_notifications = $conn->query($recent_notifications_query);
$contact_data = $conn->query($contact_data_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bus Timing App</title>
    <style>
      /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background-image: url('https://images.unsplash.com/photo-1485470733090-0aae1788d5af?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1517&q=80');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .dashboard-menu {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .dashboard-menu a {
            display: inline-block;
            padding: 15px 30px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .dashboard-menu a:hover {
            background-color: #0056b3;
        }

        .section {
            margin-bottom: 30px;
        }

        .recent-items {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .recent-items img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
        }

        .recent-items a {
            text-decoration: none;
            color: #007BFF;
            margin-right: 10px;
        }

        .recent-items a:hover {
            text-decoration: underline;
        }

        .action-links {
            margin-top: 10px;
        }

        header {
            background: rgba(0, 0, 0, 0.6);
            padding: 10px;
            color: white;
            text-align: center;
        }

        nav {
            display: flex;
            justify-content: center;
            background-color: #007BFF;
            padding: 10px 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        /* Extra for mobile responsiveness */
        @media (max-width: 600px) {
            .dashboard-menu {
                flex-direction: column;
            }

            nav a {
                padding: 10px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Bus Timing App - Admin Dashboard</h1>
    </header>

    <nav>
        <a href="upload_bus.php">Add Bus Data</a>
        <a href="upload_announcement.php">Add Announcement</a>
        <a href="upload_notification.php">Add Notification</a>
        <a href="recent_items.php">Recent Items</a>
        <a href="contact_data.php">Contact Database</a>
    </nav>

    <div class="container">
        <!-- Dashboard Menu -->
        <div class="dashboard-menu">
            <a href="upload_bus.php">Add Bus Data</a>
            <a href="upload_announcement.php">Add Announcement</a>
            <a href="upload_notification.php">Add Notification</a>
            <a href="recent_items.php">Recent Added Items</a>
            <a href="#contact-database">Contact Database</a>
        </div>

        <!-- Recent Added Buses Section -->
        <div class="section" id="recent-items">
            <h2>Recent Added Buses</h2>
            <?php while ($row = $recent_buses->fetch_assoc()): ?>
                <div class="recent-items">
                    <strong>From:</strong> <?php echo htmlspecialchars($row['from_location']); ?><br>
                    <strong>To:</strong> <?php echo htmlspecialchars($row['to_location']); ?><br>
                    <strong>Bus Name:</strong> <?php echo htmlspecialchars($row['bus_name']); ?><br>
                    <strong>Bus Number:</strong> <?php echo htmlspecialchars($row['bus_number']); ?><br>
                    <strong>Bus Route:</strong> <?php echo htmlspecialchars($row['bus_route']); ?><br>
                    <div class="action-links">
                        <a href="edit_bus.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_bus.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Recent Added Announcements Section -->
        <div class="section" id="recent-items">
            <h2>Recent Added Announcements</h2>
            <?php while ($row = $recent_announcements->fetch_assoc()): ?>
                <div class="recent-items">
                    <?php if (!empty($row['file_path'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['file_path']); ?>" alt="Announcement Image">
                    <?php endif; ?>
                    <strong>Title:</strong> <?php echo htmlspecialchars($row['title']); ?><br>
                    <strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?><br>
                    <div class="action-links">
                        <a href="edit_announcement.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_announcement.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Recent Added Notifications Section -->
        <div class="section" id="recent-items">
            <h2>Recent Added Notifications</h2>
            <?php while ($row = $recent_notifications->fetch_assoc()): ?>
                <div class="recent-items">
                    <strong>Notification Title:</strong> <?php echo htmlspecialchars($row['title']); ?><br>
                    <strong>Notification Message:</strong> <?php echo htmlspecialchars($row['message']); ?><br>
                    <div class="action-links">
                        <a href="edit_notification.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_notification.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Contact Database Section -->
        <div class="section" id="contact-database">
            <h2>Contact Database</h2>
            <?php
            if ($contact_data->num_rows > 0):
                while ($row = $contact_data->fetch_assoc()):
            ?>
                <div class="recent-items">
                    <strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?><br>
                    <strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?><br>
                    <strong>Submitted at:</strong> <?php echo htmlspecialchars($row['created_at']); ?><br>
                    <div class="action-links">
                        <a href="contact_data.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </div>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <p>No contact data available.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p style="text-align: center; margin-top: 20px;">&copy; <?php echo date("Y"); ?> Bus Timing App. All rights reserved.</p>
    </footer>
</body>
</html>