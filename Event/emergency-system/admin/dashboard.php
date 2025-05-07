<?php
// Fetch emergencies
$emergencyResponse = @file_get_contents('http://localhost/Event/api/emergencies.php');
$emergencies = json_decode($emergencyResponse, true)['data'] ?? [];

// Fetch events
$eventResponse = @file_get_contents('http://localhost/Event/api/events.php');
$events = json_decode($eventResponse, true)['data'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../styles.css" />
</head>
<body>
<div class="container dashboard-page">

    <!-- Header Container -->
    <div class="top-container">
        <h1>Emergency System Admin Dashboard</h1>
        <a href="../index.php" class="btn">Public View</a>
    </div>

    <!-- Active Emergencies Container -->
    <div class="dashboard-panel">
        <h2>Active Emergencies</h2>
        <table>
            <thead>
            <tr>
                <th>Location</th>
                <th>Description</th>
                <th>Status</th>
                <th>Event</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($emergencies as $emergency): ?>
                <tr>
                    <td>
                        <!-- Make Location clickable and without underline -->
                        <a href="emergencydetails.php?id=<?= $emergency['id'] ?>" class="no-underline"><?= htmlspecialchars($emergency['location']) ?></a>
                    </td>
                    <td><?= htmlspecialchars($emergency['description']) ?></td>
                    <td>
                        <span class="status-badge status-<?= htmlspecialchars(strtolower(str_replace(' ', '_', $emergency['status']))) ?>">
                            <?= htmlspecialchars($emergency['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($emergency['event_id']): ?>
                            <!-- Make Event clickable without underline -->
                            <?php
                            $event = array_filter($events, fn($e) => $e['id'] == $emergency['event_id']);
                            $event = reset($event);
                            ?>
                            <?= htmlspecialchars($event['name'] ?? 'Unknown') ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Actions Container (with flexbox for alignment) -->
                        <div class="action-buttons">
                            <!-- View Button -->
                            <a href="emergencydetails.php?id=<?= $emergency['id'] ?>" class="btn info">View</a>
                             <!-- Delete Button: Now goes to delete.php with the emergency ID -->
                            <a href="delete.php?id=<?= $emergency['id'] ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this emergency?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Active Events Container -->
    <div class="dashboard-panel active-events-panel">
        <h2>Active Events</h2>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Date</th>
                <th>Attendees</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event['name']) ?></td>
                    <td><?= htmlspecialchars($event['location']) ?></td>
                    <td><?= htmlspecialchars($event['date']) ?></td>
                    <td><?= htmlspecialchars($event['attendees']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
