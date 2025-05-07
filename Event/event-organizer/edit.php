<?php
require_once('../api/shared.php');

$id = $_GET['id'] ?? '';  // Get event ID from query parameter

if (!$id) {
    die("No event ID provided.");
}

// Fetch event from database
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die("Event not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from form submission
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $attendees = $_POST['attendees'] ?? '';
    $description = $_POST['description'] ?? '';

    // Validate input
    if (!$name || !$location || !$date || !$attendees) {
        die("Missing required fields.");
    }

    // Format the date and time
    $dateTime = $date . ' ' . $time;

    // Update the event in the database
    $stmt = $conn->prepare("UPDATE events SET name = ?, location = ?, date = ?, attendees = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssssis", $name, $location, $dateTime, $attendees, $description, $id);

    if ($stmt->execute()) {
        // Redirect to the event details page after a successful update
        header("Location: eventdetails.php?id=" . urlencode($id));
        exit;
    } else {
        die("Error updating event: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        h1 {
            color: #6a00ff;
            margin-bottom: 1.5rem;
        }

        a.btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: transparent;
            border: 2px solid #6a00ff;
            color: #6a00ff;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        a.btn:hover {
            background: #6a00ff;
            color: white;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        input[type="text"],
        input[type="datetime-local"],
        input[type="number"] {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        button.btn {
            background: #00aaff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button.btn:hover {
            background: #0077cc;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert.error {
            background-color: #dc3545;
            color: white;
        }

        .alert.warning {
            background-color: #ffc107;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Edit Event</h1>
        <a href="index.php" class="btn">Back to Events</a>

        <!-- Form for editing event -->
        <form method="POST">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($event['location']) ?>" required>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="datetime-local" id="date" name="date" value="<?= htmlspecialchars(str_replace(' ', 'T', substr($event['date'], 0, 16))) ?>" required>
            </div>

            <div class="form-group">
                <label for="attendees">Number of Attendees:</label>
                <input type="number" id="attendees" name="attendees" value="<?= htmlspecialchars($event['attendees']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" class="form-control"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn">Update Event</button>
        </form>
    </div>
</body>
</html>
