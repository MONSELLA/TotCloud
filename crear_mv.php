<?php
include("connection.php");

// Iniciar la sessió
session_start();

// Comprovar si l'usuari ha iniciat sessió
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "No has iniciat sessió."]);
    exit();
}

// Establir el tipus de contingut com JSON
header('Content-Type: application/json');

try {
    // Iniciam una transacció
    $conn->begin_transaction();

    // Rebre les dades JSON enviades des de JavaScript
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Dades JSON invàlides.');
    }

    // Preparar la crida a l'Stored Procedure per validar els camps
    $sql = "CALL validar_camps(?, ?, ?);";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('No s\'ha pogut preparar la consulta: ' . $conn->error);
    }

    $vmName = htmlspecialchars($data['vmName']);
    $vmIp = htmlspecialchars($data['vmIp']);
    $vmMac = htmlspecialchars($data['vmMac']);

    $stmt->bind_param("sss", $vmName, $vmIp, $vmMac);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $cpus = $data['cpus']; // Array d'IDs de CPUs
    $gpus = $data['gpus']; // Array d'IDs de GPUs
    $discs_durs = $data['discs_durs']; // Array d'IDs de Discs Durs
    $rams = $data['rams']; // Array d'IDs de RAMs
    $sis_op = $data['sis_op']; // Array d'IDs de Sistemes Operatius

    // Validar que les dades requerides són presents
    if (!isset($vmName, $vmIp, $vmMac, $sis_op[0])) {
        throw new Exception('Falten camps obligatoris.');
    }

    // Preparar la consulta SQL
    $sql = "INSERT INTO MAQUINA_VIRTUAL (nom, ip, mac, idSO, idClient) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('No s\'ha pogut preparar la consulta: ' . $conn->error);
    }

    // Vincular els valors a la consulta preparada
    $idSO = (int)$sis_op[0];
    $idClient = (int)$_SESSION['id'];

    $stmt->bind_param("sssii", $vmName, $vmIp, $vmMac, $idSO, $idClient);

    // Executar la consulta
    if (!$stmt->execute()) {
        throw new Exception('No s\'ha pogut executar la consulta: ' . $stmt->error);
    }

    // Obtenir l'ID de la màquina virtual creada
    $idMaquina = (int)$conn->insert_id;

    // Tancar la consulta
    $stmt->close();

    // Establir la fk "idMaquina" dels components comprats per l'usuari
    // CPU
    $sql = "UPDATE CPU 
            SET idMaquina = ?
            WHERE idCpu = ?";
    foreach ($cpus as $id) {
        $idCPU = (int)$id;
        update($conn, $sql, $idMaquina, $idCPU);
    }

    // GPU
    $sql = "UPDATE GPU 
            SET idMaquina = ?
            WHERE idGpu = ?";
    foreach ($gpus as $id) {
        $idGPU = (int)$id;
        update($conn, $sql, $idMaquina, $idGPU);
    }

    // RAM
    $sql = "UPDATE RAM 
            SET idMaquina = ?
            WHERE idRam = ?";
    foreach ($rams as $id) {
        $idRAM = (int)$id;
        update($conn, $sql, $idMaquina, $idRAM);
    }

    // DISC_DUR
    $sql = "UPDATE DISC_DUR
            SET idMaquina = ?
            WHERE idDisc = ?";
    foreach ($discs_durs as $id) {
        $idDD = (int)$id;
        update($conn, $sql, $idMaquina, $idDD);
    }

    // Acceptar la transacció
    $conn->commit();
    // Retornar un JSON amb èxit
    echo json_encode([
        'success' => true,
        'message' => 'La màquina virtual s\'ha creat correctament.'
    ]);
} catch (Exception $e) {
    // Rebutjar la transacció
    $conn->rollback();
    // Retornar error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

// Tancar la connexió
$conn->close();

function update($conn, $sql, $idMaquina, $id)
{
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('No s\'ha pogut preparar la consulta: ' . $conn->error);
    }

    $stmt->bind_param("ii", $idMaquina, $id);

    // Executar la consulta
    if (!$stmt->execute()) {
        throw new Exception('No s\'ha pogut executar la consulta: ' . $stmt->error);
    }

    // Tancar la consulta
    $stmt->close();
}
