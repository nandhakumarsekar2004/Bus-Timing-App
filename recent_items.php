<?php
session_start();
require_once 'db_connect.php';

// Initialize message variables
$success_message = "";
$error_message = "";

// Handle deletion requests
if (isset($_GET['delete_announcement_id'])) {
    $delete_id = $_GET['delete_announcement_id'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Announcement deleted successfully!";
    } else {
        $error_message = "Error deleting announcement.";
    }
    $stmt->close();
} elseif (isset($_GET['delete_notification_id'])) {
    $delete_id = $_GET['delete_notification_id'];
    
    // Debugging line to verify correct ID
    echo "Delete Notification ID: " . htmlspecialchars($delete_id);
    
    // Updated query using `notification_id` instead of `id`
    $stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $success_message = "Notification deleted successfully!";
    } else {
        $error_message = "Error deleting notification.";
    }
    $stmt->close();
}

// Fetch all announcements and notifications
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Recent Items</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 2rem 0;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 90%;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.3s ease;
        }
        .item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .item h3 {
            margin-top: 0;
            color: #333;
        }
        .action-links {
            margin-top: 1rem;
        }
        .action-links a {
            color: #007bff;
            text-decoration: none;
            margin-right: 1rem;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php if ($success_message): ?>
    <div class="alert success" id="successAlert"><?php echo $success_message; ?></div>
<?php elseif ($error_message): ?>
    <div class="alert error" id="errorAlert"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="container">
    <h2>Manage Announcements</h2>
    <?php while ($row = $announcements->fetch_assoc()): ?>
        <div class="item">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <div class="action-links">
                <a href="recent_items.php?delete_announcement_id=<?php echo $row['id']; ?>">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div class="container">
    <h2>Manage Notifications</h2>
    <?php while ($row = $notifications->fetch_assoc()): ?>
        <div class="item">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
            <div class="action-links">
                <p><a href="recent_items.php?delete_notification_id=<?php echo urlencode($row['notification_id']); ?>">Delete</a></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    if (successAlert || errorAlert) {
        setTimeout(() => {
            if (successAlert) successAlert.style.display = 'none';
            if (errorAlert) errorAlert.style.display = 'none';
        }, 2000);
    }
</script>
</body>
</html>
