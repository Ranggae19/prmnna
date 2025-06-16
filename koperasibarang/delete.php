<?php
$conn = mysqli_connect("localhost", "root", "", "koperasibarang");
$id = $_GET['id'];

$sql = "DELETE FROM customer WHERE id_customer=$id";
if (mysqli_query($conn, $sql)) {
    header("Location: index.php");
} else {
    echo "Error: " . mysqli_error($conn);
}
mysqli_close($conn);
?>