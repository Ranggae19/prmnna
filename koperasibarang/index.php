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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - Koperasi Barang</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
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
                <a href="index.php" class="active">
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
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['username'] ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Dashboard Content -->
        <div class="container-fluid">
            <h4 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card dashboard-card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users card-icon"></i>
                            <h5>Total Customer</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM customer";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?= $row['total'] ?></h2>
                            <a href="customer.php" class="text-white small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card dashboard-card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-box-open card-icon"></i>
                            <h5>Total Items</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM item";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?= $row['total'] ?></h2>
                            <a href="item.php" class="text-white small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card dashboard-card bg-warning text-dark">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt card-icon"></i>
                            <h5>Total Transaksi</h5>
                            <?php
                            $query = "SELECT COUNT(*) as total FROM transaksi";
                            $result = mysqli_query($conn, $query);
                            $row = mysqli_fetch_assoc($result);
                            ?>
                            <h2><?= $row['total'] ?></h2>
                            <a href="transaksi.php" class="text-dark small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
       
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>