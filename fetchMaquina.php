<?php
include('connection.php');
header('Content-Type: application/json');
session_start();

// Comprovar si l'usuari ha iniciat sessió
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "No has iniciat sessió."]);
    exit();
}

$idClient = (int)$_SESSION['id'];

// Consulta per obtenir les màquines virtuals del client
$sql = "SELECT idMaquina, maquina_virtual.nom, ip, mac, sistema_operatiu.nom AS sistema, versio
        FROM maquina_virtual
        JOIN sistema_operatiu ON maquina_virtual.idSO = sistema_operatiu.idSO
        WHERE idClient = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idClient);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();

$maquines = [];

if ($result->num_rows > 0) {
    while ($mv = $result->fetch_assoc()) {
        $maquina = [
            "nom" => $mv['nom'],
            "ip" => $mv['ip'],
            "mac" => $mv['mac'],
            "sistema" => $mv['sistema'],
            "versio" => $mv['versio'],
            "components" => [
                "cpu" => [],
                "gpu" => [],
                "ram" => [],
                "discos" => []
            ]
        ];

        // Obtenir CPU
        $sql_cpu = "SELECT velocitatRellotge, nuclis, model.nom AS model, model.nomMarca AS marca
                    FROM cpu
                    JOIN model ON cpu.nomModel = model.nom
                    WHERE idMaquina = ?";
        $stmt_cpu = $conn->prepare($sql_cpu);
        $stmt_cpu->bind_param("i", $mv['idMaquina']);
        $stmt_cpu->execute();
        $result_cpu = $stmt_cpu->get_result();
        while ($cpu = $result_cpu->fetch_assoc()) {
            $maquina['components']['cpu'][] = [
                "velocitatRellotge" => $cpu['velocitatRellotge'],
                "nuclis" => $cpu['nuclis'],
                "model" => $cpu['model'],
                "marca" => $cpu['marca']
            ];
        }
        $stmt_cpu->close();

        // Obtenir GPU
        $sql_gpu = "SELECT nuclis, capacitat, generacio, model.nom AS model, model.nomMarca AS marca
                    FROM gpu
                    JOIN vram ON gpu.idVRAM = vram.idVRAM
                    JOIN model ON gpu.nomModel = model.nom
                    WHERE idMaquina = ?";
        $stmt_gpu = $conn->prepare($sql_gpu);
        $stmt_gpu->bind_param("i", $mv['idMaquina']);
        $stmt_gpu->execute();
        $result_gpu = $stmt_gpu->get_result();
        while ($gpu = $result_gpu->fetch_assoc()) {
            $maquina['components']['gpu'][] = [
                "nuclis" => $gpu['nuclis'],
                "capacitat" => $gpu['capacitat'],
                "generacio" => $gpu['generacio'],
                "model" => $gpu['model'],
                "marca" => $gpu['marca']
            ];
        }
        $stmt_gpu->close();

        // Obtenir RAM
        $sql_ram = "SELECT capacitat, generacio
                    FROM ram
                    WHERE idMaquina = ?";
        $stmt_ram = $conn->prepare($sql_ram);
        $stmt_ram->bind_param("i", $mv['idMaquina']);
        $stmt_ram->execute();
        $result_ram = $stmt_ram->get_result();
        while ($ram = $result_ram->fetch_assoc()) {
            $maquina['components']['ram'][] = [
                "capacitat" => $ram['capacitat'],
                "generacio" => $ram['generacio']
            ];
        }
        $stmt_ram->close();

        // Obtenir Discos Durs
        $sql_disc = "SELECT capacitat, nomTipus AS tipus
                    FROM disc_dur
                    WHERE idMaquina = ?";
        $stmt_disc = $conn->prepare($sql_disc);
        $stmt_disc->bind_param("i", $mv['idMaquina']);
        $stmt_disc->execute();
        $result_disc = $stmt_disc->get_result();
        while ($disc = $result_disc->fetch_assoc()) {
            $maquina['components']['discos'][] = [
                "capacitat" => $disc['capacitat'],
                "tipus" => $disc['tipus']
            ];
        }
        $stmt_disc->close();

        $maquines[] = $maquina;
    }
}

echo json_encode($maquines);

$conn->close();
