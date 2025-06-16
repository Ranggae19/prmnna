<?php
// KONEKSI DATABASE
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());
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
                <a href="#" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="customer.php">
                    <i class="fas fa-users"></i> Customer
                </a>
            </li>
            <li>
                <a href="identitas.php">
                    <i class="fas fa-id-card"></i> Identitas
                </a>
            </li>
            <li>
                <a href="item.php">
                    <i class="fas fa-box-open"></i> Item
                </a>
            </li>
            <li>
                <a href="level.php">
                    <i class="fas fa-layer-group"></i> Level Manager
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
                <a href="transaction.php">
                    <i class="fas fa-exchange-alt"></i> Transaction
                </a>
            </li>
            <li>
                <a href="report.php">
                    <i class="fas fa-chart-pie"></i> Reports
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <i class="fas fa-cog"></i> Settings
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
            <h4 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h4>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card dashboard-card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-users card-icon"></i>
                            <h5>Total Customer</h5>
                            <h2>128</h2>
                            <a href="customer.php" class="text-white small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card dashboard-card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-box-open card-icon"></i>
                            <h5>Total Items</h5>
                            <h2>56</h2>
                            <a href="item.php" class="text-white small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card dashboard-card bg-warning text-dark">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt card-icon"></i>
                            <h5>Transactions</h5>
                            <h2>42</h2>
                            <a href="transaction.php" class="text-dark small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card dashboard-card bg-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line card-icon"></i>
                            <h5>Total Sales</h5>
                            <h2>Rp12.5jt</h2>
                            <a href="sales.php" class="text-white small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Transactions</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Item</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>John Doe</td>
                                            <td>Beras 5kg</td>
                                            <td>2023-06-15</td>
                                            <td>Rp120,000</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Jane Smith</td>
                                            <td>Minyak Goreng</td>
                                            <td>2023-06-14</td>
                                            <td>Rp85,000</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Robert Johnson</td>
                                            <td>Gula 1kg</td>
                                            <td>2023-06-13</td>
                                            <td>Rp45,000</td>
                                            <td><span class="badge bg-danger">Cancelled</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Sales Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        
        // Sales chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Food', 'Beverages', 'Electronics', 'Others'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9',
                        '#17a673',
                        '#2c9faf',
                        '#dda20a'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>