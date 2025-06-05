<?php
$page = 'layanan';
include '../database/connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../");
    exit();
}

$query = "SELECT * FROM Layanan ORDER BY id_layanan ASC";
$result = mysqli_query($conn, $query);

if (isset($_POST['submit_all'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $id = $_POST['id_layanan'];

    if (!empty($id)) {
        // Edit
        $query = "UPDATE layanan SET nama_layanan = '$nama' WHERE id_layanan = $id";
        mysqli_query($conn, $query);
        header("Location: layanan.php?success=edit");  // untuk tambah
        exit();
    } else {
        // Tambah
        $query = "INSERT INTO layanan (nama_layanan) VALUES ('$nama')";
        mysqli_query($conn, $query);
        header("Location: layanan.php?success=add"); // untuk tambah
        exit();
    }
}

if (isset($_GET['id_hapus'])) {
    $id_hapus = intval($_GET['id_hapus']);
    mysqli_query($conn, "DELETE FROM layanan WHERE id_layanan = $id_hapus");
    header("Location: layanan.php?success=delete");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Layanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/DashboardOwner.css">
    <style>
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #F8F9FC;
        }

        /* Optional: hover effect halus */
        a.text-secondary:hover {
            color: #4880FF;
        }

        a.text-danger:hover {
            color: #ff0000;
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

        .btn-add {
            font-size: 0.9rem;
            padding: 6px 14px;
            border-radius: 10px;
            border: 1px solid #4880FF;
            color: #4880FF;
            background: white;
            transition: 0.2s;
        }

        .btn-add:hover {
            background-color: #4880FF;
            color: white;
        }

        .btn-icon {
            background: none;
            border: none;
            font-size: 1.1rem;
            color: #4a4a4a;
            transition: 0.2s;
        }

        .btn-icon:hover {
            color: #4880FF;
        }

        .btn-icon.delete:hover {
            color: #dc3545;
        }

        .section-title {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .dropdown-month {
            width: auto;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include '../layout/sidebar.php'; ?>

        <div class="p-4 flex-grow-1 w-100">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-title">Layanan</h3>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalLayanan">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Layanan
                </button>

            </div>

            <!-- Tabel Data -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php
                            if ($_GET['success'] == 'add') echo "Data Layanan berhasil ditambahkan.";
                            elseif ($_GET['success'] == 'edit') echo "Data Layanan berhasil diperbarui.";
                            elseif ($_GET['success'] == 'delete') echo "Data Layanan berhasil dihapus.";
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <h5 class="fw-bold mb-3">Daftar Layanan</h5>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nama_layanan']; ?></td>
                                    <td class="text-center">
                                        <div class="d-inline-flex border rounded-3 overflow-hidden bg-light">
                                            <a class="d-flex align-items-center justify-content-center px-3 py-2 text-secondary" onclick="editLayanan(<?= $row['id_layanan']; ?>, '<?= $row['nama_layanan']; ?>')" data-bs-toggle="modal" data-bs-target="#modalLayanan">
                                                <i class="bi bi-pencil-square fs-10"></i>
                                            </a>

                                            <div class="border-start"></div>
                                            <a href="layanan.php?id_hapus=<?= $row['id_layanan']; ?>"
                                                class="d-flex align-items-center justify-content-center px-3 py-2 text-danger"
                                                title="Hapus"
                                                onclick="return confirm('Hapus layanan ini?')">
                                                <i class="bi bi-trash3 fs-10"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($result) === 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada data layanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Tambah/Edit Layanan -->
    <div class="modal fade" id="modalLayanan" tabindex="-1" aria-labelledby="modalLayananLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="layanan.php">
                <div class="modal-content border border-primary-subtle rounded shadow-sm">
                    <div class="modal-body">
                        <label class="form-label">Nama Layanan</label>
                        <input type="hidden" name="id_layanan" id="idLayanan">
                        <input type="text" class="form-control bg-light" placeholder="Nama Layanan" name="nama_layanan" id="namaLayanan" required>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="submit" name="submit_all" class="btn btn-primary">Done</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        function editLayanan(id, nama) {
            document.getElementById('idLayanan').value = id;
            document.getElementById('namaLayanan').value = nama;
        }
    </script>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>