<?php
$page = 'bahan_baku';
include '../database/connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $jumlah_stok = $_POST['jumlah_stok'];
    $harga = $_POST['harga'];
    $tanggal_update = date('Y-m-d');

    if (isset($_POST['id_edit']) && $_POST['id_edit'] != '') {
        $id_edit = $_POST['id_edit'];
        $query = "UPDATE Bahan_Baku SET nama_barang=?, jumlah_stok=?, harga=?, tanggal_update=? WHERE id_bahan_baku=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sissi", $nama_barang, $jumlah_stok, $harga, $tanggal_update, $id_edit);
        $successType = 'edit';
    } else {
        $query = "INSERT INTO Bahan_Baku (id_user, nama_barang, jumlah_stok, harga, tanggal_update) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isiss", $id_user, $nama_barang, $jumlah_stok, $harga, $tanggal_update);
        $successType = 'add';
    }

    if (!$stmt->execute()) {
        die("Query gagal: " . $stmt->error);
    }

    header("Location: bahan_baku.php?success=$successType");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Bahan_Baku WHERE id_bahan_baku=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: bahan_baku.php?success=delete");
    exit();
}


$data = $conn->query("SELECT * FROM Bahan_Baku ORDER BY id_bahan_baku DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bahan Baku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/DashboardOwner.css">
    <link rel="shortcut icon" href="../assets/logo.png" type="image/x-icon">

    <style>
        body {
            background: #F8F9FC;
            font-family: 'Nunito Sans', sans-serif;
        }

        .rheza {
                color: #4880FF;
            }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .btn-outline-primary {
            border-color: #4880FF;
            color: #4880FF;
        }

        .btn-outline-primary:hover {
            background-color: #4880FF;
            color: white;
        }

        .btn-edit {
            background-color: #e0f2fe;
            color: #0284c7;
        }

        .btn-delete {
            background-color: #fee2e2;
            color: #dc2626;
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

        <div class="p-4 flex-grow-1 w-100">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    if ($_GET['success'] == 'add') echo "Data bahan baku berhasil ditambahkan.";
                    elseif ($_GET['success'] == 'edit') echo "Data bahan baku berhasil diperbarui.";
                    elseif ($_GET['success'] == 'delete') echo "Data bahan baku berhasil dihapus.";
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Bahan Baku</h3>
                <div class="fw-semibold"><?= $_SESSION['role'] ?></div>
            </div>

            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-semibold mb-0">Data Bahan Baku</h5>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalForm">+ Tambah Bahan Baku</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Stok</th>
                                <th>Harga</th>
                                <th>Update Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = $data->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                    <td><?= $row['jumlah_stok'] ?></td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td><?= $row['tanggal_update'] ?></td>
                                    <td>
                                        <div class="d-inline-flex border rounded-3 overflow-hidden bg-light">
                                            <a class="d-flex align-items-center justify-content-center px-3 py-2 text-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalForm"
                                                data-id="<?= $row['id_bahan_baku'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_barang']) ?>"
                                                data-jumlah="<?= $row['jumlah_stok'] ?>"
                                                data-harga="<?= $row['harga'] ?>">
                                                <i class="bi bi-pencil-square fs-10"></i>
                                            </a>

                                            <div class="border-start"></div>

                                            <a href="?delete=<?= $row['id_bahan_baku'] ?>"
                                                class="d-flex align-items-center justify-content-center px-3 py-2 text-danger"
                                                onclick="return confirm('Yakin ingin hapus?')">
                                                <i class="bi bi-trash3 fs-10"></i>
                                            </a>
                                        </div>

                                    </td>
                                </tr>
                            <?php endwhile;
                            if ($data->num_rows == 0): ?>
                                <tr>
                                    <td colspan="6">Belum ada data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalFormLabel">Form Bahan Baku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_edit" id="id_edit">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" id="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" name="jumlah_stok" id="jumlah_stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" id="harga" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalForm = document.getElementById('modalForm');
            modalForm.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const jumlah = button.getAttribute('data-jumlah');
                const harga = button.getAttribute('data-harga');

                document.getElementById('id_edit').value = id || '';
                document.getElementById('nama_barang').value = nama || '';
                document.getElementById('jumlah_stok').value = jumlah || '';
                document.getElementById('harga').value = harga || '';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>