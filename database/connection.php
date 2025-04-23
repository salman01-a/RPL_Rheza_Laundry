<?php 
$conn = new mysqli("localhost","root","","db_rheza_laundry");
if($conn -> connect_error){
    die($conn -> connect_error);
}
?>