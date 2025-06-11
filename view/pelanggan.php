<?php
$page = 'pelanggan';
include '../database/connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}

// Filter bulan & tahun
$selected_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Pagination
$limit = 15;
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page_number - 1) * $limit;

// Daftar nama bulan
$bulan_list = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Hitung total data
$count_query = "
    SELECT COUNT(DISTINCT p.id_pelanggan) AS total 
    FROM pelanggan p
    JOIN transaksi t ON p.id_pelanggan = t.id_pelanggan
    WHERE MONTH(t.waktu_mulai) = '$selected_bulan' AND YEAR(t.waktu_mulai) = '$selected_tahun'
";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Query data dengan limit
$query = "
    SELECT DISTINCT p.* 
    FROM pelanggan p
    JOIN transaksi t ON p.id_pelanggan = t.id_pelanggan
    WHERE MONTH(t.waktu_mulai) = '$selected_bulan' AND YEAR(t.waktu_mulai) = '$selected_tahun'
    ORDER BY p.id_pelanggan ASC
    LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $query);
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
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #F8F9FC;
        }

        .text-primary-custom {
            color: #4880FF;
            font-weight: 700;
        }

        .rheza {
            color: #4880FF;
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

        .table td, .table th {
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
    if ($_SESSION['role'] == 'owner')
        include '../layout/sidebar.php';
    else
        include '../layout/SidebarStaff.php';
    ?>

    <div class="p-4 flex-grow-1 w-100" style="margin-left: 280px; padding: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title">Pelanggan</h3>
            <div class="fw-semibold"><?= ucfirst($_SESSION['role']) ?></div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <!-- Title & Search Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-2 fw-bold">Daftar Pelanggan</h5>
                        <h6 class="fw-semibold mb-0 text-muted">
                            Menampilkan pelanggan dengan transaksi di bulan <?= $bulan_list[$selected_bulan] ?? '' ?> <?= $selected_tahun ?>
                        </h6>
                    </div>
                    <input type="text" id="searchInput" class="search-bar" placeholder="Cari nama atau no HP...">
                </div>

                <!-- Filter Bulan & Tahun -->
                <form method="GET" class="d-flex align-items-center gap-2 mb-3">
                    <select name="bulan" class="form-select dropdown-month" onchange="this.form.submit()">
                        <?php
                        foreach ($bulan_list as $key => $val) {
                            $selected = ($selected_bulan == $key) ? 'selected' : '';
                            echo "<option value='$key' $selected>$val</option>";
                        }
                        ?>
                    </select>
 
                    <select name="tahun" class="form-select dropdown-month" onchange="this.form.submit()">
                        <?php
                        $current_year = date('Y');
                        for ($i = $current_year; $i >= $current_year - 5; $i--) {
                            $selected = ($selected_tahun == $i) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </form>

                <!-- Table -->
                <table class="table table-hover table-bordered" id="pelangganTable">
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
                    $no = $offset + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['no_hp']} <a href='https://wa.me/62" . substr($row['no_hp'], 1) . "' target='_blank'><i class='bi bi-whatsapp whatsapp-icon ms-2'></i></a></td>
                            <td>{$row['alamat']}</td>
                        </tr>";
                        $no++;
                    }

                    if ($no === $offset + 1) {
                        echo "<tr><td colspan='4' class='text-center'>Tidak ada pelanggan bulan ini.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
<nav>
    <ul class="pagination justify-content-center mt-3">
        <?php
        $range = 2; // jumlah halaman sebelum dan sesudah halaman aktif
        $start = max(1, $page_number - $range);
        $end = min($total_pages, $page_number + $range);

        // Tampilkan halaman pertama
        if ($start > 1) {
            $first_params = http_build_query(['bulan' => $selected_bulan, 'tahun' => $selected_tahun, 'page' => 1]);
            echo "<li class='page-item'><a class='page-link' href='?$first_params'>1</a></li>";
            if ($start > 2) {
                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
        }

        // Halaman tengah (sekitar aktif)
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page_number) ? 'active' : '';
            $params = http_build_query(['bulan' => $selected_bulan, 'tahun' => $selected_tahun, 'page' => $i]);
            echo "<li class='page-item $active'><a class='page-link' href='?$params'>$i</a></li>";
        }

        // Tampilkan halaman terakhir
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }
            $last_params = http_build_query(['bulan' => $selected_bulan, 'tahun' => $selected_tahun, 'page' => $total_pages]);
            echo "<li class='page-item'><a class='page-link' href='?$last_params'>$total_pages</a></li>";
        }
        ?>
    </ul>
</nav>
<?php endif; ?>


            </div>
        </div>
    </div>
</div>

<!-- Script Search -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("searchInput").addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll("#pelangganTable tbody tr");

        rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            const hp = row.cells[2].textContent.toLowerCase();
            if (nama.includes(keyword) || hp.includes(keyword)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>
</body>
</html>
