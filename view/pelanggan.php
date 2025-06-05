<?php 
$page = 'pelanggan'; 
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/DashboardOwner.css">
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #F8F9FC;
        }

        .text-primary-custom {
            color: #4880FF;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .table thead {
            background-color: #F1F3F9;
        }

        .whatsapp-icon {
            color: #25D366;
            font-size: 1.2rem;
        }

        .search-bar {
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 8px 12px;
            width: 250px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .dropdown-month {
            width: auto;
            border-radius: 8px;
        }

        .section-title {
            font-weight: 700;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php 
        if($_SESSION['role'] == 'owner')
            include '../layout/sidebar.php'; 
        else 
            include '../layout/SidebarStaff.php';
        ?>

        <div class="p-4 flex-grow-1 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title">Pelanggan</h3>
                <div class="fw-semibold">Admin</div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">Daftar Pelanggan</h5>
                        <select class="form-select dropdown-month">
                            <option>Oktober</option>
                            <option>November</option>
                            <option>Desember</option>
                        </select>
                    </div>

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM pelanggan ORDER BY id_pelanggan ASC";
                            $result = mysqli_query($conn, $query);
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['nama']}</td>
                                    <td>{$row['no_hp']} <a href='https://wa.me/62" . substr($row['no_hp'], 1) . "' target='_blank'><i class='bi bi-whatsapp whatsapp-icon ms-2'></i></a></td>
                                    <td>{$row['alamat']}</td>
                                </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
