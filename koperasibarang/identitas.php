<?php
// KONEKSI DATABASE
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// PROSES DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM identitas WHERE id_identitas = $delete_id";
    if (mysqli_query($conn, $sql)) {
        header("Location: identitas.php");
        exit();
    } else {
        $error = "Error deleting record: " . mysqli_error($conn);
    }
}

// PROSES FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $data = [
        'nama_identitas' => mysqli_real_escape_string($conn, $_POST['nama_identitas']),
        'badan_hukum' => mysqli_real_escape_string($conn, $_POST['badan_hukum']),
        'npwp' => mysqli_real_escape_string($conn, $_POST['npwp']),
        'email' => mysqli_real_escape_string($conn, $_POST['email']),
        'url' => mysqli_real_escape_string($conn, $_POST['url']),
        'alamat' => mysqli_real_escape_string($conn, $_POST['alamat']),
        'telp' => mysqli_real_escape_string($conn, $_POST['telp']),
        'fax' => mysqli_real_escape_string($conn, $_POST['fax']),
        'rekening' => mysqli_real_escape_string($conn, $_POST['rekening'])
    ];

    // Handle file upload
    $current_foto = null;
    if (!empty($_POST['current_foto'])) {
        $current_foto = $_POST['current_foto'];
    }
    
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/identitas/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Hapus foto lama jika ada
        if ($current_foto && file_exists($current_foto)) {
            unlink($current_foto);
        }
        
        $file_ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $data['foto'] = $target_file;
        }
    } elseif ($current_foto) {
        $data['foto'] = $current_foto;
    } else {
        $data['foto'] = '';
    }

    if (empty($id)) {
        // INSERT DATA BARU
        $sql = "INSERT INTO identitas (nama_identitas, badan_hukum, npwp, email, url, alamat, telp, fax, rekening, foto) 
                VALUES (
                    '{$data['nama_identitas']}',
                    '{$data['badan_hukum']}',
                    '{$data['npwp']}',
                    '{$data['email']}',
                    '{$data['url']}',
                    '{$data['alamat']}',
                    '{$data['telp']}',
                    '{$data['fax']}',
                    '{$data['rekening']}',
                    '{$data['foto']}'
                )";
    } else {
        // UPDATE DATA
        $sql = "UPDATE identitas SET 
                nama_identitas='{$data['nama_identitas']}',
                badan_hukum='{$data['badan_hukum']}',
                npwp='{$data['npwp']}',
                email='{$data['email']}',
                url='{$data['url']}',
                alamat='{$data['alamat']}',
                telp='{$data['telp']}',
                fax='{$data['fax']}',
                rekening='{$data['rekening']}',
                foto='{$data['foto']}'
                WHERE id_identitas=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: identitas.php");
        exit();
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// AMBIL DATA JIKA EDIT
$editData = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM identitas WHERE id_identitas = $id");
    $editData = mysqli_fetch_assoc($result);
}

// AMBIL SEMUA DATA
$result = mysqli_query($conn, "SELECT * FROM identitas ORDER BY id_identitas DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Identitas - Koperasi Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><i class="fas fa-id-card me-2"></i>Data Identitas Koperasi</h3>
            </div>
            
            <div class="card-body">
                <!-- FORM INPUT -->
                <div class="form-container mb-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $editData['id_identitas'] ?? '' ?>">
                        <input type="hidden" name="current_foto" value="<?= $editData['foto'] ?? '' ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Koperasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_identitas" 
                                       value="<?= htmlspecialchars($editData['nama_identitas'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Badan Hukum <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="badan_hukum" 
                                       value="<?= htmlspecialchars($editData['badan_hukum'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NPWP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="npwp" 
                                       value="<?= htmlspecialchars($editData['npwp'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= htmlspecialchars($editData['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" name="url" 
                                       value="<?= htmlspecialchars($editData['url'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="telp" 
                                       value="<?= htmlspecialchars($editData['telp'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= htmlspecialchars($editData['alamat'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fax</label>
                                <input type="text" class="form-control" name="fax" 
                                       value="<?= htmlspecialchars($editData['fax'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Rekening Bank <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="rekening" 
                                       value="<?= htmlspecialchars($editData['rekening'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Logo Koperasi</label>
                            <input type="file" class="form-control" name="foto" accept="image/*">
                            <?php if (!empty($editData['foto'])): ?>
                                <div class="mt-2">
                                    <img src="<?= $editData['foto'] ?>" class="logo-preview img-thumbnail">
                                    <p class="text-muted small mt-1">File saat ini: <?= basename($editData['foto']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                            <?php if (!empty($editData)): ?>
                                <a href="identitas.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- TABEL DATA -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Koperasi</th>
                                <th>Badan Hukum</th>
                                <th>NPWP</th>
                                <th>Kontak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $row['id_identitas'] ?></td>
                                        <td><?= htmlspecialchars($row['nama_identitas']) ?></td>
                                        <td><?= htmlspecialchars($row['badan_hukum']) ?></td>
                                        <td><?= htmlspecialchars($row['npwp']) ?></td>
                                        <td>
                                            <small>
                                                Telp: <?= htmlspecialchars($row['telp']) ?><br>
                                                Email: <?= htmlspecialchars($row['email']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="identitas.php?id=<?= $row['id_identitas'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="identitas.php?delete_id=<?= $row['id_identitas'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data identitas</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// TUTUP KONEKSI
mysqli_close($conn);
?>