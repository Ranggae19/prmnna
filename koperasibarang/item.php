<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// CRUD Operations for Item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $nama_item = mysqli_real_escape_string($conn, $_POST['nama_item']);
        $uom = mysqli_real_escape_string($conn, $_POST['uom']);
        $harga_beli = (float)$_POST['harga_beli'];
        $harga_jual = (float)$_POST['harga_jual'];

        if ($harga_beli < 0 || $harga_jual < 0) {
            $_SESSION['error'] = "Harga tidak boleh negatif.";
            header("Location: item.php");
            exit();
        }

        $query = "INSERT INTO item (nama_item, uom, harga_beli, harga_jual) 
                  VALUES ('$nama_item', '$uom', $harga_beli, $harga_jual)";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Item berhasil ditambahkan";
            header("Location: item.php");
            exit();
        }
    } elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nama_item = mysqli_real_escape_string($conn, $_POST['nama_item']);
        $uom = mysqli_real_escape_string($conn, $_POST['uom']);
        $harga_beli = (float)$_POST['harga_beli'];
        $harga_jual = (float)$_POST['harga_jual'];

        if ($harga_beli < 0 || $harga_jual < 0) {
            $_SESSION['error'] = "Harga tidak boleh negatif.";
            header("Location: item.php");
            exit();
        }

        $query = "UPDATE item SET 
                  nama_item='$nama_item', 
                  uom='$uom', 
                  harga_beli=$harga_beli, 
                  harga_jual=$harga_jual
                  WHERE id_item=$id";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Item berhasil diperbarui";
            header("Location: item.php");
            exit();
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $check = mysqli_query($conn, "SELECT * FROM item WHERE id_item=$id");
    if ($check && mysqli_num_rows($check) > 0) {
        $query = "DELETE FROM item WHERE id_item=$id";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Item berhasil dihapus";
        }
    } else {
        $_SESSION['error'] = "Data item tidak ditemukan untuk dihapus.";
    }
    header("Location: item.php");
    exit();
}

$items = mysqli_query($conn, "SELECT * FROM item ORDER BY nama_item");
if (!$items) die("Error query: " . mysqli_error($conn));

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM item WHERE id_item=$id");
    if ($edit_query && mysqli_num_rows($edit_query) > 0) {
        $edit_data = mysqli_fetch_assoc($edit_query);
    } else {
        $_SESSION['error'] = "Item tidak ditemukan untuk diedit.";
        header("Location: item.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }
        .sidebar-header h3 {
            color: white;
            margin: 0;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .sidebar-header p {
            color: #b8c7ce;
            margin: 5px 0 0;
            font-size: 0.9rem;
        }
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        .sidebar-menu li {
            position: relative;
            margin: 0;
        }
        .sidebar-menu li a {
            display: block;
            padding: 12px 20px;
            color: #b8c7ce;
            text-decoration: none;
            border-left: 3px solid transparent;
        }
        .sidebar-menu li a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-menu li.active a {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #3c8dbc;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .table {
            margin-top: 20px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        .price {
            font-weight: bold;
        }
        .profit {
            font-weight: bold;
            color: #28a745;
        }
        .badge-profit {
            background-color: #d4edda;
            color: #28a745;
            font-weight: 500;
        }
        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .divider {
            border-top: 1px solid #eee;
            margin: 25px 0;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>KOPERASI</h3>
            <p>BARANG</p>
            <p>Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="customer.php">
                    <i class="fas fa-users"></i> Customer
                </a>
            </li>
            <li class="active">
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
                <a href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4">Manajemen Item</h1>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Tambah/Edit Item -->
        <div class="form-container">
            <h2 class="section-title"><?= isset($edit_data) ? 'Edit Item' : 'Tambah Item Baru' ?></h2>
            <form method="POST" class="mt-3">
                <input type="hidden" name="id" value="<?= isset($edit_data) ? $edit_data['id_item'] : '' ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_item" class="form-label">Nama Item</label>
                        <input type="text" class="form-control" id="nama_item" name="nama_item" 
                               value="<?= isset($edit_data) ? htmlspecialchars($edit_data['nama_item']) : '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="uom" class="form-label">Satuan (UOM)</label>
                        <select class="form-select" id="uom" name="uom" required>
                            <option value="">Pilih Satuan</option>
                            <option value="pcs" <?= (isset($edit_data) && $edit_data['uom'] == 'pcs' ? 'selected' : '') ?>>Pieces (pcs)</option>
                            <option value="unit" <?= (isset($edit_data) && $edit_data['uom'] == 'unit' ? 'selected' : '') ?>>Unit</option>
                            <option value="set" <?= (isset($edit_data) && $edit_data['uom'] == 'set' ? 'selected' : '') ?>>Set</option>
                            <option value="pak" <?= (isset($edit_data) && $edit_data['uom'] == 'pak' ? 'selected' : '') ?>>Pak</option>
                            <option value="lusin" <?= (isset($edit_data) && $edit_data['uom'] == 'lusin' ? 'selected' : '') ?>>Lusin</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                        <input type="number" class="form-control" id="harga_beli" name="harga_beli"
                               value="<?= isset($edit_data) ? $edit_data['harga_beli'] : '' ?>"
                               min="0" step="100" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual"
                               value="<?= isset($edit_data) ? $edit_data['harga_jual'] : '' ?>"
                               min="0" step="100" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <?php if(isset($edit_data)): ?>
                        <button type="submit" name="edit" class="btn btn-warning me-2">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                        <a href="item.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    <?php else: ?>
                        <button type="submit" name="tambah" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-1"></i> Simpan
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="divider"></div>

        <!-- Daftar Item -->
        <div class="table-container">
            <h2 class="section-title">Daftar Item</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Item</th>
                            <th width="10%">Satuan</th>
                            <th width="15%">Harga Beli</th>
                            <th width="15%">Harga Jual</th>
                            <th width="15%">Profit</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($items) > 0): ?>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($items)): 
                                $profit = $row['harga_jual'] - $row['harga_beli'];
                                $profit_percent = $row['harga_beli'] > 0 ? round(($profit / $row['harga_beli']) * 100, 2) : 0;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_item']) ?></td>
                                    <td class="text-center"><?= strtoupper(htmlspecialchars($row['uom'])) ?></td>
                                    <td class="price text-end">Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="price text-end">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                                    <td class="profit text-end">
                                        Rp <?= number_format($profit, 0, ',', '.') ?>
                                        <span class="badge badge-profit ms-2"><?= $profit_percent ?>%</span>
                                    </td>
                                    <td class="action-buttons text-center">
                                        <a href="item.php?edit=<?= $row['id_item'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="item.php?hapus=<?= $row['id_item'] ?>" class="btn btn-sm btn-danger" 
                                           title="Hapus" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada data item</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            new bootstrap.Alert(alert).close();
        });
    }, 5000);
</script>
</body>
</html>

<?php mysqli_close($conn); ?>