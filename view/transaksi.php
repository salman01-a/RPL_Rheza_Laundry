<?php
include '../database/connection.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total transaksi
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Transaksi");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Tambah transaksi
if (isset($_POST['submit_all'])) {
    $nama = $_POST['nama_pelanggan'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    mysqli_query($conn, "INSERT INTO Pelanggan (nama, no_hp, alamat) VALUES ('$nama', '$no_hp', '$alamat')");
    $id_pelanggan = mysqli_insert_id($conn);

    $layanan = $_POST['id_layanan'];
$berat = $_POST['jumlah_berat'];

// Ambil harga dari tabel Layanan
$result = mysqli_query($conn, "SELECT harga_layanan FROM Layanan WHERE id_layanan = $layanan");
$row = mysqli_fetch_assoc($result);
$harga_perkg = $row['harga_layanan'];

$subtotal = $berat * $harga_perkg;

    $metode = $_POST['metode_pembayaran'];
    $proses = $_POST['status_proses'];
    $bayar = $_POST['status_pembayaran'];
    $total = $subtotal;
    $user_id = $_SESSION['user_id'];
    $waktu = date('Y-m-d H:i:s');

    mysqli_query($conn, "INSERT INTO Transaksi (id_user, id_pelanggan, metode_pembayaran, status_proses, status_pembayaran, total_harga, waktu_mulai) VALUES ($user_id, $id_pelanggan, '$metode', '$proses', '$bayar', $total, '$waktu')");
    $id_transaksi = mysqli_insert_id($conn);

    mysqli_query($conn, "INSERT INTO Detail_Transaksi (id_transaksi, id_layanan, sub_total, jumlah_berat) VALUES ($id_transaksi, $layanan, $subtotal, $berat)");

    header("Location: transaksi.php?success=1");
    exit();
}

// Hapus transaksi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM Detail_Transaksi WHERE id_transaksi=$id");
    mysqli_query($conn, "DELETE FROM Transaksi WHERE id_transaksi=$id");
    header("Location: transaksi.php?success=2");
    exit();
}

// Update transaksi
if (isset($_POST['update_transaksi'])) {
    $id_transaksi = (int)$_POST['id_transaksi'];
    $id_pelanggan = (int)$_POST['id_pelanggan'];

    $nama = mysqli_real_escape_string($conn, $_POST['edit_nama_pelanggan']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['edit_no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['edit_alamat']);

    $id_layanan = (int)$_POST['edit_id_layanan'];
    $jumlah_berat = (float)$_POST['edit_jumlah_berat'];

    // Ambil harga layanan
    $getHarga = mysqli_query($conn, "SELECT harga_layanan FROM Layanan WHERE id_layanan = $id_layanan");
    $hargaData = mysqli_fetch_assoc($getHarga);
    $harga = $hargaData ? $hargaData['harga_layanan'] : 0;
    $sub_total = $harga * $jumlah_berat;

    $metode = mysqli_real_escape_string($conn, $_POST['edit_metode_pembayaran']);
    $status_proses = mysqli_real_escape_string($conn, $_POST['edit_status_proses']);
    $status_pembayaran = mysqli_real_escape_string($conn, $_POST['edit_status_pembayaran']);

    // Update data pelanggan
    $updatePelanggan = mysqli_query($conn, "
        UPDATE Pelanggan 
        SET nama='$nama', no_hp='$no_hp', alamat='$alamat' 
        WHERE id_pelanggan=$id_pelanggan
    ");

    // Update transaksi
    $updateTransaksi = mysqli_query($conn, "
        UPDATE Transaksi 
        SET metode_pembayaran='$metode', status_proses='$status_proses', status_pembayaran='$status_pembayaran', total_harga=$sub_total 
        WHERE id_transaksi=$id_transaksi
    ");

    // Update detail transaksi
    $updateDetail = mysqli_query($conn, "
        UPDATE Detail_Transaksi 
        SET id_layanan=$id_layanan, jumlah_berat=$jumlah_berat, sub_total=$sub_total 
        WHERE id_transaksi=$id_transaksi
    ");

    // Kirim WhatsApp jika selesai
    if ($status_proses === 'Completed') {
        $getLayanan = mysqli_query($conn, "SELECT * FROM Layanan WHERE id_layanan = $id_layanan");
        $Data = mysqli_fetch_assoc($getLayanan);
        $layanan = $Data['nama_layanan'];
        include '../service/whatsappapi.php';
        kirimPesanWA($no_hp, $nama, $layanan, $jumlah_berat, $sub_total, $metode);
    }

    // Redirect jika sukses
    header("Location: transaksi.php?success=3");
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
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">
    <style>
        body { font-family: 'Nunito Sans', sans-serif; background-color: #F8F9FC; }
        .rheza { color: #4880FF; }
    </style>
</head>
<body>
<div class="d-flex">
    <?php
    $Cpage = 'transaksi';
    if ($_SESSION['role'] == 'owner') include '../layout/sidebar.php';
    else include '../layout/SidebarStaff.php';
    ?>

    <div class="p-4 flex-grow-1 w-100" style="margin-left: 280px; padding: 20px;">


    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?php 
          if ($_GET['success'] == '1') echo "Data Transaksi berhasil ditambahkan.";
          elseif ($_GET['success'] == '3') echo "Data Transaksi berhasil diperbarui.";
          elseif ($_GET['success'] == '2') echo "Data Transaksi berhasil dihapus.";
          ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Transaksi</h3>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksi">
                <i class="bi bi-plus-circle me-1"></i>Tambah Transaksi
            </button>
        </div>
   
      
    
        <!-- Table -->
        <div class="table-responsive bg-white rounded p-3 shadow-sm">
              <!-- Search Bar -->
        <div class="d-flex justify-content-end mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pelanggan, layanan, atau status..." style="width: 300px;">
        </div>
            <table class="table table-bordered table-hover align-middle" id="transaksiTable">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Berat</th>
                        <th>Metode Pembayaran</th>
                        <th>Total Harga</th>
                        <th>Status Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conn, "
                      SELECT t.id_transaksi, p.id_pelanggan, p.nama AS pelanggan, p.no_hp, p.alamat,
           l.id_layanan, l.nama_layanan, l.harga_layanan,
           d.jumlah_berat, d.sub_total,
           t.status_pembayaran, t.status_proses, t.metode_pembayaran
    FROM Transaksi t
    JOIN Pelanggan p ON t.id_pelanggan=p.id_pelanggan
    JOIN Detail_Transaksi d ON d.id_transaksi=t.id_transaksi
    JOIN Layanan l ON l.id_layanan=d.id_layanan
    ORDER BY t.id_transaksi DESC LIMIT $limit OFFSET $offset
                    ");
                    $no = 1 + $offset;
                    $modals = '';
                    while ($row = mysqli_fetch_assoc($query)):
                        $badge = $row['status_proses'] === 'Completed' ? 'success' : ($row['status_proses'] === 'Processing' ? 'primary' : 'danger');
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['pelanggan'] ?></td>
                        <td><?= $row['nama_layanan'] ?></td>
                        <td><?= $row['jumlah_berat'] ?> Lot</td>
                        <td><?= $row['metode_pembayaran'] ?></td>
                        <td><?= $row['sub_total'] ?></td>
                        <td><?= $row['status_pembayaran'] ?></td>
                        <td><span class="badge bg-<?= $badge ?>"><?= $row['status_proses'] ?></span></td>
                        <td>
                            <div class="d-inline-flex border rounded-3 overflow-hidden bg-light">
                                <a class="d-flex align-items-center justify-content-center px-3 py-2 text-secondary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_transaksi'] ?>">
                                    <i class="bi bi-pencil-square fs-10"></i>
                                </a>
                                <div class="border-start"></div>
                                <a href="?hapus=<?= $row['id_transaksi'] ?>" class="d-flex align-items-center justify-content-center px-3 py-2 text-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                    <i class="bi bi-trash3 fs-10"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <?php
                   $modals .= '
                   <div class="modal fade" id="modalEdit' . $row['id_transaksi'] . '" tabindex="-1">
                       <div class="modal-dialog modal-lg">
                           <form method="POST" class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title">Edit Transaksi</h5>
                                   <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                               </div>
                               <div class="modal-body">
                                   <input type="hidden" name="id_transaksi" value="' . $row['id_transaksi'] . '">
                                   
                                   <h6>Data Pelanggan</h6>
                                   <input type="hidden" name="id_pelanggan" value="' . $row['id_pelanggan'] . '">
                                   <div class="mb-2"><label>Nama</label><input type="text" name="edit_nama_pelanggan" class="form-control" value="' . $row['pelanggan'] . '" required></div>
                                   <div class="mb-2"><label>No HP</label><input type="text" name="edit_no_hp" class="form-control" value="' . $row['no_hp'] . '"></div>
                                   <div class="mb-3"><label>Alamat</label><textarea name="edit_alamat" class="form-control">' . $row['alamat'] . '</textarea></div>
               
                                   <h6>Data Laundry</h6>
                                   <div class="mb-2">
                                       <label>Layanan</label>
                                       <select name="edit_id_layanan" class="form-control">';
                                       $layanan_q = mysqli_query($conn, "SELECT * FROM Layanan");
                                       while ($lay = mysqli_fetch_assoc($layanan_q)) {
                                           $selected = ($lay['nama_layanan'] == $row['nama_layanan']) ? 'selected' : '';
                                           $modals .= '<option value="' . $lay['id_layanan'] . '" ' . $selected . '>' . $lay['nama_layanan'] . '</option>';
                                       }
                   $modals .= '</select>
                                   </div>
                                   <div class="mb-2"><label>Berat (Lot)</label><input type="number" name="edit_jumlah_berat" class="form-control" value="' . $row['jumlah_berat'] . '" required></div>
                                   <div class="mb-2"><label>Metode Pembayaran</label>
                                       <select name="edit_metode_pembayaran" class="form-control">
                                           <option value="Tunai" ' . ($row['metode_pembayaran'] == 'Tunai' ? 'selected' : '') . '>Tunai</option>
                                           <option value="Transfer" ' . ($row['metode_pembayaran'] == 'Transfer' ? 'selected' : '') . '>Transfer</option>
                                       </select>
                                   </div>
                                   <div class="mb-2"><label>Status Proses</label>
                                       <select name="edit_status_proses" class="form-control">
                                           <option value="Processing" ' . ($row['status_proses'] == 'Processing' ? 'selected' : '') . '>Processing</option>
                                           <option value="Completed" ' . ($row['status_proses'] == 'Completed' ? 'selected' : '') . '>Completed</option>
                                           <option value="Rejected" ' . ($row['status_proses'] == 'Rejected' ? 'selected' : '') . '>Rejected</option>
                                       </select>
                                   </div>
                                   <div class="mb-2"><label>Status Pembayaran</label>
                                       <select name="edit_status_pembayaran" class="form-control">
                                           <option value="Belum Lunas" ' . ($row['status_pembayaran'] == 'Belum Lunas' ? 'selected' : '') . '>Belum Lunas</option>
                                           <option value="Lunas" ' . ($row['status_pembayaran'] == 'Lunas' ? 'selected' : '') . '>Lunas</option>
                                       </select>
                                   </div>
                               </div>
                               <div class="modal-footer">
                                   <button type="submit" name="update_transaksi" class="btn btn-success">Simpan</button>
                               </div>
                           </form>
                       </div>
                   </div>';
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <?php
                $range = 2;
                $start = max(1, $page - $range);
                $end = min($total_pages, $page + $range);

                if ($start > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                    if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }

                for ($i = $start; $i <= $end; $i++) {
                    $active = ($i == $page) ? 'active' : '';
                    echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
                }

                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo "<li class='page-item'><a class='page-link' href='?page=$total_pages'>$total_pages</a></li>";
                }
                ?>
            </ul>
        </nav>
        <?php endif; ?>

        <?= $modals ?>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalTambahTransaksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="step1">
                    <h6>Data Pelanggan</h6>
                    <div class="mb-2"><label>Nama</label><input type="text" name="nama_pelanggan" class="form-control" required></div>
                    <div class="mb-2"><label>No HP</label><input type="text" name="no_hp" class="form-control"></div>
                    <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"></textarea></div>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">Lanjut</button>
                </div>
                <div id="step2" style="display: none;">
                    <h6>Data Laundry</h6>
                    <div class="mb-2">
                        <label>Layanan</label>
                        <select name="id_layanan" class="form-control" required>
                            <?php
                            $layanan = mysqli_query($conn, "SELECT * FROM Layanan");
                            while ($row = mysqli_fetch_assoc($layanan)):
                            ?>
                                <option value="<?= $row['id_layanan'] ?>"><?= $row['nama_layanan'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-2"><label>Berat (Lot)</label><input type="number" name="jumlah_berat" class="form-control" required></div>
                    <div class="mb-2"><label>Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-2"><label>Status Proses</label>
                        <select name="status_proses" class="form-control">
                            <option value="Processing">Processing</option>
                            <option value="Completed">Completed</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-2"><label>Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-control">
                            <option value="Belum Lunas">Belum Lunas</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>
                    <button type="submit" name="submit_all" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function nextStep() {
    document.getElementById("step1").style.display = "none";
    document.getElementById("step2").style.display = "block";
}

document.getElementById("searchInput").addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#transaksiTable tbody tr");

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
    });
});
</script>
</body>
</html>
