<?php
require_once('connexio.php');

$codpob = $_POST["codpob"];

$sql = "DELETE FROM poblacio WHERE codpob = ?";

$stmt = $conn->prepare($sql);

// Asociar los parámetros a la sentencia SQL
$stmt->bind_param("s", $codpob);  // "s" indica que los tres parámetros son strings

// Ejecutar la sentencia
if ($stmt->execute()) {
    header("Location: poblacio.php");
    echo "Nuevo registro insertado correctamente.";
} else {
    echo "Error al insertar los datos: " . $stmt->error;
}
