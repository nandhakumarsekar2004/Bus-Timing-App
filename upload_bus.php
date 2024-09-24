<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db_connect.php'; // Connect to the database

// Initialize message variables
$success_message = "";
$error_message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from_location = isset($_POST['from_location']) ? $_POST['from_location'] : '';
    $to_location = isset($_POST['to_location']) ? $_POST['to_location'] : '';
    $bus_name = isset($_POST['bus_name']) ? $_POST['bus_name'] : '';
    $bus_number = isset($_POST['bus_number']) ? $_POST['bus_number'] : '';
    $bus_route = isset($_POST['bus_route']) ? $_POST['bus_route'] : '';

    // Insert data into the buses table
    $stmt = $conn->prepare("INSERT INTO buses (from_location, to_location, bus_name, bus_number, bus_route) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $from_location, $to_location, $bus_name, $bus_number, $bus_route);
    
    if ($stmt->execute()) {
        $bus_id = $stmt->insert_id; // Get the ID of the inserted bus record

        // Insert timings into bus_timings table
        $timing_errors = [];
        foreach ($_POST['departure_time'] as $index => $departure_time) {
            $arrival_time = $_POST['arrival_time'][$index];
            if (empty($departure_time) || empty($arrival_time)) {
                continue; // Skip empty timings
            }

            $stmt = $conn->prepare("INSERT INTO bus_timings (bus_id, departure_time, arrival_time) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $bus_id, $departure_time, $arrival_time);
            
            if (!$stmt->execute()) {
                $timing_errors[] = "Error adding timing entry!";
            }
        }

        if (empty($timing_errors)) {
            $success_message = "Bus details and timings added successfully!";
        } else {
            $error_message = implode("<br>", $timing_errors);
        }
    } else {
        $error_message = "Error adding bus details!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bus Timings</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
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
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
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
        input[type="time"] {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 0.5rem;
        }

        button:hover {
            background-color: #45a049;
        }

        .timing-entry {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .timing-entry:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        #alert {
            display: none;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            transition: opacity 0.5s ease;
        }

        #alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        #alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload Bus Timings</h2>
        <form method="post" action="">
            <label for="from_location">From Location:</label>
            <input type="text" id="from_location" name="from_location" required>

            <label for="to_location">To Location:</label>
            <input type="text" id="to_location" name="to_location" required>

            <label for="bus_name">Bus Name:</label>
            <input type="text" id="bus_name" name="bus_name" required>

            <label for="bus_number">Bus Number:</label>
            <input type="text" id="bus_number" name="bus_number" required>

            <label for="bus_route">Bus Route:</label>
            <input type="text" id="bus_route" name="bus_route" required>
            
            <div id="timings">
                <div class="timing-entry fade-in">
                    <label>Departure Time:</label>
                    <input type="time" name="departure_time[]" required>

                    <label>Arrival Time:</label>
                    <input type="time" name="arrival_time[]" required>

                    <button type="button" onclick="removeTiming(this)">Remove Timing</button>
                </div>
            </div>
            <button type="button" onclick="addTiming()">Add Another Timing</button>
            <button type="submit">Upload</button>
        </form>

        <!-- Alert Placeholder -->
        <div id="alert" class="<?php echo $success_message ? 'success' : ($error_message ? 'error' : ''); ?>">
            <?php echo $success_message ? $success_message : $error_message; ?>
        </div>
    </div>

    <script>
        function addTiming() {
            const timingsDiv = document.getElementById('timings');
            const newEntry = document.createElement('div');
            newEntry.classList.add('timing-entry', 'fade-in');
            newEntry.innerHTML = `
                <label>Departure Time:</label>
                <input type="time" name="departure_time[]" required>

                <label>Arrival Time:</label>
                <input type="time" name="arrival_time[]" required>

                <button type="button" onclick="removeTiming(this)">Remove Timing</button>
            `;
            timingsDiv.appendChild(newEntry);
        }

        function removeTiming(button) {
            const entry = button.parentElement;
            entry.style.opacity = '0';
            entry.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                entry.parentElement.removeChild(entry);
            }, 300);
        }

        // Show alert if there's a success or error message
        document.addEventListener('DOMContentLoaded', function() {
            const alertDiv = document.getElementById('alert');
            if (alertDiv.classList.contains('success') || alertDiv.classList.contains('error')) {
                alertDiv.style.display = 'block';
                setTimeout(() => {
                    alertDiv.style.opacity = '0';
                    setTimeout(() => {
                        alertDiv.style.display = 'none';
                        alertDiv.style.opacity = '1'; // Reset opacity for future alerts
                    }, 500); // Time to wait for the opacity transition
                }, 3000); // Time to show the alert
            }
        });
    </script>
</body>
</html>