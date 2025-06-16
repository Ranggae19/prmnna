<?php
// KONEKSI DATABASE
$koneksi = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// PROSES PENAMBAHAN DATA TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // AMBIL DATA DARI FORM
    $no_transaksi = mysqli_real_escape_string($koneksi, $_POST['no_transaksi']);
    $id_customer = mysqli_real_escape_string($koneksi, $_POST['id_customer']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $total = mysqli_real_escape_string($koneksi, $_POST['total']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    // QUERY TAMBAH TRANSAKSI
    $query = "INSERT INTO transaksi (no_transaksi, id_customer, tanggal, total, status) 
              VALUES ('$no_transaksi', '$id_customer', '$tanggal', '$total', '$status')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: transaksi.php");
        exit();
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Koperasi Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            padding: 0;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-left: 4px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #007bff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 8px 8px 0 0 !important;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-selesai {
            color: #28a745;
            font-weight: bold;
        }
        .status-batal {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="customer.php">
                                <i class="bi bi-people-fill"></i>
                                Customer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="transaksi.php">
                                <i class="bi bi-cart-check-fill"></i>
                                Transaksi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-box-seam-fill"></i>
                                Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-earmark-text-fill"></i>
                                Laporan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tambah Transaksi Baru</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="transaksi.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Transaksi*</label>
                                    <input type="text" class="form-control" name="no_transaksi" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal*</label>
                                    <input type="date" class="form-control" name="tanggal" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer*</label>
                                    <select class="form-select" name="id_customer" required>
                                        <option value="">Pilih Customer</option>
                                        <?php
                                        $query_customer = "SELECT id_customer, nama_customer FROM customer";
                                        $result_customer = mysqli_query($koneksi, $query_customer);
                                        while ($row = mysqli_fetch_assoc($result_customer)) {
                                            echo "<option value='".$row['id_customer']."'>".$row['nama_customer']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="total" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status*</label>
                                <select class="form-select" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aktifkan sidebar
        document.querySelectorAll('.nav-link').forEach(link => {
            if(link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>

<?php
// TUTUP KONEKSI DATABASE
mysqli_close($koneksi);
?>