<?php
// KONEKSI DATABASE
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// CEK SESSION LOGIN
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// QUERY DATA TRANSAKSI - DIPERBAIKI
$transactions = mysqli_query($conn, "SELECT t.*, c.nama_customer as customer_name 
                                  FROM transaksi t 
                                  JOIN customer c ON t.id_customer = c.id_customer 
                                  ORDER BY t.tanggal DESC");
if (!$transactions) {
    die("Error dalam query: " . mysqli_error($conn));
}
$total_transactions = mysqli_num_rows($transactions);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Koperasi Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-color: #ecf0f1;
            --sidebar-active: #3498db;
            --header-bg: #ffffff;
            --content-bg: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--content-bg);
            margin: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-color);
            padding-top: 20px;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            text-align: center;
            padding: 10px 15px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: var(--sidebar-color);
            text-decoration: none;
            transition: background 0.2s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: var(--sidebar-active);
            color: #fff;
        }

        .sidebar-menu li a i {
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        .top-navbar {
            background-color: var(--header-bg);
            padding: 10px 15px;
            border-bottom: 1px solid #ccc;
        }

        .table-borderless td, .table-borderless th {
            border: none;
        }

        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-completed { background-color: #28a745; color: #fff; }
        .badge-cancelled { background-color: #dc3545; color: #fff; }

        @media (max-width: 768px) {
            .sidebar {
                left: -100%;
            }

            .sidebar.active {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>KOPERASI BARANG</h4>
            <p class="text-muted small mb-0">Admin Panel</p>
        </div>

        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="customer.php"><i class="fas fa-users"></i> Customer</a></li>
            <li><a href="item.php"><i class="fas fa-box-open"></i> Item</a></li>
            <li><a href="transaction.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand top-navbar">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                        </span>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-exchange-alt me-2"></i>Data Transaksi</h2>
                <a href="create_transaction.php" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i>Transaksi Baru
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead>
                                <tr class="border-bottom">
                                    <th>No</th>
                                    <th>No. Transaksi</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($total_transactions > 0) {
                                    $counter = 1;
                                    while($row = mysqli_fetch_assoc($transactions)):
                                ?>
                                    <tr class="border-bottom">
                                        <td><?= $counter++ ?></td>
                                        <td><?= htmlspecialchars($row['no_transaksi'] ?? 'TRX-'.str_pad($row['id_transaksi'], 4, '0', STR_PAD_LEFT)) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                        <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                ($row['status'] == 'batal') ? 'cancelled' : 
                                                (($row['status'] == 'pending') ? 'pending' : 'completed') 
                                            ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="view_transaction.php?id=<?= $row['id_transaksi'] ?>" class="btn btn-sm btn-light text-primary" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit_transaction.php?id=<?= $row['id_transaksi'] ?>" class="btn btn-sm btn-light text-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_transaction.php?id=<?= $row['id_transaksi'] ?>" class="btn btn-sm btn-light text-danger" title="Hapus" onclick="return confirm('Hapus transaksi ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada data transaksi</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <div class="text-muted small">Total <?= $total_transactions ?> data transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>

<?php 
mysqli_close($conn); 
?>