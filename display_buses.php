<?php
require_once 'db_connect.php'; // Connect to the database

// Initialize variables
$source = '';
$destination = '';
$departure_time = '';
$buses = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if form fields exist in the $_POST array
    if (isset($_POST['source'], $_POST['destination'], $_POST['departure_time'])) {
        $source = $_POST['source'];
        $destination = $_POST['destination'];
        $departure_time = $_POST['departure_time'];

        // Calculate the time range (30 minutes before and after)
        $departure_time_min = date('H:i:s', strtotime($departure_time . ' - 30 minutes'));
        $departure_time_max = date('H:i:s', strtotime($departure_time . ' + 30 minutes'));

        // Prepare SQL statement to retrieve buses based on the search criteria and time range
        $sql = "
        SELECT b.*, bt.departure_time, bt.arrival_time
        FROM buses b
        JOIN bus_timings bt ON b.bus_id = bt.bus_id
        WHERE b.from_location = ? AND b.to_location = ? 
        AND TIME(bt.departure_time) BETWEEN ? AND ?
        ORDER BY bt.departure_time
    ";
        $stmt = $conn->prepare($sql);

        // Debug: Check if the statement was prepared correctly
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("ssss", $source, $destination, $departure_time_min, $departure_time_max);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the query returned any results
        if ($result->num_rows === 0) {
            echo "No buses found for the given criteria.";
        } else {
            // Fetch all results
            while ($row = $result->fetch_assoc()) {
                // Format times to show in 12-hour format with AM/PM
                $row['departure_time'] = date('h:i A', strtotime($row['departure_time']));
                $row['arrival_time'] = date('h:i A', strtotime($row['arrival_time']));
                $buses[] = $row;
            }
        }
    } else {
        echo "Form data is missing. Please ensure all fields are filled.";
    }
}

// Refresh the page to clear the form data and search results
if (isset($_POST['refresh'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Navigation</title>
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
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
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
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: 500;
            color: #34495e;
        }
        input[type="text"], input[type="time"] {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        input[type="text"]:focus, input[type="time"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
            outline: none;
        }
        input[type="submit"], .refresh-button {
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
        input[type="submit"]:hover, .refresh-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease forwards, colorChange 5s infinite alternate;
        }
        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .card p {
            margin: 10px 0;
            color: #34495e;
        }
        .no-results {
            text-align: center;
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 30px;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes colorChange {
            0% {
                background-color: rgba(255, 255, 255, 0.9);
            }
            25% {
                background-color: rgba(255, 240, 245, 0.9);
            }
            50% {
                background-color: rgba(240, 248, 255, 0.9);
            }
            75% {
                background-color: rgba(245, 255, 250, 0.9);
            }
            100% {
                background-color: rgba(255, 255, 240, 0.9);
            }
        }
        .refresh-button{
            color: green;
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

        /* Add padding to the bottom of the container to prevent content from being hidden by the navigation bar */
        .container {
            padding-bottom: 70px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search for Buses</h2>
        <form method="post" action="">
            <label for="source">From Location:</label>
            <input type="text" id="source" name="source" required>

            <label for="destination">To Location:</label>
            <input type="text" id="destination" name="destination" required>

            <label for="departure_time">Departure Time:</label>
            <input type="time" id="departure_time" name="departure_time" required>

            <input type="submit" value="Search">
        </form>
        <br>
        <form method="post">
            <input type="submit" name="refresh" class="refresh-button" value="Refresh Page">
        </form>

        <!-- Display buses only if search is made and there are results -->
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['refresh'])): ?>
            <?php if (!empty($buses)): ?>
                <h2>Available Buses</h2>
                <?php foreach ($buses as $index => $bus): ?>
                    <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s;">
                        <div class="card-title">Bus Number: <?php echo htmlspecialchars($bus['bus_number']); ?></div>
                        <p><strong>From Location:</strong> <?php echo htmlspecialchars($bus['from_location']); ?></p>
                        <p><strong>To Location:</strong> <?php echo htmlspecialchars($bus['to_location']); ?></p>
                        <p><strong>Bus Name:</strong> <?php echo htmlspecialchars($bus['bus_name']); ?></p>
                        <p><strong>Bus Route:</strong> <?php echo htmlspecialchars($bus['bus_route']); ?></p>
                        <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($bus['departure_time']); ?></p>
                        <p><strong>Arrival Time:</strong> <?php echo htmlspecialchars($bus['arrival_time']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-results">No buses found matching your criteria.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="mdc-bottom-navigation">
        <div class="mdc-bottom-navigation__container">
            <a href="display_buses.php" class="mdc-bottom-navigation__link active">
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