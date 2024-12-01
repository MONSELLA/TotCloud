<?php
include("connection.php");

header('Content-Type: application/json');

$queryType = $_GET['query'];
$asc = $_GET['asc'];
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';
$sql = "";

switch ($queryType) {
    case 'cpu':
        // Si no s'ha introduït cap marca, mostrem tots els components
        if ($brand == '') {
            $sql = "SELECT idcpu, model.nom, model.nomMarca, velocitatRellotge, nuclis, preu 
                    FROM cpu 
                    JOIN model ON cpu.nomModel = model.nom
                    WHERE idMaquina IS NULL 
                    AND nomFase = 'final'";
        } else {
            // Si hi ha marca, filtre per la marca especificada
            $sql = "SELECT idcpu, model.nom, model.nomMarca, velocitatRellotge, nuclis, preu 
                    FROM cpu 
                    JOIN model ON cpu.nomModel = model.nom
                    WHERE idMaquina IS NULL 
                    AND nomFase = 'final'
                    AND model.nomMarca LIKE ?";
        }
        break;

    case 'gpu':
        // Si no s'ha introduït cap marca, mostrem tots els components
        if ($brand == '') {
            $sql = "SELECT idgpu, model.nom, model.nomMarca, nuclis, vram.generacio, vram.capacitat, preu 
                    FROM model 
                    JOIN (gpu 
                        JOIN vram
                        ON vram.idVRAM = gpu.idVRAM) 
                    ON gpu.nomModel = model.nom
                    WHERE idMaquina IS NULL
                    AND nomFase = 'final'";
        } else {
            // Si hi ha marca, filtre per la marca especificada
            $sql = "SELECT idgpu, model.nom, model.nomMarca, nuclis, vram.generacio, vram.capacitat, preu 
                    FROM model 
                    JOIN (gpu 
                        JOIN vram
                        ON vram.idVRAM = gpu.idVRAM) 
                    ON gpu.nomModel = model.nom
                    WHERE idMaquina IS NULL
                    AND nomFase = 'final'
                    AND model.nomMarca LIKE ?";
        }
        break;

    case 'disc_dur':
        if ($brand == '') {
            // Consulta per a Disc Dur, sense filtrar per marca
            $sql = "SELECT iddisc, nomTipus, capacitat, preu 
                    FROM disc_dur
                    WHERE idMaquina IS NULL 
                    AND nomFase = 'final'";
        } else {
            $sql = "SELECT iddisc, nomTipus, capacitat, preu 
                    FROM disc_dur
                    WHERE idMaquina IS NULL 
                    AND nomFase = 'final'
                    AND nomTipus LIKE ?";
        }
        break;

    case 'ram':
        if ($brand == '') {
            // Consulta per a RAM, sense filtrar per marca
            $sql = "SELECT idram, generacio, capacitat, preu 
                FROM ram
                WHERE idMaquina IS NULL 
                AND nomFase = 'final'";
        } else {
            // Consulta per a RAM, sense filtrar per marca
            $sql = "SELECT idram, generacio, capacitat, preu 
                FROM ram
                WHERE idMaquina IS NULL 
                AND nomFase = 'final'
                AND generacio LIKE ?";
        }
        break;

    case 'sistema_operatiu':
        if ($brand == '') {
            // Consulta per a Sistema Operatiu, sense filtrar per marca
            $sql = "SELECT idSO, nom, versio
                FROM sistema_operatiu";
        } else {
            $sql = "SELECT idSO, nom, versio
                FROM sistema_operatiu
                WHERE nom LIKE ?";
        }
        break;

    case 'cpu_virtual';
        $sql = "SELECT idCPUV, velocitatRellotge, preu
                FROM cpu_virtual";
        break;

    case 'ram_virtual';
        $sql = "SELECT idRAMV, capacitat, preu
                FROM ram_virtual";
        break;

    case 'emmagatzemament';
        $sql = "SELECT idEmmagatzemament, capacitat, preu
                FROM emmagatzemament";
        break;

    case 'sgbd';
        $sql = "SELECT idSGBD, nom, versio
                FROM sgbd";
        break;

    default:
        // Si no es reconeix el tipus de consulta, retornem un array buit
        echo json_encode([]);
        exit;
}

if (($queryType != 'sistema_operatiu') && ($queryType != 'sgbd')) {
    $sql .= " ORDER BY preu $asc";
}

if ($stmt = $conn->prepare($sql)) {
    if ($brand != '') {
        $brand = "%" . $brand . "%";  // Usar LIKE per a una cerca parcial
        $stmt->bind_param('s', $brand);  // Enllaçar el paràmetre de la marca (cadena)
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    // Agafem els resultats de la consulta i els afegim a l'array $data
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);  // Retornem els resultats com a JSON
    $stmt->close();
    $conn->close();
} else {
    echo json_encode([]);  // Si no hi ha resultats, retornem un array buit
    $stmt->close();
    $conn->close();
}
