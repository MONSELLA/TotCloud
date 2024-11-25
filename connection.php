<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$dbname = 'ex_classe';

$conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

// Comprovar connexió
if (!$conn) {
    die("Error de connexió a la base de dades: " . mysqli_connect_error());
}
?>