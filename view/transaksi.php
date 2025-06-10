    <?php
    $page = 'transaksi';
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

    // Hapus transaksi
    if (isset($_GET['hapus'])) {
        $id = $_GET['hapus'];
        mysqli_query($conn, "DELETE FROM Detail_Transaksi WHERE id_transaksi=$id");
        mysqli_query($conn, "DELETE FROM Transaksi WHERE id_transaksi=$id");
        header("Location: transaksi.php?deleted=1");
        exit();
    }

    // Update transaksi
    if (isset($_POST['update_transaksi'])) {
        $id = $_POST['id_transaksi'];
        $proses = $_POST['edit_status_proses'];
        $bayar = $_POST['edit_status_pembayaran'];

        if ($proses === 'Completed') {
            include '../service/whatsappapi.php';
            $getPelanggan = mysqli_query($conn, "
                SELECT p.no_hp, p.nama 
                FROM Transaksi t
                JOIN Pelanggan p ON t.id_pelanggan = p.id_pelanggan
                WHERE t.id_transaksi = $id
            ");
            $pelanggan = mysqli_fetch_assoc($getPelanggan);
            $nomor = $pelanggan['no_hp'];
            $nama = $pelanggan['nama'];

            kirimPesanWA($nomor, $nama);
        }

        mysqli_query($conn, "UPDATE Transaksi SET status_proses='$proses', status_pembayaran='$bayar' WHERE id_transaksi=$id");
        header("Location: transaksi.php?updated=1");
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
        <style>
            .rheza {
                    color: #4880FF;
                }
        </style>
    </head>

    <body style="font-family: 'Nunito Sans', sans-serif; background-color: #F8F9FC;">
    <div class="d-flex">
        <?php
        if ($_SESSION['role'] == 'owner')
            include '../layout/sidebar.php';
        else
            include '../layout/SidebarStaff.php';
        ?>

        <div class="p-4 flex-grow-1 w-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Transaksi</h3>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksi">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Transaksi
                </button>
            </div>

            <!-- Search Bar -->
            <div class="d-flex justify-content-end mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari pelanggan, layanan, atau status..." style="width: 300px;">
            </div>

            <!-- Table -->
            <div class="table-responsive bg-white rounded p-3 shadow-sm">
                <table class="table table-bordered table-hover align-middle" id="transaksiTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Berat</th>
                            <th>Metode</th>
                            <th>Status Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "
                            SELECT t.*, p.nama, d.jumlah_berat, l.nama_layanan 
        FROM Transaksi t
        JOIN Pelanggan p ON t.id_pelanggan = p.id_pelanggan
        JOIN Detail_Transaksi d ON d.id_transaksi = t.id_transaksi
        JOIN Layanan l ON l.id_layanan = d.id_layanan
        ORDER BY t.id_transaksi DESC
        LIMIT $limit OFFSET $offset 
                        ");
                        $no = 1;
                        $modals = '';
                        while ($row = mysqli_fetch_assoc($query)):
                            $badge = $row['status_proses'] === 'Completed' ? 'success' : ($row['status_proses'] === 'Processing' ? 'primary' : 'danger');
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['nama_layanan'] ?></td>
                                <td><?= $row['jumlah_berat'] ?> KG</td>
                                <td><?= $row['metode_pembayaran'] ?></td>
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
                                <div class="modal-dialog">
                                    <form method="POST" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Transaksi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_transaksi" value="' . $row['id_transaksi'] . '">
                                            <div class="mb-2">
                                                <label>Status Proses</label>
                                                <select name="edit_status_proses" class="form-control">
                                                    <option value="Processing" ' . ($row['status_proses'] == 'Processing' ? 'selected' : '') . '>Processing</option>
                                                    <option value="Completed" ' . ($row['status_proses'] == 'Completed' ? 'selected' : '') . '>Completed</option>
                                                    <option value="Rejected" ' . ($row['status_proses'] == 'Rejected' ? 'selected' : '') . '>Rejected</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label>Status Pembayaran</label>
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
        $range = 2; // tampil 2 halaman sebelum dan sesudah
        $start = max(1, $page - $range);
        $end = min($total_pages, $page + $range);

        // tombol pertama
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo "<li class='page-item $active'><a class='page-link' href='?page=$i'>$i</a></li>";
        }

        // tombol terakhir
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
                    <button type="submit" name="submit_all" class="btn btn-success" id="btnSimpan" style="display:none">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script -->
    <script>
    function nextStep() {
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
        document.getElementById('btnSimpan').style.display = 'inline-block';
    }

    document.getElementById("searchInput").addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll("#transaksiTable tbody tr");
        rows.forEach(row => {
            const nama = row.cells[1].textContent.toLowerCase();
            const layanan = row.cells[2].textContent.toLowerCase(); 
            const metode = row.cells[4].textContent.toLowerCase();
            const status = row.cells[6].textContent.toLowerCase();

            if (nama.includes(keyword) || layanan.includes(keyword) || metode.includes(keyword) || status.includes(keyword)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
