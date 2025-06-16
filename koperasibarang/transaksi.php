<?php
// KONEKSI DATABASE
$koneksi = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// VARIABEL UNTUK MENYIMPAN PESAN
$error = '';
$success = '';

// PROSES PENAMBAHAN DATA TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
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
        $success = "Transaksi berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// PROSES UPDATE DATA TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_POST['id_transaksi']);
    $no_transaksi = mysqli_real_escape_string($koneksi, $_POST['no_transaksi']);
    $id_customer = mysqli_real_escape_string($koneksi, $_POST['id_customer']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $total = mysqli_real_escape_string($koneksi, $_POST['total']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    $query = "UPDATE transaksi SET 
              no_transaksi='$no_transaksi', 
              id_customer='$id_customer', 
              tanggal='$tanggal', 
              total='$total', 
              status='$status' 
              WHERE id_transaksi='$id_transaksi'";

    if (mysqli_query($koneksi, $query)) {
        $success = "Transaksi berhasil diperbarui!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// PROSES HAPUS DATA TRANSAKSI
if (isset($_GET['hapus'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    $query = "DELETE FROM transaksi WHERE id_transaksi='$id_transaksi'";
    
    if (mysqli_query($koneksi, $query)) {
        $success = "Transaksi berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}

// AMBIL DATA TRANSAKSI UNTUK DITAMPILKAN
$query_transaksi = "SELECT t.*, c.nama_customer 
                    FROM transaksi t 
                    JOIN customer c ON t.id_customer = c.id_customer 
                    ORDER BY t.tanggal DESC";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);

// AMBIL DATA UNTUK EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_transaksi = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $query = "SELECT * FROM transaksi WHERE id_transaksi='$id_transaksi'";
    $result = mysqli_query($koneksi, $query);
    $edit_data = mysqli_fetch_assoc($result);
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
        .action-btn {
            margin: 0 3px;
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
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo isset($edit_data) ? 'Edit Transaksi' : 'Tambah Transaksi Baru'; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="transaksi.php" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Form Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <?php if (isset($edit_data)): ?>
                                <input type="hidden" name="id_transaksi" value="<?php echo $edit_data['id_transaksi']; ?>">
                            <?php endif; ?>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Transaksi*</label>
                                    <input type="text" class="form-control" name="no_transaksi" 
                                           value="<?php echo isset($edit_data) ? $edit_data['no_transaksi'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal*</label>
                                    <input type="date" class="form-control" name="tanggal" 
                                           value="<?php echo isset($edit_data) ? $edit_data['tanggal'] : ''; ?>" required>
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
                                            $selected = (isset($edit_data) && $edit_data['id_customer'] == $row['id_customer']) ? 'selected' : '';
                                            echo "<option value='".$row['id_customer']."' $selected>".$row['nama_customer']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Total*</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="total" 
                                               value="<?php echo isset($edit_data) ? $edit_data['total'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status*</label>
                                <select class="form-select" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="pending" <?php echo (isset($edit_data) && $edit_data['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="selesai" <?php echo (isset($edit_data) && $edit_data['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="batal" <?php echo (isset($edit_data) && $edit_data['status'] == 'batal') ? 'selected' : ''; ?>>Batal</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <?php if (isset($edit_data)): ?>
                                    <button type="submit" name="update" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Transaksi
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="tambah" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Transaksi
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar Riwayat Transaksi -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                                        <tr>
                                            <td><?php echo $row['no_transaksi']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo $row['nama_customer']; ?></td>
                                            <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php 
                                                $status_class = '';
                                                if ($row['status'] == 'pending') $status_class = 'status-pending';
                                                elseif ($row['status'] == 'selesai') $status_class = 'status-selesai';
                                                elseif ($row['status'] == 'batal') $status_class = 'status-batal';
                                                ?>
                                                <span class="<?php echo $status_class; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="transaksi.php?edit=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-warning action-btn" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="transaksi.php?hapus=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-danger action-btn" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
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