<?php
// index.php - Landing page for EAST WEST CAFE
session_start();

// If user is already logged in, redirect them (optional)
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>East West Cafe</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfyVpo8edGOo-0Accvg26mfeP1pvdesbKR6A&s');
 background-position: center;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      text-align: center;
background-image: url('https://image.slidesdocs.com/responsive-images/background/coffee-brown-simple-powerpoint-background_50c1f5d31d__960_540.jpg');   
      padding: 120px;
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 20px rgba(175, 173, 173, 1);
      color: #fff;
      width: 90%;
      max-width: 800px;
    }

    h1 {
      font-size: 2.5rem;
      text-decoration-style: italic;
      font-weight: bold;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.4);
    }

    h2 {
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: bold;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.4);
    }

    p {
      margin-bottom: 30px;
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .btn {
      display: inline-block;
      padding: 14px 35px;
      margin: 10px;
      font-size: 1rem;
      font-weight: bold;
      text-decoration: none;
      border-radius: 30px;
      transition: 0.3s ease-in-out;
      color: #fff;
      background: linear-gradient(45deg, #0c7ae8, #11cdd0);
      box-shadow: 0 4px 12px rgba(7, 142, 204, 0.881);
    }

    .btn:hover {
      transform: scale(1.1);
      box-shadow: 0 6px 18px rgba(0,0,0,0.4);
    }
  </style>
</head>
<body>
  <div class="container">
<h1 style="color: #d29b58;"><i>EAST WEST UNIVERSITY</i></h1>
    <h1 style="color: #ca942e;"><i>CAFE</i></h1>
    <p> Where every sip feels like home and every bite tells a story<br><br>Feeling hungry?
    </p>
    <a href="register.php" class="btn">Register</a>
    <a href="login.php" class="btn">Login</a><br>
    <a href="admin_login.php" class="btn">Admin Login</a>
  </div>
</body>
</html>
