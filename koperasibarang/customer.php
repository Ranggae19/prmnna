<?php
// KONEKSI DATABASE
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// PROSES CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CREATE atau UPDATE
    $id = $_POST['id'] ?? null;
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $fax = mysqli_real_escape_string($conn, $_POST['fax'] ?? '');

    if (empty($id)) {
        // CREATE
        $query = "INSERT INTO customer (nama_customer, alamat, telp, email, fax) 
                 VALUES ('$nama', '$alamat', '$telp', '$email', '$fax')";
    } else {
        // UPDATE
        $query = "UPDATE customer SET 
                 nama_customer='$nama',
                 alamat='$alamat',
                 telp='$telp',
                 email='$email',
                 fax='$fax'
                 WHERE id_customer=$id";
    }

    if (mysqli_query($conn, $query)) {
        $pesan = empty($id) ? "Data customer berhasil ditambahkan" : "Data customer berhasil diupdate";
        header("Location: customer.php?pesan=".urlencode($pesan));
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM customer WHERE id_customer=$id";
    if (mysqli_query($conn, $query)) {
        $pesan = "Data customer berhasil dihapus";
        header("Location: customer.php?pesan=".urlencode($pesan));
        exit();
    } else {
        $error = "Gagal menghapus: " . mysqli_error($conn);
    }
}

// AMBIL DATA UNTUK EDIT
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM customer WHERE id_customer=$id");
    $editData = mysqli_fetch_assoc($result);
}

// AMBIL SEMUA DATA CUSTOMER
$customer_result = mysqli_query($conn, "SELECT * FROM customer ORDER BY id_customer DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - Koperasi Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-active: #3498db;
            --header-bg: #ffffff;
            --content-bg: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--content-bg);
            overflow-x: hidden;
        }
        
        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 20px;
            background: rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-menu li {
            position: relative;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(0,0,0,0.2);
            color: white;
            border-left: 3px solid var(--sidebar-active);
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        /* Navbar */
        .top-navbar {
            background: var(--header-bg);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        /* Cards */
        .dashboard-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        /* Table Styling */
        .data-table th {
            background-color: #28a745;
            color: white;
        }
        
        .action-buttons .btn {
            margin-right: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -var(--sidebar-width);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4>KOPERASI BARANG</h4>
            <p class="text-muted small mb-0">Admin Panel</p>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="customer.php" class="active">
                    <i class="fas fa-users"></i> Customer
                </a>
            </li>
            <li>
                <a href="item.php">
                    <i class="fas fa-box-open"></i> Item
                </a>
            </li>
            <li>
                <a href="petugas.php">
                    <i class="fas fa-user-tie"></i> Petugas
                </a>
            </li>
            <li>
                <a href="sales.php">
                    <i class="fas fa-chart-line"></i> Sales
                </a>
            </li>
            <li>
                <a href="transaksi.php">
                    <i class="fas fa-exchange-alt"></i> Transaksi
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand top-navbar">
            <div class="container-fluid">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger rounded-pill">3</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Dashboard Content -->
        <div class="container-fluid">
            <h4 class="mb-4"><i class="fas fa-users me-2"></i>Manajemen Customer</h4>
            
            <!-- Notifikasi -->
            <?php if (isset($_GET['pesan'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']) ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Form Input/Edit -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><?= $editData ? 'Edit' : 'Tambah' ?> Customer</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $editData['id_customer'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Customer*</label>
                                <input type="text" class="form-control" name="nama" 
                                       value="<?= htmlspecialchars($editData['nama_customer'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telepon*</label>
                                <input type="tel" class="form-control" name="telp" 
                                       value="<?= htmlspecialchars($editData['telp'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat*</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= htmlspecialchars($editData['alamat'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fax</label>
                                <input type="text" class="form-control" name="fax" 
                                       value="<?= htmlspecialchars($editData['fax'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i> <?= $editData ? 'Update' : 'Simpan' ?>
                            </button>
                            <?php if ($editData): ?>
                                <a href="customer.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data Customer -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Customer</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped data-table">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($customer_result) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($customer_result)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id_customer']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_customer']) ?></td>
                                            <td><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['telp'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                            <td class="action-buttons">
                                                <a href="customer.php?edit=<?= $row['id_customer'] ?>" 
                                                   class="btn btn-sm btn-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="customer.php?delete=<?= $row['id_customer'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus customer ini?')"
                                                   title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data customer</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>