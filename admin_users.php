<?php
session_start();
include "db.php";

// Only Admin can access
if (!isset($_SESSION['User_ID']) || $_SESSION['Role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO Users (Name, Email, Password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        $message = "User added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE Users SET Name=?, Email=?, Password=?, Role=? WHERE User_ID=?");
    $stmt->bind_param("ssssi", $name, $email, $password, $role, $user_id);

    if ($stmt->execute()) {
        $message = "User updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Handle Delete User
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $stmt = $conn->prepare("DELETE FROM Users WHERE User_ID=?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all users
$result_users = $conn->query("SELECT * FROM Users ORDER BY User_ID DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Users</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #42a54cdc, #8c6eef); color:#fff; margin:0; }
header { background: rgba(0,0,0,0.4); padding:20px; text-align:center; backdrop-filter: blur(10px); position: relative; }
header h1 { margin:0; font-size:2rem; }
.logout { position: absolute; top:20px; right:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); padding:10px 20px; border:none; border-radius:20px; color:#fff; cursor:pointer; text-decoration:none; }
.container { padding:40px; }
.message { text-align:center; margin-bottom:20px; color:#ffd369; font-weight:bold; }
form { margin-bottom:30px; text-align:center; }
form input, form select { padding:10px; margin:5px; border-radius:10px; border:none; }
form button { padding:10px 20px; border:none; border-radius:20px; background:linear-gradient(135deg, #00ccff, #099eeeff); color:#fff; cursor:pointer; }
table { width:100%; border-collapse: collapse; }
table th, table td { padding:10px; border-bottom:1px solid #fff; text-align:center; }
table th { background: rgba(0,0,0,0.4); }
.action-btn { padding:5px 10px; border:none; border-radius:10px; cursor:pointer; color:#fff; margin:2px; }
.edit-btn { background:#007bff; }
.delete-btn { background:#dc3545; }
</style>
</head>
<body>

<header>
<h1>Manage Users</h1>
<a href="admin_dashboard.php" class="logout">Back to Admin Dashboard</a>
</header>

<div class="container">

<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>

<!-- Add User Form -->
<h2 style="text-align:center;">Add New User</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="password" placeholder="Password" required>
    <select name="role" required>
        <option value="Student">Student</option>
        <option value="Admin">Admin</option>
    </select>
    <button type="submit" name="add_user">Add User</button>
</form>

<!-- Users Table -->
<h2 style="text-align:center;">Existing Users</h2>
<table>
<tr>
    <th>User ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Password</th>
    <th>Role</th>
    <th>Actions</th>
</tr>
<?php while($user = $result_users->fetch_assoc()): ?>
<tr>
    <td><?php echo $user['User_ID']; ?></td>
    <td><?php echo $user['Name']; ?></td>
    <td><?php echo $user['Email']; ?></td>
    <td><?php echo $user['Password']; ?></td>
    <td><?php echo $user['Role']; ?></td>
    <td>
        <!-- Edit form -->
        <form method="POST" style="display:inline-block;">
            <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
            <input type="text" name="name" value="<?php echo $user['Name']; ?>" required>
            <input type="email" name="email" value="<?php echo $user['Email']; ?>" required>
            <input type="text" name="password" value="<?php echo $user['Password']; ?>" required>
            <select name="role">
                <option value="Student" <?php if($user['Role']=='Student') echo 'selected'; ?>>Student</option>
                <option value="Admin" <?php if($user['Role']=='Admin') echo 'selected'; ?>>Admin</option>
            </select>
            <button type="submit" name="update_user" class="action-btn edit-btn">Update</button>
        </form>
        <a href="?delete_user=<?php echo $user['User_ID']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>

</div>
</body>
</html>