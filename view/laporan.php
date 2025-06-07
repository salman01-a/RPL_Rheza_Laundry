<?php
$page = "laporan";
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}

// Ambil bulan dan tahun dari parameter GET (default: bulan ini)
$bulan_ini = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_ini = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Total Order
$query_order = "SELECT COUNT(*) AS total FROM Transaksi WHERE MONTH(waktu_mulai) = '$bulan_ini' AND YEAR(waktu_mulai) = '$tahun_ini'";
$total_order = mysqli_fetch_assoc(mysqli_query($conn, $query_order))['total'] ?? 0;

// Total Pelanggan
$query_pelanggan = "
    SELECT COUNT(*) AS total 
    FROM Pelanggan 
    WHERE MONTH(tanggal_dibuat) = '$bulan_ini' AND YEAR(tanggal_dibuat) = '$tahun_ini'
";
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, $query_pelanggan))['total'] ?? 0;

// Total Berat
$query_berat = "SELECT SUM(jumlah_berat) AS total FROM Detail_Transaksi 
JOIN Transaksi ON Detail_Transaksi.id_transaksi = Transaksi.id_transaksi 
WHERE MONTH(waktu_mulai) = '$bulan_ini' AND YEAR(waktu_mulai) = '$tahun_ini'";
$total_berat = mysqli_fetch_assoc(mysqli_query($conn, $query_berat))['total'] ?? 0;

// Total Pendapatan
$query_pendapatan = "SELECT SUM(total_harga) AS total FROM Transaksi WHERE status_pembayaran = 'Lunas' AND MONTH(waktu_mulai) = '$bulan_ini' AND YEAR(waktu_mulai) = '$tahun_ini'";
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, $query_pendapatan))['total'] ?? 0;

// Total Pengeluaran Operasional
$query_operasional = "SELECT SUM(nominal_pengeluaran) AS total FROM Operasional WHERE MONTH(tanggal_pengeluaran) = '$bulan_ini' AND YEAR(tanggal_pengeluaran) = '$tahun_ini'";
$total_operasional = mysqli_fetch_assoc(mysqli_query($conn, $query_operasional))['total'] ?? 0;

// Total Pengeluaran Bahan Baku
$query_bahan = "SELECT SUM(harga) AS total FROM Bahan_Baku WHERE MONTH(tanggal_update) = '$bulan_ini' AND YEAR(tanggal_update) = '$tahun_ini'";
$total_bahan_baku = mysqli_fetch_assoc(mysqli_query($conn, $query_bahan))['total'] ?? 0;

// Total Pengeluaran = Operasional + Bahan Baku
$total_pengeluaran = $total_operasional + $total_bahan_baku;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/DashboardOwner.css">
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #F8F9FC;
        }

        .icon-dashboard {
            font-size: 2rem;
            color: #4880FF;
            margin-bottom: 10px;
        }

        .text-primary-custom {
            color: #4880FF;
            font-weight: 700;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-semibold {
            font-weight: 600;
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
            <h3 class="fw-bold">Laporan Bulanan</h3>
            <div class="fw-semibold"><?= ucfirst($_SESSION['role']) ?></div>
        </div>

        <!-- Filter Bulan & Tahun -->
        <div class="card p-3 mb-4 shadow-sm">
            <form method="get" class="row align-items-end">
                <div class="col-md-3">
                    <label for="bulan" class="form-label fw-semibold">Pilih Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        <?php
                        $bulan_list = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        foreach ($bulan_list as $num => $nama) {
                            $selected = ($num == $bulan_ini) ? 'selected' : '';
                            echo "<option value='$num' $selected>$nama</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="tahun" class="form-label fw-semibold">Pilih Tahun</label>
                    <select name="tahun" id="tahun" class="form-select">
                        <?php
                        $tahun_sekarang = date('Y');
                        for ($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--) {
                            $selected = ($i == $tahun_ini) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary mt-3">Tampilkan</button>
                </div>
            </form>
        </div>

        <!-- Kartu Statistik -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-cash-coin icon-dashboard"></i>
                    <h6>Total Pengeluaran</h6>
                    <h4 class="text-primary-custom">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-currency-dollar icon-dashboard"></i>
                    <h6>Total Pendapatan</h6>
                    <h4 class="text-primary-custom">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-basket icon-dashboard"></i>
                    <h6>Total Berat</h6>
                    <h4 class="text-primary-custom"><?= number_format($total_berat, 2) ?> Kg</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-list-check icon-dashboard"></i>
                    <h6>Total Order</h6>
                    <h4 class="text-primary-custom"><?= $total_order ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-people icon-dashboard"></i>
                    <h6>Total Pelanggan</h6>
                    <h4 class="text-primary-custom"><?= $total_pelanggan ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-box-seam icon-dashboard"></i>
                    <h6>Pemasokan Bahan Baku</h6>
                    <h4 class="text-primary-custom">Rp <?= number_format($total_bahan_baku, 0, ',', '.') ?></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <i class="bi bi-lightning-charge-fill icon-dashboard"></i>
                    <h6>Pengeluaran Operasional</h6>
                    <h4 class="text-primary-custom">Rp <?= number_format($total_operasional, 0, ',', '.') ?></h4>
                </div>
            </div>
       
     </div>

     <!-- Rincian Operasional & Bahan Baku Berdampingan -->
<div class="row mt-5">
    <!-- Tabel Operasional -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Pengeluaran Operasional</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query_detail_operasional = "
                                SELECT * FROM Operasional 
                                WHERE MONTH(tanggal_pengeluaran) = '$bulan_ini' AND YEAR(tanggal_pengeluaran) = '$tahun_ini'
                                ORDER BY tanggal_pengeluaran DESC
                            ";
                            $result_detail = mysqli_query($conn, $query_detail_operasional);
                            while ($row = mysqli_fetch_assoc($result_detail)) {
                                echo "<tr>
                                        <td>{$no}</td>
                                        <td>" . date('d-m-Y', strtotime($row['tanggal_pengeluaran'])) . "</td>
                                        <td>{$row['jenis_pengeluaran']}</td>
                                        <td>Rp " . number_format($row['nominal_pengeluaran'], 0, ',', '.') . "</td>
                                      </tr>";
                                $no++;
                            }

                            if ($no === 1) {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Bahan Baku -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Pemasokan Bahan Baku</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query_bahan_detail = "
                                SELECT * FROM Bahan_Baku
                                WHERE MONTH(tanggal_update) = '$bulan_ini' AND YEAR(tanggal_update) = '$tahun_ini'
                                ORDER BY tanggal_update DESC
                            ";
                            $result_bahan = mysqli_query($conn, $query_bahan_detail);
                            while ($row = mysqli_fetch_assoc($result_bahan)) {
                                echo "<tr>
                                        <td>{$no}</td>
                                        <td>" . date('d-m-Y', strtotime($row['tanggal_update'])) . "</td>
                                        <td>{$row['nama_barang']}</td>
                                        <td>{$row['jumlah_stok']}</td>
                                        <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                                      </tr>";
                                $no++;
                            }

                            if ($no === 1) {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada data.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Rincian Pelanggan dan Transaksi Bulan Ini -->
<div class="card mt-5 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Rincian Transaksi Pelanggan Bulan <?= $bulan_ini ?>/<?= $tahun_ini ?></h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pelanggan</th>
                        <th>Jumlah Berat (Kg)</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query_pelanggan_transaksi = "
                        SELECT p.nama, 
                               SUM(d.jumlah_berat) AS total_berat, 
                               SUM(t.total_harga) AS total_harga
                        FROM Transaksi t
                        JOIN Pelanggan p ON p.id_pelanggan = t.id_pelanggan
                        JOIN Detail_Transaksi d ON d.id_transaksi = t.id_transaksi
                        WHERE MONTH(t.waktu_mulai) = '$bulan_ini' AND YEAR(t.waktu_mulai) = '$tahun_ini'
                        GROUP BY p.id_pelanggan
                        ORDER BY p.nama ASC
                    ";

                    $result_pelanggan = mysqli_query($conn, $query_pelanggan_transaksi);
                    while ($row = mysqli_fetch_assoc($result_pelanggan)) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama']}</td>
                                <td>" . number_format($row['total_berat'], 2) . " Kg</td>
                                <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                              </tr>";
                        $no++;
                    }

                    if ($no === 1) {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada transaksi bulan ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    </div>


    
</div>

</body>
</html>
