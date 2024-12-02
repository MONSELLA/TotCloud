<?php
include("connection.php");

// Iniciar la sessió
session_start();
$_SESSION['id'] = 1;
// Establir el tipus de contingut com JSON
header('Content-Type: application/json');

try {
    // Rebre les dades JSON enviades des de JavaScript
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Dades JSON invàlides.');
    }

    $bdName = htmlspecialchars($data['bdName']);
    $cpu = $data['cpu'];
    $emm = $data['emmagatzemament'];
    $ram = $data['ram'];
    $sgbd = $data['sgbd'];

    // Validar que les dades requerides són presents
    if (!isset($bdName, $sgbd[0], $ram[0], $emm[0], $cpu[0])) {
        throw new Exception('Falten camps obligatoris.');
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO BASE_DE_DADES (nom, idClient, idRAMV, idEmmagatzemament, idSGBD, idCPUV) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('No s\'ha pogut preparar la consulta: ' . $conn->error);
    }

    // Vincular els valors a la consulta preparada
    $idClient = (int)$_SESSION['id'];
    $idramv = (int)$ram[0];
    $idemm = (int)$emm[0];
    $idsgbd = (int)$sgbd[0];
    $idcpuv = (int)$cpu[0];

    $stmt->bind_param("siiiii", $bdName, $idClient, $idramv, $idemm, $idsgbd, $idcpuv);

    // Executar la consulta
    if (!$stmt->execute()) {
        throw new Exception('No s\'ha pogut executar la consulta: ' . $stmt->error);
    }

    // Obtenir l'ID de la BD
    $idBD = (int)$conn->insert_id;

    // Tancar la consulta
    $stmt->close();
    // Tancar la connexió
    $conn->close();

    // Retornar un JSON amb èxit
    echo json_encode([
        'success' => true,
        'message' => 'La base de dades s\'ha creat correctament.'
    ]);
} catch (Exception $e) {
    // Retornar error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
