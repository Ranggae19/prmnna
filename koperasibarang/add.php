<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = mysqli_connect("localhost", "root", "", "koperasibarang");
    
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $fax = mysqli_real_escape_string($conn, $_POST['fax']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "INSERT INTO customer (nama_customer, alamat, telp, fax, email) 
            VALUES ('$nama', '$alamat', '$telp', '$fax', '$email')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php"); // Redirect setelah berhasil
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Customer</title>
    <style>
        form { width: 50%; margin: 20px auto; }
        input, button { display: block; width: 100%; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Tambah Customer Baru</h2>
    
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Customer" required>
        <input type="text" name="alamat" placeholder="Alamat">
        <input type="text" name="telp" placeholder="Telepon">
        <input type="text" name="fax" placeholder="Fax">
        <input type="email" name="email" placeholder="Email">
        <button type="submit">Simpan</button>
    </form>
</body>
</html>