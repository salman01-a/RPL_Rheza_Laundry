<?php $page = 'dashboard'; 

include 'database/connection.php';
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
    <title>Dashboard Owner</title>
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

        .icon-dashboard {
            font-size: 2rem;
            color: #4880FF;
            margin-bottom: 10px;
        }

        .bg-success-custom {
            background-color: #00B69B !important;
            color: white;
        }

        .bg-processing {
            background-color: #6226EF !important;
            color: white;
        }

        .badge {
            padding: 8px 14px;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .card-header {
            background: none;
            border: none;
            padding: 0;
            font-weight: 600;
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
        else include '../layout/SidebarStaff.php';
        ?>

        <div class="p-4 flex-grow-1 w-100">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Dashboard</h3>
                <div class="fw-semibold">Admin</div>
            </div>


        <?php 
        if($_SESSION['role'] == 'owner'){
        ?>
            <!-- Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-people-fill icon-dashboard"></i>
                            <h6>Total Pelanggan</h6>
                            <h3 class="text-primary-custom">10k</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-currency-dollar icon-dashboard"></i>
                            <h6>Total Pendapatan</h6>
                            <h3 class="text-primary-custom">10M</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-basket-fill icon-dashboard"></i>
                            <h6>Total Order</h6>
                            <h3 class="text-primary-custom">100k</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-clock-history icon-dashboard"></i>
                            <h6>Order In Proses</h6>
                            <h3 class="text-primary-custom">100</h3>
                        </div>
                    </div>
                </div>
            </div>
            <?php }
            else{
            ?>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-people-fill icon-dashboard"></i>
                            <h6>Total Pelanggan</h6>
                            <h3 class="text-primary-custom">10k</h3>
                        </div>
                    </div>
                </div>
             
               
                <div class="col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-clock-history icon-dashboard"></i>
                            <h6>Order In Proses</h6>
                            <h3 class="text-primary-custom">100</h3>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <!-- Detail Transaksi Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Detail Transaksi</h5>
                        <select class="form-select w-auto">
                            <option>Oktober</option>
                            <option>November</option>
                        </select>
                    </div>

                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Layanan</th>
                                <th>Berat Cucian</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Christine Brooks</td>
                                <td>Self-Service</td>
                                <td>5Kg</td>
                                <td>Cashless</td>
                                <td><span class="badge bg-success-custom">Completed</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Christine Brooks</td>
                                <td>Drop-Off</td>
                                <td>5Kg</td>
                                <td>Cashless</td>
                                <td><span class="badge bg-processing">Processing</span></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Christine Brooks</td>
                                <td>Express</td>
                                <td>5Kg</td>
                                <td>Cashless</td>
                                <td><span class="badge bg-success-custom">Completed</span></td>
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
