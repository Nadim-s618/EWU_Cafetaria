<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel - East West Cafe</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #4e2a84, #ff914d);
    color: #fff;
    height: 100vh;
    display: flex;
}

.sidebar {
    width: 260px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(12px);
    padding: 30px 20px;
    box-sizing: border-box;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    padding: 12px;
    margin: 10px 0;
    color: #fff;
    text-decoration: none;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
    text-align: center;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.3);
}

.main-content {
    flex: 1;
    padding: 40px;
}

.header {
    background: rgba(255,255,255,0.2);
    padding: 20px;
    border-radius: 15px;
    backdrop-filter: blur(12px);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header h2 {
    margin: 0;
}

.card-container {
    margin-top: 40px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

.card {
    background: rgba(255,255,255,0.15);
    padding: 30px;
    border-radius: 20px;
    backdrop-filter: blur(12px);
    text-align: center;
}

.logout-btn {
    padding: 10px 18px;
    background: #ff6a00;
    border: none;
    border-radius: 20px;
    color: white;
    cursor: pointer;
    font-weight: bold;
}

.logout-btn:hover {
    background: #ee0979;
}
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#">Dashboard</a>
    <a href="#">Manage Users</a>
    <a href="#">Orders</a>
    <a href="#">Menu Items</a>
    <a href="#">Settings</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h2>Welcome, <?php echo $_SESSION['Name']; ?> 👋</h2>
        <a href="logout.php"><button class="logout-btn">Logout</button></a>
    </div>

    <div class="card-container">
        <div class="card">
            <h3>Total Users</h3>
            <p>128</p>
        </div>

        <div class="card">
            <h3>Orders Today</h3>
            <p>42</p>
        </div>

        <div class="card">
            <h3>Revenue</h3>
            <p>$1,240</p>
        </div>

        <div class="card">
            <h3>Pending Tasks</h3>
            <p>7</p>
        </div>
    </div>
</div>

</body>
</html>
