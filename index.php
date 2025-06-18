<?php
session_start();
include 'database/connection.php';
$gagal = false;
if (isset($_GET['dev']) && $_GET['dev'] === 'create') {
    $nama = 'rheza';
    $no_hp = '08123456789';
    $username = 'admin';
    $password = 'admin123';
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
    $query = "SELECT * FROM Users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password yang sudah di-hash
    if ($user ) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        header("Location: view/dashboard.php");
        exit();
    } else {
        header("Location: index.php?pesan=gagal");
        exit();
    }
}
if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'){
  $gagal = true;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="shortcut icon" href="assets/logo.png" type="image/x-icon">
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
          <p style="text-align: left; margin = 2px;"><?php echo($gagal)? "<span style='color:#fa1111;'>Username atau Password Salah</span>":""; ?></p>
        </div>
        <button type="submit" name="submit">LOGIN</button>
      </form>
    </div>
  </div>
</body> 
</body>
</html>
