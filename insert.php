<?php
require_once('connexio.php');

$codpob = $_POST["codpob"];
$despob = $_POST["despob"];
$urlpob = $_POST["urlpob"];

$sql = "INSERT INTO poblacio (codpob, despob, urlpob) VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

// Asociar los parámetros a la sentencia SQL
$stmt->bind_param("sss", $codpob, $despob, $urlpob);  // "sss" indica que los tres parámetros son strings

// Ejecutar la sentencia
if ($stmt->execute()) {
    header("Location: poblacio.php");
    echo "Nuevo registro insertado correctamente.";
} else {
    echo "Error al insertar los datos: " . $stmt->error;
}
