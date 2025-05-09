<?php
// Fetch events from API
$api_url = 'http://localhost/Event/api/events.php';
$response = file_get_contents($api_url);
$events = $response ? json_decode($response, true)['data'] ?? [] : [];

$formattedEvents = [];
foreach ($events as $event) {
    $formattedEvents[] = [
        'id' => $event['id'],
        'title' => $event['name'],
        'start' => $event['date'],
        'end' => $event['end_date'] ?? $event['date'],
        'location' => $event['location'],
        'attendees' => $event['attendees'],
        'status' => $event['status'] ?? 'PENDING',
    ];
}
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Event - Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet"/>
  <style>
    body {
        background-color: #f4f6f9;
        padding-top: 56px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    #sidebar {
        position: fixed;
        top: 56px;
        left: 0;
        height: calc(100% - 56px);
        width: 250px;
        background-color: #6a00ff;
        color: white;
        padding-top: 20px;
    }
    #sidebar a {
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }
    #sidebar a:hover { background-color: #4a00cc; }
    .content-wrapper {
        margin-left: 250px;
        padding: 20px;
    }
    .navbar-dark.bg-dark {
        background-color: #6a00ff !important;
    }
    .navbar-brand {
        font-weight: bold;
        color: white;
    }
    .navbar .btn-outline-light {
        color: white;
        border-color: #6a00ff;
    }
    .navbar .btn-outline-light:hover {
        background-color: #6a00ff;
        border-color: #6a00ff;
    }
    .card {
        border: 2px solid #6a00ff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .card-header {
        background: linear-gradient(45deg, #6a00ff, #00aaff);
        color: white;
        font-size: 1.2rem;
        font-weight: bold;
    }
    .card-body {
        background-color: #ffffff;
    }
    .form-label {
        font-weight: 500;
        color: #4a00cc;
    }
    .form-control {
        border: 1px solid #6a00ff;
        border-radius: 8px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        font-size: 0.9rem;
    }
    .form-control:focus {
        border-color: #6a00ff;
        box-shadow: 0 0 5px rgba(106, 0, 255, 0.5);
    }
    .btn {
        background: linear-gradient(45deg, #6a00ff, #4a00cc);
        color: white;
        border-radius: 5px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .btn:hover {
        background: linear-gradient(45deg, #4a00cc, #6a00ff);
        border-color: #4a00cc;
    }
    .footer {
        padding: 15px;
        text-align: center;
        font-size: 0.9rem;
    }
    .footer a {
        color: #6a00ff;
        text-decoration: none;
        font-weight: 500;
    }
    .footer a:hover {
        text-decoration: underline;
    }

    /* Smaller form style */
    .card-body form {
        max-width: 600px;
        margin: 0 auto;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">📅 Event Management</span>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="text-white">Welcome, <?= $_SESSION['username'] ?? 'Guest' ?></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<div id="sidebar">
  <a href="index.php">📆 Event Calendar</a>
  <a href="eventlist.php">📋 Event List</a>
  <a href="addevent.php">➕ Add Event</a>
</div>

<!-- Content -->
<div class="content-wrapper">
  <div class="row">
    <!-- Form for adding events -->
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Add Event</h5></div>
        <div class="card-body">
          <form id="eventForm">
            <input type="hidden" id="eventId" name="id">
            <div class="mb-3">
              <label class="form-label">Event Name</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Location</label>
              <input type="text" class="form-control" name="location" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date & Time</label>
              <input type="datetime-local" class="form-control" name="datetime" id="datetime" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Attendees</label>
              <input type="number" class="form-control" name="attendees" min="1" required>
            </div>
            <button type="submit" class="btn w-100">💾 Save Event</button>
            <button type="reset" class="btn btn-outline-secondary mt-2 w-100" onclick="clearForm()">🧹 Clear</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer mt-4 shadow-sm rounded">
    <p class="mb-0">&copy; <?= date("Y") ?> Event Management | <a href="index.php">Back to Calendar</a></p>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('eventForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const eventData = {
        name: formData.get('name'),
        location: formData.get('location'),
        date: formData.get('datetime'),
        attendees: formData.get('attendees')
      };

      axios.post('http://localhost/Event/api/events.php', JSON.stringify(eventData), {
        headers: { 'Content-Type': 'application/json' }
      })
      .then(res => {
        if (res.data.success) {
          alert('Event saved!');
          location.reload();
        } else {
          console.error('Error saving event:', res.data.message);
        }
      })
      .catch(err => {
        console.error('Error details:', err.response ? err.response.data : err);
      });
    });
  });

  function clearForm() {
    document.getElementById('eventForm').reset();
    document.getElementById('eventId').value = '';
  }
</script>

</body>
</html>
