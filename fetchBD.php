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
$sql = "SELECT idBD, nom, idClient, idRAMV, idEmmagatzemament AS idEmm, idSGBD, idCPUV
        FROM base_de_dades
        WHERE idClient = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idClient);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();

$bds = [];

if ($result->num_rows > 0) {
    while ($bd = $result->fetch_assoc()) {
        $base = [
            "nom" => $bd['nom'],
            "components" => [
                "cpuv" => [],
                "ramv" => [],
                "emm" => [],
                "sgbd" => []
            ],
            "usuaris" => []
        ];

        // Obtenir CPU
        $sql_cpu = "SELECT velocitatRellotge
                    FROM cpu_virtual
                    WHERE idcpuv = ?";
        $stmt_cpu = $conn->prepare($sql_cpu);
        $stmt_cpu->bind_param("i", $bd['idCPUV']);
        $stmt_cpu->execute();
        $result_cpu = $stmt_cpu->get_result();
        if ($cpu = $result_cpu->fetch_assoc()) {
            $base['components']['cpuv'][] = [
                "velocitatRellotge" => $cpu['velocitatRellotge']
            ];
        }
        $stmt_cpu->close();

        // Obtenir RAM
        $sql_ram = "SELECT capacitat
                    FROM ram_virtual
                    WHERE idramv = ?";
        $stmt_ram = $conn->prepare($sql_ram);
        $stmt_ram->bind_param("i", $bd['idRAMV']);
        $stmt_ram->execute();
        $result_ram = $stmt_ram->get_result();
        if ($ram = $result_ram->fetch_assoc()) {
            $base['components']['ramv'][] = [
                "capacitat" => $ram['capacitat']
            ];
        }
        $stmt_ram->close();

        // Obtenir Emmagatzemament
        $sql_emm = "SELECT capacitat
                    FROM emmagatzemament
                    WHERE idEmmagatzemament = ?";
        $stmt_emm = $conn->prepare($sql_emm);
        $stmt_emm->bind_param("i", $bd['idEmm']);
        $stmt_emm->execute();
        $result_emm = $stmt_emm->get_result();
        if ($emm = $result_emm->fetch_assoc()) {
            $base['components']['emm'][] = [
                "capacitat" => $emm['capacitat']
            ];
        }
        $stmt_emm->close();

        // Obtenir SGBD
        $sql_sgbd = "SELECT nom, versio
                    FROM SGBD
                    WHERE idSGBD = ?";
        $stmt_sgbd = $conn->prepare($sql_sgbd);
        $stmt_sgbd->bind_param("i", $bd['idSGBD']);
        $stmt_sgbd->execute();
        $result_sgbd = $stmt_sgbd->get_result();
        if ($sgbd = $result_sgbd->fetch_assoc()) {
            $base['components']['sgbd'][] = [
                "nom" => $sgbd['nom'],
                "versio" => $sgbd['versio']
            ];
        }
        $stmt_sgbd->close();

        // Obtenir els usuaris
        $sql_user = "SELECT nom,idUsuariBd
                    FROM USUARI_BD
                    WHERE idBD = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $bd['idBD']);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        $sql_p = "SELECT nomPrivilegi
            FROM te_PRIVILEGI_BD
            WHERE idUsuariBd = ?";
        $stmt_p = $conn->prepare($sql_p);
        while ($user = $result_user->fetch_assoc()) {
            //Per a cada usuari obtenim els seus privilegis
            $privilegis = [];

            $stmt_p->bind_param("i", $user['idUsuariBd']);
            $stmt_p->execute();
            $result_p = $stmt_p->get_result();
            while ($privilegi = $result_p->fetch_assoc()) {
                $privilegis[] = $privilegi["nomPrivilegi"];
            }
            //Guardar el nom d'usuari i els seus privilegis
            $base['usuaris'][] = [
                "nom" => $user['nom'],
                "privilegis" => $privilegis
            ];
        }
        $stmt_p->close();
        $stmt_user->close();

        $bds[] = $base;
    }
}

echo json_encode($bds);

$conn->close();
