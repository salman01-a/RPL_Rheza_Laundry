    <?php
    $page = 'dashboard';
    include '../database/connection.php';
    session_start();

    $bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
    $tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

    $bulan_list = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];


    if (!isset($_SESSION['user_id'])) {
        header("Location: ../");
        exit();
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // Statistik Utama
    $total_pelanggan = 0;
    $total_pendapatan = 0;
    $total_order = 0;
    $order_processing = 0;

    // Ambil Total Pelanggan
    $query_pelanggan = "SELECT COUNT(*) AS total FROM Pelanggan";
    $result_pelanggan = mysqli_query($conn, $query_pelanggan);
    if ($row = mysqli_fetch_assoc($result_pelanggan)) {
        $total_pelanggan = $row['total'];
    }

    // Ambil Total Pendapatan
    $query_pendapatan = "SELECT SUM(total_harga) AS total FROM Transaksi WHERE status_pembayaran = 'Lunas'";
    $result_pendapatan = mysqli_query($conn, $query_pendapatan);
    if ($row = mysqli_fetch_assoc($result_pendapatan)) {
        $total_pendapatan = $row['total'] ?? 0;
    }

    // Ambil Total Order
    $query_order = "SELECT COUNT(*) AS total FROM Transaksi";
    $result_order = mysqli_query($conn, $query_order);
    if ($row = mysqli_fetch_assoc($result_order)) {
        $total_order = $row['total'];
    }

    // Ambil Order yang sedang diproses
    $query_processing = "SELECT COUNT(*) AS total FROM Transaksi WHERE status_proses = 'Processing'";
    $result_processing = mysqli_query($conn, $query_processing);
    if ($row = mysqli_fetch_assoc($result_processing)) {
        $order_processing = $row['total'];
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
        <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">

        <style>
            body {
                font-family: 'Nunito Sans', sans-serif;
                background-color: #F8F9FC;
            }

            .rheza {
                color: #4880FF;
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

            .table> :not(caption)>*>* {
                vertical-align: middle;
            }
        </style>
    </head>

    <body>
        <div class="d-flex">
            <?php
            if ($_SESSION['role'] == 'owner')
                include '../layout/sidebar.php';
            else
                include '../layout/SidebarStaff.php';
            ?>

            <div class="p-4 flex-grow-1 w-100" style="margin-left: 280px; padding: 20px">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold">Dashboard</h3>
                    <div class="fw-semibold"><?= ucfirst($_SESSION['role']) ?></div>
                </div>

                <?php if ($_SESSION['role'] == 'owner') { ?>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-people-fill icon-dashboard"></i>
                                    <h6>Total Pelanggan</h6>
                                    <h3 class="text-primary-custom"><?php echo $total_pelanggan; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-currency-dollar icon-dashboard"></i>
                                    <h6>Total Pendapatan</h6>
                                    <h3 class="text-primary-custom">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-basket-fill icon-dashboard"></i>
                                    <h6>Total Order</h6>
                                    <h3 class="text-primary-custom"><?php echo $total_order; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-clock-history icon-dashboard"></i>
                                    <h6>Order In Proses</h6>
                                    <h3 class="text-primary-custom"><?php echo $order_processing; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-people-fill icon-dashboard"></i>
                                    <h6>Total Pelanggan</h6>
                                    <h3 class="text-primary-custom"><?php echo $total_pelanggan; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="bi bi-clock-history icon-dashboard"></i>
                                    <h6>Order In Proses</h6>
                                    <h3 class="text-primary-custom"><?php echo $order_processing; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Detail Transaksi Bulan <?= $bulan_list[$bulan] ?> <?= $tahun ?></h5> 
                            <form method="GET" class="d-flex align-items-center gap-2">
                                <select name="bulan" class="form-select w-auto" onchange="this.form.submit()">
                                    <?php
                                    foreach ($bulan_list as $key => $value) {
                                        $selected = ($bulan == $key) ? 'selected' : '';
                                        echo "<option value='$key' $selected>$value</option>";
                                    }
                                    ?>
                                </select>

                                <select name="tahun" class="form-select w-auto" onchange="this.form.submit()">
                                    <?php
                                    $tahun_sekarang = date('Y');
                                    for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--) {
                                        $selected = ($tahun == $i) ? 'selected' : '';
                                        echo "<option value='$i' $selected>$i</option>";
                                    }
                                    ?>
                                </select>
                            </form>
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
                                    <th>Status Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;

                                $query_transaksi = "
    SELECT t.*, p.nama, d.jumlah_berat, l.nama_layanan 
    FROM Transaksi t
    JOIN Pelanggan p ON t.id_pelanggan = p.id_pelanggan
    JOIN Detail_Transaksi d ON d.id_transaksi = t.id_transaksi
    JOIN Layanan l ON l.id_layanan = d.id_layanan
    WHERE MONTH(t.waktu_mulai) = '$bulan' AND YEAR(t.waktu_mulai) = '$tahun'
    ORDER BY t.id_transaksi DESC Limit 7
";


                                $result_transaksi = mysqli_query($conn, $query_transaksi);
                                while ($row = mysqli_fetch_assoc($result_transaksi)) {
                                    $status = $row['status_proses'];
                                    $badge_class = $status === 'Completed' ? 'bg-success-custom' : ($status === 'Processing' ? 'bg-processing' : 'bg-secondary');
                                    echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama']}</td>
            <td>{$row['nama_layanan']}</td>
            <td>{$row['jumlah_berat']}Kg</td>
            <td>{$row['metode_pembayaran']}</td>
            <td>{$row['status_pembayaran']}</td>    
            <td><span class='badge $badge_class'>{$status}</span></td>
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