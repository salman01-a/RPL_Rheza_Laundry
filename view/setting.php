<?php
$page = "setting";
session_start();
include '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../");
  exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION['user_id'];
$query = "SELECT * FROM Users WHERE id_user = $id_user";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../style/DashboardOwner.css">
  <style>
    body {
      font-family: 'Nunito Sans', sans-serif;
      background-color: #F8F9FC;
    }
    
        

    .rheza {
      color: #4880FF;
    }

    .profile-img {
      width: 130px;
      height: 130px;
      background-color: #0d6efd;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 50px;
      color: white;
      margin: auto;
    }

    .card {
      border: none;
      border-radius: 15px;
    }

    input[disabled] {
      background-color: #e9ecef !important;
      color: #495057;
    }

    /* .card-title {
      font-weight: 700  ;
      color: #343a40;
    } */
  </style>
</head>
<body>
  <?php
    if ($_SESSION['role'] == 'owner')
        include '../layout/sidebar.php';
    else
        include '../layout/SidebarStaff.php';
  ?>  

  <main class="container py-4" style="margin-left: 250px;"> <!-- biar offset karena sidebar -->
    <div class="row">
      <div class="col-lg-8 offset-lg-2">
        <div class="card shadow p-4">
          <h4 class="card-title mb-4">Pengaturan Profil</h4>
          <div class="text-center mb-4">
            <div class="profile-img">
              <i class="bi bi-person-fill"></i>
            </div>
          </div>

          <form>
            <div class="mb-3">
              <label for="nama" class="form-label">Nama</label>
              <input type="text" class="form-control" id="nama" value="<?= htmlspecialchars($user['nama']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="telepon" class="form-label">Nomor Telepon</label>
              <input type="text" class="form-control" id="telepon" value="<?= htmlspecialchars($user['no_hp']); ?>" disabled>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
