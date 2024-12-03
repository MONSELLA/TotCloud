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
        throw new Exception('No s\'ha pogut crear la base de dades: ' . $conn->error);
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
        throw new Exception('No s\'ha pogut crear la base de dades: ' . $stmt->error);
    }

    // Obtenir l'ID de la BD creada
    $idBD = (int)$conn->insert_id;

    // Tancar la consulta
    $stmt->close();

    // Crear els usuaris de la base de dades
    foreach ($data['users'] as $user) {
        $sql = "INSERT INTO USUARI_BD (nom, contrasenya, idBD) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('No s\'ha pogut crear la base de dades: ' . $conn->error);
        }

        $nom = $user['username'];
        $contrasenya = password_hash($user['password'], PASSWORD_DEFAULT); //encriptar la contrasenya

        $stmt->bind_param("ssi", $nom, $contrasenya, $idBD);
        // Executar la consulta
        if (!$stmt->execute()) {
            throw new Exception('No s\'ha pogut crear la base de dades: ' . $stmt->error);
        }
        // Tancar la consulta
        $stmt->close();

        //Associar els privilegis a l'usuari creat
        $idUser = (int)$conn->insert_id;
        foreach ($user['privileges'] as $privilege) {
            $sql = "INSERT INTO te_PRIVILEGI_BD (nomPrivilegi, idUsuariBd) 
                VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('No s\'ha pogut crear la base de dades: ' . $conn->error);
            }

            $stmt->bind_param("si", $privilege, $idUser);
            // Executar la consulta
            if (!$stmt->execute()) {
                throw new Exception('No s\'ha pogut crear la base de dades: ' . $stmt->error);
            }
            // Tancar la consulta
            $stmt->close();
        }
    }

    // Acceptar la transacció
    $conn->commit();
    // Retornar un JSON amb èxit
    echo json_encode([
        'success' => true,
        'message' => 'La base de dades s\'ha creat correctament.'
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
