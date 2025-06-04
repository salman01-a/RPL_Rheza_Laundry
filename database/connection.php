<?php 
$conn = new mysqli("localhost","root","","RPL");
if($conn -> connect_error){
    die($conn -> connect_error);
}
?>