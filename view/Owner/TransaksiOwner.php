<?php $page = 'transaksi'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../style/DashboardOwner.css">
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #F8F9FC;
        }

        .text-primary-custom {
            color: #4880FF;
            font-weight: 700;
        }

        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 10px;
        }

        .bg-success-custom {
            background-color: #00B69B !important;
            color: white;
        }

        .bg-processing {
            background-color: #6226EF !important;
            color: white;
        }

        .bg-rejected {
            background-color: #FF4C61 !important;
            color: white;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
        }

        .table thead {
            background-color: #F1F4F9;
            font-weight: 600;
        }

        .btn-action {
            background: none;
            border: none;
            padding: 0;
            color: #888;
        }

        .btn-action:hover {
            color: #FF4C61;
        }
    </style>
</head>

<!-- ... bagian head tetap sama ... -->

<body>
    <div class="d-flex">
        <?php include '../../layout/Sidebar.php'; ?>

        <div class="p-4 flex-grow-1 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Transaksi</h3>
                <button class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>Tambah Transaksi</button>
            </div>

            <!-- Tabel READ (tampilan saja, tanpa tombol aksi) -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Data Transaksi</h5>
                    <table class="table table-bordered align-middle">
                        <thead>
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
                                <td>Self Service</td>
                                <td>5KG (2 Lot)</td>
                                <td>Cash</td>
                                <td><span class="badge bg-success-custom">Completed</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Rosie Pearson</td>
                                <td>Drop Off</td>
                                <td>6KG (2 Lot)</td>
                                <td>Cashless</td>
                                <td><span class="badge bg-processing">Processing</span></td>
                            </tr>
                            <!-- ... data lainnya ... -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel CUD (dengan tombol aksi edit & hapus) -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Kelola Transaksi</h5>
                    <table class="table table-hover table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Layanan</th>
                                <th>Berat Cucian</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Christine Brooks</td>
                                <td>Self Service</td>
                                <td>5KG (2 Lot)</td>
                                <td>Cash</td>
                                <td><span class="badge bg-success-custom">Completed</span></td>
                                <td>
                                    <button class="btn-action"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn-action"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Rosie Pearson</td>
                                <td>Drop Off</td>
                                <td>6KG (2 Lot)</td>
                                <td>Cashless</td>
                                <td><span class="badge bg-processing">Processing</span></td>
                                <td>
                                    <button class="btn-action"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn-action"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            <!-- ... data lainnya ... -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>


</html>
