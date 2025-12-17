<?php
session_start();
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch all feedback with user info
$sql_feedback = "
    SELECT 
        f.Feedback_ID,
        f.Message,
        f.Created_At,
        u.Name,
        u.Email
    FROM feedback f
    JOIN Users u ON f.User_ID = u.User_ID
    ORDER BY f.Created_At DESC
";

$result_feedback = mysqli_query($conn, $sql_feedback);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Feedback - East West Cafe</title>
<style>
body { font-family: 'Segoe UI', sans-serif; margin:0; background: linear-gradient(135deg, #42a54cdc, #8c6eef); color: #fff; }
header { background: rgba(10, 7, 7, 0.4); padding:20px; text-align:center; backdrop-filter: blur(10px); position: relative; }
header h1 { margin:0; font-size:2rem; }
.logout { position: absolute; top:20px; right:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); padding:10px 20px; border:none; border-radius:20px; color:#fff; cursor:pointer; text-decoration:none; }
.container { padding:40px; }
.feedback-table { width:100%; border-collapse: collapse; }
.feedback-table th, .feedback-table td { padding:12px; border-bottom:1px solid #fff; text-align:left; }
.feedback-table th { background: rgba(0,0,0,0.4); }
.feedback-msg { background: rgba(255,255,255,0.1); padding:10px; border-radius:12px; }
</style>
</head>
<body>

<header>
<h1>User Feedback</h1>
<a href="admin_dashboard.php" class="logout">Back to Dashboard</a>
</header>

<div class="container">

<?php if(mysqli_num_rows($result_feedback) > 0): ?>
<table class="feedback-table">
<tr>
    <th>ID</th>
    <th>User Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Date</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result_feedback)): ?>
<tr>
    <td><?php echo $row['Feedback_ID']; ?></td>
    <td><?php echo htmlspecialchars($row['Name']); ?></td>
    <td><?php echo htmlspecialchars($row['Email']); ?></td>
    <td>
        <div class="feedback-msg">
            <?php echo nl2br(htmlspecialchars($row['Message'])); ?>
        </div>
    </td>
    <td><?php echo $row['Created_At']; ?></td>
</tr>
<?php endwhile; ?>

</table>
<?php else: ?>
<p style="text-align:center;">No feedback submitted yet.</p>
<?php endif; ?>

</div>
</body>
</html>
