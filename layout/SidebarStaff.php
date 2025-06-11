<div class="bg-white shadow p-3" style="width: 280px; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000;">
    <a href="#" class="d-flex align-items-center mb-4 text-decoration-none">
        <span class="fs-4 fw-bold text-dark"><span class="rheza">Rheza</span>Laundry</span>
    </a>
    <ul class="nav nav-pills flex-column mb-auto">
    <li><a href="dashboard.php" class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>"><i class="bi bi-house-door me-2"></i>Dashboard</a></li>
        <li><a href="transaksi.php" class="nav-link <?= ($page == 'transaksi') ? 'active' : '' ?>"><i class="bi bi-cart me-2"></i>Transaksi</a></li>
        <li><a href="pelanggan.php" class="nav-link <?= ($page == 'pelanggan') ? 'active' : '' ?>"><i class="bi bi-people me-2"></i>Pelanggan</a></li>
        <li><a href="operasional.php" class="nav-link <?= ($page == 'operasional') ? 'active' : '' ?>"><i class="bi bi-gear me-2"></i>Operasional</a></li>
        <li><a href="bahan_baku.php" class="nav-link <?= ($page == 'bahan_baku') ? 'active' : '' ?>"><i class="bi bi-droplet me-2"></i>Bahan Baku</a></li>
    </ul>
    <hr>
    <a href="setting.php" class="text-decoration-none d-flex align-items-center nav-link <?= ($page == 'setting') ? 'active' : '' ?>">
        <i class="bi bi-gear me-2"></i><span>Settings</span>
    </a>
    <a href="../layout/Logout.php" class="text-decoration-none d-flex align-items-center nav-link mt-2">
        <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
    </a>
</div>
