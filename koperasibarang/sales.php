<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// CRUD Operations for Sales
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $tgl_sales = mysqli_real_escape_string($conn, $_POST['tgl_sales']);
        $id_customer = (int)$_POST['id_customer'];
        $do_number = mysqli_real_escape_string($conn, $_POST['do_number']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $query = "INSERT INTO sales (tgl_sales, id_customer, do_number, status) 
                 VALUES ('$tgl_sales', $id_customer, '$do_number', '$status')";
        if (mysqli_query($conn, $query)) {
            $pesan = "Data sales berhasil ditambahkan";
            header("Location: sales.php?pesan=".urlencode($pesan));
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    } 
    elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $tgl_sales = mysqli_real_escape_string($conn, $_POST['tgl_sales']);
        $id_customer = (int)$_POST['id_customer'];
        $do_number = mysqli_real_escape_string($conn, $_POST['do_number']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $query = "UPDATE sales SET 
                 tgl_sales='$tgl_sales', 
                 id_customer=$id_customer, 
                 do_number='$do_number', 
                 status='$status'
                 WHERE id_sales=$id";
        if (mysqli_query($conn, $query)) {
            $pesan = "Data sales berhasil diupdate";
            header("Location: sales.php?pesan=".urlencode($pesan));
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $query = "DELETE FROM sales WHERE id_sales=$id";
    if (mysqli_query($conn, $query)) {
        $pesan = "Data sales berhasil dihapus";
        header("Location: sales.php?pesan=".urlencode($pesan));
        exit();
    } else {
        $error = "Gagal menghapus: " . mysqli_error($conn);
    }
}

// Get sales data with customer names
$sales = mysqli_query($conn, "SELECT s.*, c.nama_customer 
                             FROM sales s
                             JOIN customer c ON s.id_customer = c.id_customer
                             ORDER BY s.tgl_sales DESC");

// Get customers for dropdown
$customers = mysqli_query($conn, "SELECT id_customer, nama_customer FROM customer");

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sales WHERE id_sales=$id"));
}
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
        
        .status-pending { background-color: #ffc107; color: #000; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
        
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
                <a href="customer.php">
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
                <a href="sales.php" class="active">
                    <i class="fas fa-chart-line"></i> Sales
                </a>
            </li>
            <li>
                <a href="transaction.php">
                    <i class="fas fa-exchange-alt"></i> Transaction
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
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
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
            <h4 class="mb-4"><i class="fas fa-chart-line me-2"></i>Manajemen Sales</h4>
            
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
                    <h5 class="mb-0"><?= $edit_data ? 'Edit' : 'Tambah' ?> Sales</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_data['id_sales'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Sales*</label>
                                <input type="date" class="form-control" name="tgl_sales" 
                                       value="<?= $edit_data ? htmlspecialchars($edit_data['tgl_sales']) : date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer*</label>
                                <select class="form-select" name="id_customer" required>
                                    <option value="">Pilih Customer</option>
                                    <?php 
                                    $customers = mysqli_query($conn, "SELECT id_customer, nama_customer FROM customer");
                                    while($customer = mysqli_fetch_assoc($customers)): 
                                    ?>
                                        <option value="<?= $customer['id_customer'] ?>" 
                                            <?= ($edit_data && $edit_data['id_customer'] == $customer['id_customer']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($customer['nama_customer']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor DO*</label>
                                <input type="text" class="form-control" name="do_number" 
                                       value="<?= $edit_data ? htmlspecialchars($edit_data['do_number']) : '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status*</label>
                                <select class="form-select" name="status" required>
                                    <option value="pending" <?= ($edit_data && $edit_data['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= ($edit_data && $edit_data['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= ($edit_data && $edit_data['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i> <?= $edit_data ? 'Update' : 'Simpan' ?>
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="sales.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data Sales -->
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Sales</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped data-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tanggal</th>
                                    <th>No. DO</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($sales) > 0): ?>
                                    <?php $no = 1; while($row = mysqli_fetch_assoc($sales)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tgl_sales'])) ?></td>
                                            <td><?= htmlspecialchars($row['do_number']) ?></td>
                                            <td><?= htmlspecialchars($row['nama_customer']) ?></td>
                                            <td>
                                                <span class="badge rounded-pill status-<?= $row['status'] ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td class="action-buttons">
                                                <a href="sales.php?edit=<?= $row['id_sales'] ?>" 
                                                   class="btn btn-sm btn-warning"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="sales.php?hapus=<?= $row['id_sales'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus sales ini?')"
                                                   title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data sales</td>
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