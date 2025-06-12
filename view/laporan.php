<?php
$page = "laporan";
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}

// Mode filter
$mode = $_GET['mode'] ?? 'bulanan';
$bulan_ini = $_GET['bulan'] ?? date('m');
$tahun_ini = $_GET['tahun'] ?? date('Y');
$tanggal_ini = $_GET['tanggal'] ?? date('d');

// Hari & Bulan list
$hari_list = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
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

// Generate list tanggal berdasarkan bulan dan tahun yang dipilih
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_ini, $tahun_ini);
$tanggal_list = [];
for ($i = 1; $i <= $jumlah_hari; $i++) {
    $tanggal_list[sprintf('%02d', $i)] = $i;
}

// Filter utama transaksi
$filter_query = "";
$filter_label = "";
if ($mode == 'harian') {
    $filter_query = "WHERE DATE(waktu_mulai) = '$tahun_ini-$bulan_ini-$tanggal_ini'";
    $filter_label = "Tanggal $tanggal_ini $bulan_list[$bulan_ini] $tahun_ini";
} elseif ($mode == 'bulanan') {
    $filter_query = "WHERE MONTH(waktu_mulai) = '$bulan_ini' AND YEAR(waktu_mulai) = '$tahun_ini'";
    $filter_label = "Bulan $bulan_list[$bulan_ini] $tahun_ini";
} elseif ($mode == 'tahunan') {
    $filter_query = "WHERE YEAR(waktu_mulai) = '$tahun_ini'";
    $filter_label = "Tahun $tahun_ini";
}

// Filter lain (operasional, bahan baku, pelanggan)
$filter_operasional = $filter_bahan = $filter_pelanggan = "WHERE 1";
if ($mode == 'harian') {
    $filter_operasional = "WHERE DATE(tanggal_pengeluaran) = '$tahun_ini-$bulan_ini-$tanggal_ini'";
    $filter_bahan = "WHERE DATE(tanggal_update) = '$tahun_ini-$bulan_ini-$tanggal_ini'";
    $filter_pelanggan = "WHERE DATE(tanggal_dibuat) = '$tahun_ini-$bulan_ini-$tanggal_ini'";
} elseif ($mode == 'bulanan') {
    $filter_operasional = "WHERE MONTH(tanggal_pengeluaran) = '$bulan_ini' AND YEAR(tanggal_pengeluaran) = '$tahun_ini'";
    $filter_bahan = "WHERE MONTH(tanggal_update) = '$bulan_ini' AND YEAR(tanggal_update) = '$tahun_ini'";
    $filter_pelanggan = "WHERE MONTH(tanggal_dibuat) = '$bulan_ini' AND YEAR(tanggal_dibuat) = '$tahun_ini'";
} elseif ($mode == 'tahunan') {
    $filter_operasional = "WHERE YEAR(tanggal_pengeluaran) = '$tahun_ini'";
    $filter_bahan = "WHERE YEAR(tanggal_update) = '$tahun_ini'";
    $filter_pelanggan = "WHERE YEAR(tanggal_dibuat) = '$tahun_ini'";
}

// Query Statistik
$total_order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Transaksi $filter_query"))['total'] ?? 0;
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM Pelanggan $filter_pelanggan"))['total'] ?? 0;
$total_berat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(d.jumlah_berat) AS total FROM Detail_Transaksi d JOIN Transaksi t ON d.id_transaksi = t.id_transaksi $filter_query"))['total'] ?? 0;
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) AS total FROM Transaksi WHERE status_pembayaran = 'Lunas' AND id_transaksi IN (SELECT id_transaksi FROM Transaksi $filter_query)"))['total'] ?? 0;
$total_operasional = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal_pengeluaran) AS total FROM Operasional $filter_operasional"))['total'] ?? 0;
$total_bahan_baku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(harga) AS total FROM Bahan_Baku $filter_bahan"))['total'] ?? 0;

$total_pengeluaran = $total_operasional + $total_bahan_baku;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan</title>
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
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-semibold {
            font-weight: 600;
        }
        
        /* Style untuk dropdown bulan dan tahun */
        .dropdown-menu {
            padding: 10px;
            width: 300px;
        }
        
        .month-year-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .month-year-selector select {
            flex: 1;
        }
        
        .date-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        
        .date-cell {
            padding: 5px;
            text-align: center;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .date-cell:hover {
            background-color: #f0f0f0;
        }
        
        .date-cell.selected {
            background-color: #4880FF;
            color: white;
        }
        
        .other-month {
            color: #ccc;
        }
        
        .dropdown-toggle::after {
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <div class="d-flex min-vh-100">
        <?php
        if ($_SESSION['role'] == 'owner')
            include '../layout/sidebar.php';
        else
            include '../layout/SidebarStaff.php';
        ?>

        <div class="p-4 flex-grow-1 w-100" style="margin-left: 280px; padding: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Laporan <?= ucfirst($mode) ?></h3>
                <div class="fw-semibold"><?= ucfirst($_SESSION['role']) ?></div>
            </div>

            <div class="card p-3 mb-4 shadow-sm">
                <form method="get" class="row align-items-end">
                    <input type="hidden" name="mode" value="<?= $mode ?>">
                    
                    <div class="col-md-12 mb-3">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="mode" id="modeHarian" value="harian" <?= $mode == 'harian' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="modeHarian">Harian</label>

                            <input type="radio" class="btn-check" name="mode" id="modeBulanan" value="bulanan" <?= $mode == 'bulanan' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="modeBulanan">Bulanan</label>

                            <input type="radio" class="btn-check" name="mode" id="modeTahunan" value="tahunan" <?= $mode == 'tahunan' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary" for="modeTahunan">Tahunan</label>
                        </div>
                    </div>

                    <?php if ($mode == 'harian'): ?>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Pilih Tanggal</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="dateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $tanggal_ini ?> <?= $bulan_list[$bulan_ini] ?> <?= $tahun_ini ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dateDropdown">
                                    <div class="month-year-selector">
                                        <select class="form-select" id="monthSelect" onchange="updateDateGrid()">
                                            <?php foreach ($bulan_list as $num => $nama): ?>
                                                <option value="<?= $num ?>" <?= ($bulan_ini == $num ? 'selected' : '') ?>><?= $nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select class="form-select" id="yearSelect" onchange="updateDateGrid()">
                                            <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                                <option value="<?= $i ?>" <?= ($tahun_ini == $i ? 'selected' : '') ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="date-grid" id="dateGrid">
                                        <!-- Dates will be populated by JavaScript -->
                                    </div>
                                    <input type="hidden" name="tanggal" id="selectedDate" value="<?= $tanggal_ini ?>">
                                    <input type="hidden" name="bulan" id="selectedMonth" value="<?= $bulan_ini ?>">
                                    <input type="hidden" name="tahun" id="selectedYear" value="<?= $tahun_ini ?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($mode == 'bulanan'): ?>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Pilih Bulan</label>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="monthDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= $bulan_list[$bulan_ini] ?> <?= $tahun_ini ?>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="monthDropdown">
                                    <div class="month-year-selector">
                                        <select class="form-select" name="bulan">
                                            <?php foreach ($bulan_list as $num => $nama): ?>
                                                <option value="<?= $num ?>" <?= ($bulan_ini == $num ? 'selected' : '') ?>><?= $nama ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <select class="form-select" name="tahun">
                                            <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                                <option value="<?= $i ?>" <?= ($tahun_ini == $i ? 'selected' : '') ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($mode == 'tahunan'): ?>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Pilih Tahun</label>
                            <select name="tahun" class="form-select">
                                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                                    <option value="<?= $i ?>" <?= ($tahun_ini == $i ? 'selected' : '') ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
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
                            <h5 class="fw-bold mb-3">Pengeluaran Operasional <?= $filter_label ?></h5>
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
                                        $query_detail_operasional = "SELECT * FROM Operasional $filter_operasional ORDER BY tanggal_pengeluaran DESC";
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
                            <h5 class="fw-bold mb-3">Pemasokan Bahan Baku <?= $filter_label ?></h5>
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
                                        $query_bahan_detail = "SELECT * FROM Bahan_Baku $filter_bahan ORDER BY tanggal_update DESC";
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
            
            <!-- Rincian Pelanggan dan Transaksi -->
            <div class="card mt-5 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Rincian Transaksi Pelanggan <?= $filter_label ?></h5>
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
                                    $filter_query
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
                                    echo "<tr><td colspan='4' class='text-center'>Tidak ada transaksi.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk mengupdate grid tanggal berdasarkan bulan dan tahun yang dipilih
        function updateDateGrid() {
            const monthSelect = document.getElementById('monthSelect');
            const yearSelect = document.getElementById('yearSelect');
            const dateGrid = document.getElementById('dateGrid');
            
            const month = monthSelect.value;
            const year = yearSelect.value;
            
            // Update hidden inputs
            document.getElementById('selectedMonth').value = month;
            document.getElementById('selectedYear').value = year;
            
            // Hitung hari dalam bulan dan hari pertama bulan ini
            const daysInMonth = new Date(year, month, 0).getDate();
            const firstDay = new Date(year, month - 1, 1).getDay();
            
            // Kosongkan grid
            dateGrid.innerHTML = '';
            
            // Tambahkan label hari
            const days = ['M', 'S', 'S', 'R', 'K', 'J', 'S'];
            days.forEach(day => {
                const dayCell = document.createElement('div');
                dayCell.textContent = day;
                dayCell.style.fontWeight = 'bold';
                dayCell.style.textAlign = 'center';
                dateGrid.appendChild(dayCell);
            });
            
            // Tambahkan sel kosong untuk hari sebelum bulan dimulai
            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'date-cell other-month';
                dateGrid.appendChild(emptyCell);
            }
            
            // Tambahkan tanggal
            for (let day = 1; day <= daysInMonth; day++) {
                const dateCell = document.createElement('div');
                dateCell.className = 'date-cell';
                dateCell.textContent = day;
                
                // Jika ini adalah tanggal yang dipilih, tambahkan kelas selected
                if (day == <?= $tanggal_ini ?> && month == '<?= $bulan_ini ?>' && year == <?= $tahun_ini ?>) {
                    dateCell.classList.add('selected');
                }
                
                dateCell.addEventListener('click', function() {
                    // Hapus selected dari semua sel
                    document.querySelectorAll('.date-cell').forEach(cell => {
                        cell.classList.remove('selected');
                    });
                    
                    // Tambahkan selected ke sel yang diklik
                    this.classList.add('selected');
                    
                    // Update hidden input dan teks tombol
                    document.getElementById('selectedDate').value = day.toString().padStart(2, '0');
                    document.getElementById('dateDropdown').textContent = `${day} ${monthSelect.options[monthSelect.selectedIndex].text} ${year}`;
                });
                
                dateGrid.appendChild(dateCell);
            }
        }
        
        // Inisialisasi grid tanggal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dateGrid')) {
                updateDateGrid();
            }
        });
        
        // Tangani perubahan mode
        document.querySelectorAll('input[name="mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Submit form saat mode berubah
                this.form.submit();
            });
        });
    </script>
</body>
</html>