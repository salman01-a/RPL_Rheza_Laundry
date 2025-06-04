<?php
session_start();
include 'database/connection.php';

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM Users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role']= $user['role'];
        header("Location: view/dashboard.php");

    } else {
    header("Location: index.php?pesan=gagal");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="background-shape"></div>

  <div class="login-wrapper">
    <div class="login-container">
      <img src="assets/logo.png" alt="Logo" class="logo-image" />

      <form class="login-form" action="index.php" method="POST">
        <div class="input-group">
          <input type="text" placeholder="USERNAME" name= "username" required />
        </div>
        <div class="input-group">
          <input type="password" placeholder="PASSWORD" name= "password"required />
        </div>
        <button type="submit" name="submit">LOGIN</button>
      </form>
    </div>
  </div>
</body>
</html>
