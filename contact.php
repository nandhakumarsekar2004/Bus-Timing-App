<?php
$message = '';
$submitted_data = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message_content = $_POST['message'] ?? '';
    
    // Store the submitted data in a CSV file
    $data = array($name, $email, $message_content, date('Y-m-d H:i:s'));
    $file = fopen('contact_data.csv', 'a');
    fputcsv($file, $data);
    fclose($file);
    
    $message = "Thank you for your message, $name! We'll get back to you soon.";
    $submitted_data = $data;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .image-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(238, 119, 82, 0.7), rgba(231, 60, 126, 0.7), rgba(35, 166, 213, 0.7), rgba(35, 213, 171, 0.7));
            z-index: 1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .image-container:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen p-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6 mb-16">
        <h2 class="text-2xl font-bold text-center mb-6">Contact Us</h2>
        
        <!-- New Image Section -->
        <div class="image-container mb-6">
            <img src="nandhu.jpg" alt="Nandhakumar Sekar" class="w-full h-auto object-cover">
        </div>
        
        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="contactForm" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" id="name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message:</label>
                <textarea id="message" name="message" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>
            <div>
                <input type="submit" value="Send Message" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            </div>
        </form>

        <div class="mt-8 p-4 bg-gray-100 rounded-lg border-4 border-transparent hover:scale-105 transition-transform duration-300" style="background-image: linear-gradient(to right, #ee7752, #e73c7e, #23a6d5, #23d5ab); border-radius: 0.5rem; padding: 1rem; background-clip: padding-box;">
            <h3 class="text-lg font-semibold mb-2">Contact Information</h3>
            <p><strong>Nandhakumar Sekar</strong></p>
            <p>From: Dharmapuri</p>
            <p>Email: <a href="mailto:nandhusekar2602@gmail.com" class="text-blue-600 hover:underline">nandhusekar2602@gmail.com</a></p>
            <p>Mobile: <a href="tel:+919786220194" class="text-blue-600 hover:underline">09786220194</a></p>
        </div>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg">
        <ul class="flex justify-around py-2">
            <?php
            $navItems = [
                ['icon' => 'directions_bus', 'label' => 'Buses', 'href' => 'display_buses.php'],
                ['icon' => 'announcement', 'label' => 'Announcements', 'href' => 'display_announcement.php'],
                ['icon' => 'notifications', 'label' => 'Notifications', 'href' => 'display_notification.php'],
                ['icon' => 'admin_panel_settings', 'label' => 'Admin', 'href' => 'admin_login.php'],
                ['icon' => 'more_horiz', 'label' => 'More', 'href' => 'contact.php']
            ];
            foreach ($navItems as $item):
            ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="flex flex-col items-center text-gray-600 hover:text-indigo-600 transition duration-300">
                        <i class="material-icons"><?php echo htmlspecialchars($item['icon']); ?></i>
                        <span class="text-xs"><?php echo htmlspecialchars($item['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('text-indigo-600');
                }
            });
        });
    </script>
</body>
</html>