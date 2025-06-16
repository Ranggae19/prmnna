<?php
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $fax = mysqli_real_escape_string($conn, $_POST['fax']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE customer SET 
            nama_customer='$nama', 
            alamat='$alamat', 
            telp='$telp', 
            fax='$fax', 
            email='$email' 
            WHERE id_customer=$id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data lama untuk ditampilkan di form
$sql = "SELECT * FROM customer WHERE id_customer=$id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
    <style>
        form { width: 50%; margin: 20px auto; }
        input, button { display: block; width: 100%; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Edit Data Customer</h2>
    
    <form method="POST">
        <input type="text" name="nama" value="<?= $data['nama_customer'] ?>" required>
        <input type="text" name="alamat" value="<?= $data['alamat'] ?>">
        <input type="text" name="telp" value="<?= $data['telp'] ?>">
        <input type="text" name="fax" value="<?= $data['fax'] ?>">
        <input type="email" name="email" value="<?= $data['email'] ?>">
        <button type="submit">Update</button>
    </form>
</body>
</html>