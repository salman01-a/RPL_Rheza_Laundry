<?php
$page = 'staff';
include '../database/connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'owner') {
    header("Location: ../");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ambil data staff
$query = "SELECT * FROM users WHERE role = 'staff' ORDER BY id_user ASC";
$result = mysqli_query($conn, $query);

// Tambah/Edit Staff
if (isset($_POST['submit_all'])) {
    $id = $_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    if (!empty($id)) {
        // Edit
        $query = "UPDATE users SET nama = '$nama', no_hp = '$no_hp' WHERE id_user = $id";
        mysqli_query($conn, $query);
        header("Location: staff.php?success=edit");
        exit();
    } else {
        // Tambah staff default username/password
        function generateRandomPassword($length = 8) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
        }
        
        $plain_password = generateRandomPassword(8);
        
        
        $username = strtolower(str_replace(' ', '', $nama));
        $query = "INSERT INTO users (nama, no_hp, username, password, role) VALUES ('$nama', '$no_hp', '$username', '$plain_password', 'staff')";
        mysqli_query($conn, $query);    
        header("Location: staff.php?success=add");
        exit();
    }
}

// Hapus Staff
if (isset($_GET['id_hapus'])) {
    $id = intval($_GET['id_hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id_user = $id");
    header("Location: staff.php?success=delete");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff</title>
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
    </style>
</head>
<body>
<div class="d-flex">
    <?php include '../layout/sidebar.php'; ?>

    <div class="p-4 flex-grow-1 w-100"style="margin-left: 280px; padding: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-title">Staff</h3>
            <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#modalStaff">
                <i class="bi bi-plus-circle me-1"></i>Tambah Staff
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        if ($_GET['success'] == 'add') echo "Staff berhasil ditambahkan.";
                        elseif ($_GET['success'] == 'edit') echo "Staff berhasil diperbarui.";
                        elseif ($_GET['success'] == 'delete') echo "Staff berhasil dihapus.";
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <h5 class="fw-bold mb-3">Daftar Staff</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No Telepon</th>
                            <th>Password</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                            <td>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-sm bg-white border-0" value="<?= htmlspecialchars($row['password']) ?>" id="pw<?= $row['id_user'] ?>" readonly>
                                    <span class="input-group-text bg-white border-0" style="cursor: pointer;" onclick="toggleTablePassword('pw<?= $row['id_user'] ?>', this)">
                                        <i class="bi bi-eye"></i>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex border rounded-3 overflow-hidden bg-light">
                                    <a class="d-flex align-items-center justify-content-center px-3 py-2 text-secondary"
                                       onclick="editStaff(<?= $row['id_user']; ?>, '<?= $row['nama']; ?>', '<?= $row['no_hp']; ?>')"
                                       data-bs-toggle="modal" data-bs-target="#modalStaff">
                                        <i class="bi bi-pencil-square fs-10"></i>
                                    </a>
                                    <div class="border-start"></div>
                                    <a href="staff.php?id_hapus=<?= $row['id_user']; ?>"
                                       class="d-flex align-items-center justify-content-center px-3 py-2 text-danger"
                                       onclick="return confirm('Hapus staff ini?')">
                                        <i class="bi bi-trash3 fs-10"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada data staff.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Staff -->
<div class="modal fade" id="modalStaff" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="staff.php">
            <div class="modal-content border border-primary-subtle rounded shadow-sm">
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="idStaff">

                    <label class="form-label">Nama Staff</label>
                    <input type="text" class="form-control bg-light mb-3" name="nama" id="namaStaff" required>

                    <label class="form-label">No Telepon</label>
                    <input type="text" class="form-control bg-light mb-3" name="no_hp" id="nohpStaff" required>

                    <!-- <label class="form-label">Password</label>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control bg-light" name="password" id="passwordStaff">
                        <span class="input-group-text" onclick="togglePasswordVisibility()" style="cursor:pointer;">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </span>
                    </div> -->
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="submit" name="submit_all" class="btn btn-primary">Done</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script>
    function editStaff(id, nama, nohp) {
        document.getElementById('idStaff').value = id;
        document.getElementById('namaStaff').value = nama;
        document.getElementById('nohpStaff').value = nohp;
        document.getElementById('passwordStaff').value = ""; // kosongkan saat edit
    }

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("passwordStaff");
        const eyeIcon = document.getElementById("eyeIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("bi-eye");
            eyeIcon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("bi-eye-slash");
            eyeIcon.classList.add("bi-eye");
        }
    }

    function toggleTablePassword(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
