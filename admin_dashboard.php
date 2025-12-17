<?php
session_start();
include "db.php";

// Only Admin can access
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'Admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin:0;
    background: linear-gradient(135deg, #42a54cdc, #8c6eef);
    color:#fff;
}
header {
    text-align:center;
    padding:30px 20px;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(10px);
}
header h1 {
    margin:0;
    font-size:2rem;
}

/* Container for dashboard cards */
.dashboard-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 cards per row */
    gap:30px;
    padding:40px;
    max-width:800px;
    margin:0 auto;
}

/* Card style for each option */
.dashboard-card {
    background: rgba(255,255,255,0.1);
    padding:30px 20px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 6px 20px rgba(0,0,0,0.3);
    transition: transform 0.3s, background 0.3s;
    text-decoration:none;
    color:#fff;
    font-size:18px;
    font-weight:bold;
    display:flex;
    flex-direction: column;
    align-items: center;
}
.dashboard-card:hover {
    transform: translateY(-5px) scale(1.05);
    background: rgba(255,255,255,0.2);
}

/* Logout card: full width */
.logout-card {
    background: linear-gradient(135deg, #00ccff, #099eeeff);
    grid-column: 1 / -1; /* span all columns */
}
.logout-card:hover {
    transform: translateY(-5px) scale(1.05);
    background: linear-gradient(135deg,#ee0979,#ff6a00);
}

/* Responsive: 1 card per row on small screens */
@media (max-width: 600px) {
    .dashboard-container {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<header>
<h1>Admin Dashboard</h1>
</header>

<div class="dashboard-container">
    <a href="admin_users.php" class="dashboard-card">Manage Users</a>
    <a href="admin_menu.php" class="dashboard-card">Manage Menu</a>
    <a href="admin_orders.php" class="dashboard-card">Manage Orders</a>
    <a href="admin_feedback.php" class="dashboard-card">View Feedback</a>
    <a href="balance.php" class="dashboard-card">View Balance</a>
    <a href="logout.php" class="dashboard-card logout-card">Logout</a>
</div>

</body>
</html>
