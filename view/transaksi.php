<?php
$page = 'transaksi';
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}

if (isset($_POST['submit_all'])) {
    $nama = $_POST['nama_pelanggan'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    $query = "INSERT INTO Pelanggan (nama, no_hp, alamat) VALUES ('$nama', '$no_hp', '$alamat')";
    mysqli_query($conn, $query);
    $id_pelanggan = mysqli_insert_id($conn);

    $layanan = $_POST['id_layanan'];
    $berat = $_POST['jumlah_berat'];
    $harga_perkg = 10000;
    $subtotal = $berat * $harga_perkg;

    $metode = $_POST['metode_pembayaran'];
    $proses = $_POST['status_proses'];
    $bayar = $_POST['status_pembayaran'];
    $total = $subtotal;
    $user_id = $_SESSION['user_id'];
    $waktu = date('Y-m-d H:i:s');

    $insert = "INSERT INTO Transaksi (id_user, id_pelanggan, metode_pembayaran, status_proses, status_pembayaran, total_harga, waktu_mulai)
               VALUES ($user_id, $id_pelanggan, '$metode', '$proses', '$bayar', $total, '$waktu')";
    mysqli_query($conn, $insert);
    $id_transaksi = mysqli_insert_id($conn);

    $insertDetail = "INSERT INTO Detail_Transaksi (id_transaksi, id_layanan, sub_total, jumlah_berat)
                     VALUES ($id_transaksi, $layanan, $subtotal, $berat)";
    mysqli_query($conn, $insertDetail);

    header("Location: transaksi.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/DashboardOwner.css">
</head>
<body style="font-family: 'Nunito Sans', sans-serif; background-color: #F8F9FC;">
    <div class="d-flex">
        <?php 
        if($_SESSION['role'] == 'owner')
            include '../layout/sidebar.php'; 
        else 
            include '../layout/SidebarStaff.php';
        ?>

        <div class="p-4 flex-grow-1 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Transaksi</h3>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksi">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Transaksi
                </button>
            </div>

            <!-- Modal Tambah Transaksi -->
            <div class="modal fade" id="modalTambahTransaksi" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Transaksi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Step 1 -->
                            <div id="step1">
                                <h6>Data Pelanggan</h6>
                                <div class="mb-2">
                                    <label>Nama</label>
                                    <input type="text" name="nama_pelanggan" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label>No HP</label>
                                    <input type="text" name="no_hp" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control"></textarea>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="nextStep()">Lanjut</button>
                            </div>

                            <!-- Step 2 -->
                            <div id="step2" style="display: none;">
                                <h6>Data Laundry</h6>
                                <div class="mb-2">
                                    <label>Layanan</label>
                                    <select name="id_layanan" class="form-control" required>
                                        <?php 
                                        $layanan = mysqli_query($conn, "SELECT * FROM Layanan");
                                        while ($row = mysqli_fetch_assoc($layanan)): ?>
                                            <option value="<?= $row['id_layanan'] ?>"><?= $row['nama_layanan'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label>Berat (KG)</label>
                                    <input type="number" name="jumlah_berat" step="0.1" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label>Metode Pembayaran</label>
                                    <select name="metode_pembayaran" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Cashless">Cashless</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label>Status Proses</label>
                                    <select name="status_proses" class="form-control">
                                        <option value="Processing">Processing</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label>Status Pembayaran</label>
                                    <select name="status_pembayaran" class="form-control">
                                        <option value="Belum Lunas">Belum Lunas</option>
                                        <option value="Lunas">Lunas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit_all" class="btn btn-success" style="display:none" id="btnSimpan">Simpan Transaksi</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Transaksi bisa kamu tambahkan di sini -->
        </div>
    </div>

    <script>
        function nextStep() {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('btnSimpan').style.display = 'inline-block';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
