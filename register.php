<?php
// register.php - Register page for EAST WEST CAFE
//session_start();

include "db.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    $sql = "insert into users(name,email,password,role)values('$name','$email','$password','Student')";
    $result = mysqli_query($conn,$sql);
    if(!$result){
      echo "ERROR! : " . mysqli_error($conn);
    }
    else{
      echo "Registerd Successfully";
      header("Location: login.php?registered=1");
            exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - East West Cafe</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      background: linear-gradient(135deg, rgb(89, 222, 251), rgba(3, 35, 49, 0));
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .register-container {
      background: rgba(13, 111, 131, 0.1);
      padding: 50px;
      border-radius: 20px;
      backdrop-filter: blur(12px);
      box-shadow: 0 50px 40px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 420px;
      color: rgb(255, 255, 255);
      text-align: center;
    }

    h2 {
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: bold;
    }

    .form-group {
      margin-bottom: 15px;
      text-align: left;
    }

    label {
      font-weight: bold;
      font-size: 0.9rem;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      border: none;
      border-radius: 20px;
      outline: none;
    }

    .btn {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 30px;
      margin-top: 10px;
      background: linear-gradient(135deg, #00ccff, #099eeeff);
      color: rgb(250, 254, 254);
      font-size: 1rem;
      
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 18px rgba(3, 37, 16, 0.4);
    }

    .message {
      margin-top: 15px;
      font-size: 0.95rem;
    }

    a {
      color: rgb(102, 248, 105);
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Student Registration</h2>
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
    <form method="POST" action="">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
      </div>

      <button type="submit" class="btn">Register</button>
    </form>
    <p class="message">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>