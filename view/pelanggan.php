<?php
$page = 'pelanggan';
include '../database/connection.php';
session_start();
$selected_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

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
        if ($_SESSION['role'] == 'owner')
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
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <select name="bulan" class="form-select dropdown-month" onchange="this.form.submit()">
                                <?php
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
                                $selected_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
                                foreach ($bulan_list as $key => $val) {
                                    $selected = ($selected_bulan == $key) ? 'selected' : '';
                                    echo "<option value='$key' $selected>$val</option>";
                                }
                                ?>
                            </select>

                            <select name="tahun" class="form-select dropdown-month" onchange="this.form.submit()">
                                <?php
                                $current_year = date('Y');
                                $selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $current_year;
                                for ($i = $current_year; $i >= $current_year - 5; $i--) {
                                    $selected = ($selected_tahun == $i) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </form>

                    </div>
<h6 class="fw-bold mb-3">Menampilkan pelanggan dengan transaksi di bulan <?= $bulan_list[$selected_bulan] ?> <?= $selected_tahun ?></h6>
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
                            $query = "
    SELECT DISTINCT p.* 
    FROM pelanggan p
    JOIN transaksi t ON p.id_pelanggan = t.id_pelanggan
    WHERE MONTH(t.waktu_mulai) = '$selected_bulan' AND YEAR(t.waktu_mulai) = '$selected_tahun'
    ORDER BY p.id_pelanggan ASC
";

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