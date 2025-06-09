<?php
$page = 'operasional';
include '../database/connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../");
  exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// HANDLE TAMBAH DATA
if (isset($_POST['tambah'])) {
  $jenis = $_POST['jenis_pengeluaran'];
  $nominal = $_POST['nominal_pengeluaran'];
  $tanggal = $_POST['tanggal_pengeluaran'];
  $tanggal_update = date("Y-m-d");
  $id_user = $_SESSION['user_id'];

  $stmt = $conn->prepare("INSERT INTO Operasional (id_user, jenis_pengeluaran, tanggal_pengeluaran, nominal_pengeluaran, tanggal_update) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issds", $id_user, $jenis, $tanggal, $nominal, $tanggal_update);
  $stmt->execute();
  $stmt->close();

  header("Location: operasional.php?success=add"); // untuk tambah
  exit();
}

// HANDLE HAPUS
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $conn->query("DELETE FROM Operasional WHERE id_operasional = $id");
  header("Location: operasional.php?success=delete"); // untuk hapus
  exit();
}

// HANDLE EDIT
if (isset($_POST['edit_id'])) {
  $id = $_POST['edit_id'];
  $jenis = $_POST['edit_jenis_pengeluaran'];
  $nominal = $_POST['edit_nominal_pengeluaran'];
  $tanggal = $_POST['edit_tanggal_pengeluaran'];
  $tanggal_update = date("Y-m-d");

  $stmt = $conn->prepare("UPDATE Operasional SET jenis_pengeluaran=?, nominal_pengeluaran=?, tanggal_pengeluaran=?, tanggal_update=? WHERE id_operasional=?");
  $stmt->bind_param("sdssi", $jenis, $nominal, $tanggal, $tanggal_update, $id);
  $stmt->execute();
  $stmt->close();

  header("Location: operasional.php?success=edit"); // untuk edit
  exit();
}

// AMBIL DATA
$data = $conn->query("SELECT * FROM Operasional ORDER BY tanggal_pengeluaran DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Operasional</title>
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
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .rheza {
                color: #4880FF;
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
          if ($_GET['success'] == 'add') echo "Data operasional berhasil ditambahkan.";
          elseif ($_GET['success'] == 'edit') echo "Data operasional berhasil diperbarui.";
          elseif ($_GET['success'] == 'delete') echo "Data operasional berhasil dihapus.";
          ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Operasional</h3>
        <div class="fw-semibold"><?= $_SESSION['role'] ?></div>
      </div>

      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-semibold mb-0">Data Operasional</h5>
          <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Catat Biaya Operasional</button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Jenis Pengeluaran</th>
                <th>Nominal</th>
                <th>Tanggal</th>
                <th>Update</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              if ($data->num_rows > 0) {
                while ($row = $data->fetch_assoc()) {
              ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['jenis_pengeluaran']) ?></td>
                    <td>Rp<?= number_format($row['nominal_pengeluaran'], 0, ',', '.') ?></td>
                    <td><?= $row['tanggal_pengeluaran'] ?></td>
                    <td><?= $row['tanggal_update'] ?></td>
                    <td>
                      <div class="d-inline-flex border rounded-3 overflow-hidden bg-light">
                        <a class="d-flex align-items-center justify-content-center px-3 py-2 text-secondary"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEdit"
                          data-id="<?= $row['id_operasional'] ?>"
                          data-jenis="<?= htmlspecialchars($row['jenis_pengeluaran']) ?>"
                          data-nominal="<?= $row['nominal_pengeluaran'] ?>"
                          data-tanggal="<?= $row['tanggal_pengeluaran'] ?>">
                          <i class="bi bi-pencil-square fs-10"></i>
                        </a>
                        <div class="border-start"></div>
                        <a href="?hapus=<?= $row['id_operasional'] ?>"
                          class="d-flex align-items-center justify-content-center px-3 py-2 text-danger"
                          onclick="return confirm('Yakin ingin hapus?')">
                          <i class="bi bi-trash3 fs-10"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">Data operasional tidak ada.</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content p-3">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Catat Pengeluaran</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Jenis Pengeluaran</label>
              <input type="text" name="jenis_pengeluaran" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Nominal Pengeluaran</label>
              <input type="number" name="nominal_pengeluaran" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Tanggal Pengeluaran</label>
              <input type="date" name="tanggal_pengeluaran" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content p-3">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Edit Pengeluaran</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="edit_id" id="edit_id">
            <div class="mb-3">
              <label>Jenis Pengeluaran</label>
              <input type="text" name="edit_jenis_pengeluaran" id="edit_jenis" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Nominal Pengeluaran</label>
              <input type="number" name="edit_nominal_pengeluaran" id="edit_nominal" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Tanggal Pengeluaran</label>
              <input type="date" name="edit_tanggal_pengeluaran" id="edit_tanggal" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalEdit = document.getElementById('modalEdit');
      modalEdit.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('edit_id').value = button.getAttribute('data-id');
        document.getElementById('edit_jenis').value = button.getAttribute('data-jenis');
        document.getElementById('edit_nominal').value = button.getAttribute('data-nominal');
        document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal');
      });
    });
  </script>
</body>

</html>