<?php
session_start();
include "db.php";

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE Email='$email' AND Password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Set session variables
        $_SESSION['User_ID'] = $user['User_ID'];
        $_SESSION['Name'] = $user['Name'];
        $_SESSION['Role'] = $user['Role'];

        // Role-based redirection
        if ($user['Role'] === 'Admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: menu.php");
        }
        exit();
    } else {
        $message = "Invalid email or password!";
    }
}

// Redirect if already logged in
if (isset($_SESSION['User_ID'])) {
    if ($_SESSION['Role'] === 'Admin') {
        header("Location: admin_menu.php");
    } else {
        header("Location: menu.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - East West Cafe</title>
<style>
body { font-family: 'Segoe UI', sans-serif; height:100vh; background: linear-gradient(135deg, rgb(89, 222, 251), rgba(3, 35, 49, 0)); display:flex; justify-content:center; align-items:center; color:#ffffff; }
.login-container { background: rgba(22, 88, 138, 0.1); padding:50px; border-radius:20px; backdrop-filter: blur(12px); text-align:center; width:100%; max-width:420px; }
h2 { margin-bottom:20px; font-size:2rem; }
input { width:100%; padding:12px; margin:5px 0; border:none; border-radius:8px; outline:none; }
button { width:100%; padding:14px; margin-top:20px; border:none; border-radius:30px; background:linear-gradient(135deg, #00ccff, #099eeeff); color:#fff; font-weight:bold; cursor:pointer; }
.message { margin-top:15px; color:#ffd369; }
</style>
</head>
<body>
<div class="login-container">
<h2>Login</h2>
<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>
<form method="POST" action="">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<p class="message">Don't have an account? <a href="register.php">Register</a></p>
</div>
</body>
</html>