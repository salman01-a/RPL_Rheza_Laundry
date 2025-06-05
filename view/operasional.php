<?php 
$page = 'operasional'; 
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Operasional</title>
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
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .btn-outline-primary {
            border-color: #4880FF;
            color: #4880FF;
        }

        .btn-outline-primary:hover {
            background-color: #4880FF;
            color: white;
        }

        .btn-edit {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
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
            <h3 class="fw-bold">Operasional</h3>
            <div class="fw-semibold">Admin</div>
        </div>

        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">Data Operasional</h5>
                <button class="btn btn-outline-primary">+ Catat Biaya Operasional</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Nominal Pengeluaran</th>
                            <th>Tanggal Pengeluaran</th>
                            <th>Tanggal Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dummy data, ganti dengan data dari database -->
                        <tr>
                            <td>1</td>
                            <td>Air</td>
                            <td>Rp100.000</td>
                            <td>2024-06-14</td>
                            <td>2024-06-14</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-edit">✏️</button>
                                    <button class="btn btn-sm btn-delete">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Listrik</td>
                            <td>Rp100.000</td>
                            <td>2024-06-14</td>
                            <td>2024-06-14</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-edit">✏️</button>
                                    <button class="btn btn-sm btn-delete">🗑️</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Gas</td>
                            <td>Rp100.000</td>
                            <td>2024-06-14</td>
                            <td>2024-06-14</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-edit">✏️</button>
                                    <button class="btn btn-sm btn-delete">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
