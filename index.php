<?php
session_start();
include 'database/connection.php';

if (isset($_GET['dev']) && $_GET['dev'] === 'create') {
    $nama = 'rheza';
    $no_hp = '08123456789';
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'owner';

    // Cek apakah sudah ada owner
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE role = 'owner' AND username = '$username'");
    if (mysqli_num_rows($cek) === 0) {
        mysqli_query($conn, "INSERT INTO users (nama, no_hp, username, password, role) VALUES ('$nama', '$no_hp', '$username', '$password', '$role')");
        echo "<script>alert('Akun Owner berhasil dibuat: admin / admin123'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Owner sudah ada.'); window.location='index.php';</script>";
    }
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil user berdasarkan username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password yang sudah di-hash
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        header("Location: view/dashboard.php");
        exit();
    } else {
        header("Location: index.php?pesan=gagal");
        exit();
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
