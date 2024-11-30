<?php
header('Content-Type: application/json');
session_start();

// Connexió a la base de dades
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "TotCloud";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connexió fallida: " . $conn->connect_error]);
    exit();
}

// Comprovar si l'usuari ha iniciat sessió
if (!isset($_SESSION['id'])) {
    echo json_encode(["error" => "No has iniciat sessió."]);
    exit();
}

$idClient = $_SESSION['id'];

// Consulta per obtenir les màquines virtuals del client
$sql = "SELECT idMaquina, nom, ip, mac, sistema_operatiu.nom AS sistema, versio
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
            "idMaquina" => $mv['idMaquina'],
            "nom" => $mv['nom'],
            "ip" => $mv['ip'],
            "mac" => $mv['mac'],
            "sistema" => $mv['sistema'],
            "versio" => $mv['versio'],
            "components" => []
        ];

        // Obtenir CPU
        $sql_cpu = "SELECT c.velocitatRellotge, c.nuclis, c.preu, f.nom AS fase, m.nom AS model
                    FROM CPU c
                    JOIN FASE f ON c.nomFase = f.nom
                    JOIN MODEL m ON c.nomModel = m.nom
                    WHERE c.idMaquina = ?";
        $stmt_cpu = $conn->prepare($sql_cpu);
        $stmt_cpu->bind_param("i", $mv['idMaquina']);
        $stmt_cpu->execute();
        $result_cpu = $stmt_cpu->get_result();
        if ($cpu = $result_cpu->fetch_assoc()) {
            $maquina['components']['cpu'] = [
                "velocitatRellotge" => $cpu['velocitatRellotge'],
                "nuclis" => $cpu['nuclis'],
                "preu" => $cpu['preu'],
                "fase" => $cpu['fase'],
                "model" => $cpu['model']
            ];
        }

        // Obtenir GPU
        $sql_gpu = "SELECT g.nuclis, g.preu, v.capacitat AS vram, gen.nom AS generacio, f.nom AS fase, m.nom AS model
                    FROM GPU g
                    JOIN VRAM v ON g.idVRAM = v.idVRAM
                    JOIN GENERACIO gen ON v.generacio = gen.nom
                    JOIN FASE f ON g.nomFase = f.nom
                    JOIN MODEL m ON g.nomModel = m.nom
                    WHERE g.idMaquina = ?";
        $stmt_gpu = $conn->prepare($sql_gpu);
        $stmt_gpu->bind_param("i", $mv['idMaquina']);
        $stmt_gpu->execute();
        $result_gpu = $stmt_gpu->get_result();
        if ($gpu = $result_gpu->fetch_assoc()) {
            $maquina['components']['gpu'] = [
                "nuclis" => $gpu['nuclis'],
                "preu" => $gpu['preu'],
                "vram" => $gpu['vram'],
                "generacio" => $gpu['generacio'],
                "fase" => $gpu['fase'],
                "model" => $gpu['model']
            ];
        }

        // Obtenir RAM
        $sql_ram = "SELECT r.capacitat, r.preu, gen.nom AS generacio, f.nom AS fase
                    FROM RAM r
                    JOIN GENERACIO gen ON r.generacio = gen.nom
                    JOIN FASE f ON r.nomFase = f.nom
                    WHERE r.idMaquina = ?";
        $stmt_ram = $conn->prepare($sql_ram);
        $stmt_ram->bind_param("i", $mv['idMaquina']);
        $stmt_ram->execute();
        $result_ram = $stmt_ram->get_result();
        if ($ram = $result_ram->fetch_assoc()) {
            $maquina['components']['ram'] = [
                "capacitat" => $ram['capacitat'],
                "preu" => $ram['preu'],
                "generacio" => $ram['generacio'],
                "fase" => $ram['fase']
            ];
        }

        // Obtenir Discos Durs
        $sql_disc = "SELECT d.capacitat, d.preu, t.nom AS tipus, f.nom AS fase
                     FROM DISC_DUR d
                     JOIN TIPUS t ON d.nomTipus = t.nom
                     JOIN FASE f ON d.nomFase = f.nom
                     WHERE d.idMaquina = ?";
        $stmt_disc = $conn->prepare($sql_disc);
        $stmt_disc->bind_param("i", $mv['idMaquina']);
        $stmt_disc->execute();
        $result_disc = $stmt_disc->get_result();
        $discos = [];
        while ($disc = $result_disc->fetch_assoc()) {
            $discos[] = [
                "capacitat" => $disc['capacitat'],
                "preu" => $disc['preu'],
                "tipus" => $disc['tipus'],
                "fase" => $disc['fase']
            ];
        }
        if (!empty($discos)) {
            $maquina['components']['discos'] = $discos;
        }

        $maquines[] = $maquina;
    }
}

echo json_encode($maquines);

$stmt->close();
$conn->close();
