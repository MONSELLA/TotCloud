<?php
require_once('connexio.php');
// Definir la consulta SQL
$sql = "SELECT * FROM poblacio";

// Ejecutar la consulta
$result = $conn->query($sql);

// Verificar si la consulta devolvió resultados
if ($result->num_rows > 0) {
    // Retornar los resultados
    return $result;
} else {
    echo "No hay datos disponibles.";
    return null;
}
