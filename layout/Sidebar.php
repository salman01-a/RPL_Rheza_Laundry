        <div class="d-flex flex-column p-3 bg-white shadow" style="width: 280px; height: 100vh;">
            <a href="#" class="d-flex align-items-center mb-4 text-decoration-none">
                <span class="fs-4 fw-bold text-dark">RhezaLaundry</span>
            </a>
            <ul class="nav nav-pills flex-column mb-auto">
                <li><a href="dashboard.php" class="nav-link <?= ($page == 'dashboard') ? 'active' : '' ?>"><i class="bi bi-house-door me-2"></i>Dashboard</a></li>
                <li><a href="transaksi.php" class="nav-link <?= ($page == 'transaksi') ? 'active' : '' ?>"><i class="bi bi-cart me-2"></i>Transaksi</a></li>
                <li><a href="pelanggan.php" class="nav-link <?= ($page == 'pelanggan') ? 'active' : '' ?>"><i class="bi bi-people me-2"></i>Pelanggan</a></li>
                <li><a href="layanan.php" class="nav-link <?= ($page == 'layanan') ? 'active' : '' ?>"><i class="bi bi-box me-2"></i>Layanan</a></li>
                <li><a href="operasional.php" class="nav-link <?= ($page == 'operasional') ? 'active' : '' ?>"><i class="bi bi-gear me-2"></i>Operasional</a></li>
                <li><a href="bahanbaku.php" class="nav-link <?= ($page == 'bahan_baku') ? 'active' : '' ?>"><i class="bi bi-droplet me-2"></i>Bahan Baku</a></li>
                <li><a href="laporan.php" class="nav-link <?= ($page == 'laporan') ? 'active' : '' ?>"><i class="bi bi-file-earmark-text me-2"></i>Laporan</a></li>
                <li><a href="staff.php" class="nav-link <?= ($page == 'staff') ? 'active' : '' ?>"><i class="bi bi-person me-2"></i>Staff</a></li>
            </ul>
            <hr>
            <a href="SettingsOwner.php" class="text-decoration-none d-flex align-items-center nav-link <?= ($page == 'settings') ? 'active' : '' ?>">
                <i class="bi bi-gear me-2"></i><span>Settings</span>
            </a>
            <a href="../layout/Logout.php" class="text-decoration-none d-flex align-items-center nav-link mt-2">
                <i class="bi bi-box-arrow-right me-2"></i><span>Logout</span>
            </a>
        </div>
