<?php
include('../api/shared.php');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['calendar_fd_user']['type'])) {
    $_SESSION['calendar_fd_user']['type'] = 'admin'; // TEMP: remove in prod
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Event - Event Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background-color: #f4f6f9;
      padding-top: 56px;
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
    #sidebar a:hover {
      background-color: rgb(0, 26, 255);
    }
    .content-wrapper {
      margin-left: 250px;
      padding: 20px;
    }
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: #6a00ff;
      z-index: 1000;
    }
    .form-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 40px;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .form-header h3 {
      font-size: 28px;
      font-weight: 600;
      color: #6a00ff;
    }
    .form-label {
      font-weight: 500;
      color: #333;
    }
    .form-control {
      border-radius: 10px;
      padding: 10px;
      font-size: 16px;
    }
    .btn-submit {
      background-color: #6a00ff;
      color: white;
      border: none;
      font-size: 16px;
      padding: 12px 24px;
      border-radius: 10px;
      width: 100%;
    }
    .footer {
      text-align: center;
      padding: 15px 0;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <a class="navbar-brand" href="#">📅 Event Management</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <?php if (isset($_SESSION['calendar_fd_user'])): ?>
          <a class="nav-link text-white" href="logout.php">Logout</a>
        <?php else: ?>
          <a class="nav-link text-white" href="login.php">Login</a>
        <?php endif; ?>
      </li>
    </ul>
  </div>
</nav>

<div id="sidebar">
  <a href="index.php">📅 Event Calendar</a>
  <a href="eventlist.php">📋 Event List</a>
  <a href="addevent.php">➕ Add Event</a>
</div>

<div class="content-wrapper">
  <div class="container-fluid mt-4">
    <div class="form-container">
      <div class="form-header">
        <h3>Add Event</h3>
      </div>
      <form id="eventForm">
        <div class="mb-3">
          <label for="name" class="form-label">Event Name</label>
          <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="mb-3">
          <label for="location" class="form-label">Location</label>
          <input type="text" class="form-control" name="location" id="location" required>
        </div>
        <div class="mb-3">
          <label for="datetime" class="form-label">Date & Time</label>
          <input type="datetime-local" class="form-control" name="datetime" id="datetime" required>
        </div>
        <div class="mb-3">
          <label for="attendees" class="form-label">Attendees</label>
          <input type="number" class="form-control" name="attendees" id="attendees" min="1" required>
        </div>
        <button type="submit" class="btn-submit">Save Event</button>
      </form>
    </div>
  </div>
  <div class="footer">
    <p>&copy; <?= date("Y") ?> Event Management System</p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  document.getElementById("eventForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const data = {
      name: document.getElementById("name").value,
      location: document.getElementById("location").value,
      datetime: document.getElementById("datetime").value,
      attendees: document.getElementById("attendees").value
    };

    axios.post("http://localhost/Event/api/events.php", data, {
      headers: {
        'Content-Type': 'application/json'
      }
    })
    .then(res => {
      if (res.data.success) {
        alert("Event saved!");
        location.reload();
      } else {
        alert("Error: " + res.data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert("Request failed.");
    });
  });
</script>

</body>
</html>
