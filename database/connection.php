<?php 
$conn = new mysqli("localhost","root","","RPL");
if($conn -> connect_error){
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>