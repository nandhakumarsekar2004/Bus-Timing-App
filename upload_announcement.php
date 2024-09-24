<?php
session_start();
require_once 'db_connect.php';

// Initialize message variables
$success_message = "";
$error_message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Handle file upload
    $file_path = '';
    if (!empty($_FILES['announcement_file']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["announcement_file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check for valid file types (images or videos)
        $valid_file_types = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi'];
        if (in_array($file_type, $valid_file_types)) {
            if (move_uploaded_file($_FILES["announcement_file"]["tmp_name"], $target_file)) {
                $file_path = $target_file;
            } else {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Invalid file type. Only images or videos are allowed.";
        }
    }

    if (empty($error_message)) {
        // Insert data into the announcements table
        $stmt = $conn->prepare("INSERT INTO announcements (title, description, file_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $file_path);

        if ($stmt->execute()) {
            $success_message = "Announcement uploaded successfully!";
        } else {
            $error_message = "Error adding announcement.";
        }
    }
}

// Delete announcement
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success_message = "Announcement deleted successfully!";
    } else {
        $error_message = "Error deleting announcement.";
    }
}

// Edit announcement
if (isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle file update if a new file is uploaded
    $file_path = $_POST['existing_file'];
    if (!empty($_FILES['announcement_file']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["announcement_file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $valid_file_types)) {
            if (move_uploaded_file($_FILES["announcement_file"]["tmp_name"], $target_file)) {
                $file_path = $target_file;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE announcements SET title = ?, description = ?, file_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $description, $file_path, $edit_id);

    if ($stmt->execute()) {
        $success_message = "Announcement updated successfully!";
    } else {
        $error_message = "Error updating announcement.";
    }
}

// Fetch all announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
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
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            color: #555;
        }
        input[type="text"],
        textarea,
        input[type="file"] {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        textarea {
            min-height: 100px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .announcement {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.3s ease;
        }
        .announcement:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .announcement h3 {
            margin-top: 0;
            color: #333;
        }
        .announcement img,
        .announcement video {
            max-width: 100%;
            border-radius: 4px;
            margin-top: 1rem;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            text-align: center;
            border-radius: 4px;
            animation: fadeIn 0.5s ease-out;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
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
    <h2>Upload Announcement</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="announcement_file">File (optional):</label>
        <input type="file" id="announcement_file" name="announcement_file">

        <button type="submit">Upload</button>
    </form>
</div>

<div class="container">
    <h2>Existing Announcements</h2>
    <?php while ($row = $announcements->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
            <?php if ($row['file_path']): ?>
                <?php if (in_array(pathinfo($row['file_path'], PATHINFO_EXTENSION), ['mp4', 'mov', 'avi'])): ?>
                    <video controls src="<?php echo $row['file_path']; ?>" width="100%"></video>
                <?php else: ?>
                    <img src="<?php echo $row['file_path']; ?>" alt="Announcement Image" width="100%">
                <?php endif; ?>
            <?php endif; ?>
            <div class="action-links">
                <a href="upload_announcement.php?delete_id=<?php echo $row['id']; ?>">Delete</a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                <label for="edit_title_<?php echo $row['id']; ?>">Edit Title:</label>
                <input type="text" id="edit_title_<?php echo $row['id']; ?>" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                <label for="edit_description_<?php echo $row['id']; ?>">Edit Description:</label>
                <textarea id="edit_description_<?php echo $row['id']; ?>" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                <label for="edit_file_<?php echo $row['id']; ?>">Update File (optional):</label>
                <input type="file" id="edit_file_<?php echo $row['id']; ?>" name="announcement_file">
                <input type="hidden" name="existing_file" value="<?php echo $row['file_path']; ?>">
                <button type="submit">Update</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

<script>
    // Show alert for 2 seconds, then hide it
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