<?php 
// Check if the ID is set and valid
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Fetch the event details for confirmation
$event_url = 'http://localhost/Event/api/events.php?id=' . $id;
$response = json_decode(file_get_contents($event_url), true);

// Check if the response is valid and contains event data
if (!$response || !isset($response['success']) || !$response['success'] || !isset($response['data'])) {
    header("Location: index.php?error=Event not found");
    exit;
}

$event = $response['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If POST request, delete the event
    $url = 'http://localhost/Event/api/events.php?id=' . urlencode($id);

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'DELETE',
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);

    // Check the deletion response
    if ($response['success']) {
        header("Location: index.php?deleted=1");
        exit;
    } else {
        $error = "Error deleting event: " . $response['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Delete Event</h1>
        <a href="index.php" class="btn">Back to Events</a>
        
        <!-- Display any error message -->
        <?php if (isset($error)): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="alert warning">
            <p>Are you sure you want to delete this event?</p>
            <p><strong>Name:</strong> <?= htmlspecialchars($event['name']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
            <p><strong>Attendees:</strong> <?= htmlspecialchars($event['attendees']) ?></p>
        </div>
        
        <form method="POST">
            <button type="submit" class="btn danger">Confirm Delete</button>
            <a href="index.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
