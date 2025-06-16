<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM petugas WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password (gunakan password_verify jika password di-hash)
        if ($password == $user['password']) { // Ganti dengan password_verify jika menggunakan hash
            $_SESSION['username'] = $user['username'];
            $_SESSION['level'] = $user['level']; // Jika ada level/role
            $_SESSION['nama_petugas'] = $user['nama_petugas'];
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

// Jika sudah login, redirect ke index
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Koperasi Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: none;
        }
        .login-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 100px;
        }
        .btn-login {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
        }
        .btn-login:hover {
            background-color: #1a252f;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="card-header login-header py-3">
                <h4 class="text-center mb-0"><i class="fas fa-sign-in-alt"></i> LOGIN SISTEM</h4>
            </div>
            <div class="card-body p-4">
                <div class="logo">
                    <i class="fas fa-store fa-3x text-primary"></i>
                    <h3 class="mt-2">Koperasi Barang</h3>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-login py-2">
                            <i class="fas fa-sign-in-alt me-2"></i> Login
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3">
                <small class="text-muted">© <?php echo date('Y'); ?> Koperasi Barang</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>