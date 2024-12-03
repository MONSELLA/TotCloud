<?php
include('connection.php');
header('Content-Type: application/json');

// Consulta per obtenir els privilegis que es poden associar a un usuari
$sql = "SELECT nom FROM privilegis";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Error en preparar la consulta: " . $conn->error]);
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = [];
    // Agafem els resultats de la consulta i els afegim a l'array $data
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);  // Retornem els resultats com a JSON
} else {
    echo json_encode([]);  // Si no hi ha resultats, retornem un array buit
}

$stmt->close();
$conn->close();
