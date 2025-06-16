<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// CRUD Operations for Petugas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tambah'])) {
        $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $level = mysqli_real_escape_string($conn, $_POST['level']);
        
        $query = "INSERT INTO petugas (nama_user, username, password, level) 
                 VALUES ('$nama_user', '$username', '$password', '$level')";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Data petugas berhasil ditambahkan";
        }
    } 
    elseif (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $level = mysqli_real_escape_string($conn, $_POST['level']);
        
        $password_update = "";
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $password_update = ", password='$password'";
        }
        
        $query = "UPDATE petugas SET 
                 nama_user='$nama_user', 
                 username='$username', 
                 level='$level'
                 $password_update
                 WHERE id_user=$id";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Data petugas berhasil diperbarui";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id != ($_SESSION['id_user'] ?? 0)) {
        $query = "DELETE FROM petugas WHERE id_user=$id";
        if (!mysqli_query($conn, $query)) {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        } else {
            $_SESSION['success'] = "Data petugas berhasil dihapus";
        }
    } else {
        $_SESSION['error'] = "Tidak dapat menghapus akun sendiri";
    }
}

// Query data petugas
$petugas = mysqli_query($conn, "SELECT * FROM petugas ORDER BY level, nama_user");
if (!$petugas) {
    die("Error query: " . mysqli_error($conn));
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM petugas WHERE id_user=$id"));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Petugas</title>
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
        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        .badge-admin { background-color: #dc3545; }
        .badge-kasir { background-color: #0d6efd; }
        .badge-pegawai { background-color: #198754; }
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
            <li>
                <a href="item.php">
                    <i class="fas fa-box-open"></i> Item
                </a>
            </li>
            <li class="active">
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
                <a href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="mb-4">Manajemen Petugas</h1>

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

        <!-- Form Tambah/Edit Petugas -->
        <div class="form-container">
            <h2 class="section-title"><?= isset($edit_data) ? 'Edit Petugas' : 'Tambah Petugas Baru' ?></h2>
            <form method="POST" class="mt-3">
                <input type="hidden" name="id" value="<?= isset($edit_data) ? $edit_data['id_user'] : '' ?>">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_user" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_user" name="nama_user" 
                               value="<?= isset($edit_data) ? htmlspecialchars($edit_data['nama_user']) : '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= isset($edit_data) ? htmlspecialchars($edit_data['username']) : '' ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               <?= !isset($edit_data) ? 'required' : 'placeholder="Biarkan kosong jika tidak ingin mengubah"' ?>>
                        <?php if(isset($edit_data)): ?>
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="admin" <?= (isset($edit_data) && $edit_data['level'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="kasir" <?= (isset($edit_data) && $edit_data['level'] == 'kasir') ? 'selected' : '' ?>>Kasir</option>
                            <option value="pegawai" <?= (isset($edit_data) && $edit_data['level'] == 'pegawai') ? 'selected' : '' ?>>Pegawai</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <?php if(isset($edit_data)): ?>
                        <button type="submit" name="edit" class="btn btn-warning me-2">
                            <i class="fas fa-save me-1"></i> Update
                        </button>
                        <a href="petugas.php" class="btn btn-secondary">
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

        <!-- Daftar Petugas -->
        <div class="table-container">
            <h2 class="section-title">Daftar Petugas</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Petugas</th>
                            <th>Username</th>
                            <th width="15%">Level</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($petugas) > 0): ?>
                            <?php $no = 1; while($row = mysqli_fetch_assoc($petugas)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill badge-<?= $row['level'] ?>">
                                            <?= ucfirst($row['level']) ?>
                                        </span>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="petugas.php?edit=<?= $row['id_user'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <?php if($row['id_user'] != ($_SESSION['id_user'] ?? 0)): ?>
                                            <a href="petugas.php?hapus=<?= $row['id_user'] ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin ingin menghapus petugas ini?')">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data petugas</td>
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

<?php 
mysqli_close($conn); 
?>