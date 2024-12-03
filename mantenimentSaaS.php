<?php
// Incloure les classes necessàries
include "connection.php";
include "CONFIGURACIO.php";
include "CPUVIRTUAL.php";
include "EMMAGATZEMAMENT.php";
include "RAMVIRTUAL.php";
include "SGBD.php";

// Crear instàncies de les classes
$configManager = new CONFIGURACIO($conn);
$cpuManager = new CPUVIRTUAL($conn);
$storageManager = new EMMAGATZEMAMENT($conn);
$ramManager = new RAMVIRTUAL($conn);
$sgbdManager = new SGBD($conn);

// Gestionar sol·licituds POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_section = $_GET['section'] ?? 'configuracio'; // Obtenir la secció actual

    // CONFIGURACIO
    if (isset($_POST['add_configuracio'])) {
        $port = $_POST['port'] ?? null;
        if ($port)
            $configManager->addConfiguracio($port);
    } elseif (isset($_POST['delete_configuracio'])) {
        $idConfig = $_POST['idConfig'] ?? null;
        if ($idConfig)
            $configManager->deleteConfiguracio($idConfig);
    } elseif (isset($_POST['update_configuracio'])) {
        $idConfig = $_POST['idConfig'] ?? null;
        $port = $_POST['port'] ?? null;
        if ($idConfig && $port)
            $configManager->updateConfiguracio($idConfig, $port);
    }

    // CPUVIRTUAL
    if (isset($_POST['add_cpuvirtual'])) {
        $velocitatRellotge = $_POST['velocitatRellotge'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($velocitatRellotge && $preu)
            $cpuManager->addCPUVIRTUAL($velocitatRellotge, $preu);
    } elseif (isset($_POST['delete_cpuvirtual'])) {
        $idCPUV = $_POST['idCPUV'] ?? null;
        if ($idCPUV)
            $cpuManager->deleteCPUVIRTUAL($idCPUV);
    } elseif (isset($_POST['update_cpuvirtual'])) {
        $idCPUV = $_POST['idCPUV'] ?? null;
        $velocitatRellotge = $_POST['velocitatRellotge'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($idCPUV && $velocitatRellotge && $preu)
            $cpuManager->updateCPUVIRTUAL($idCPUV, $velocitatRellotge, $preu);
    }

    // EMMAGATZEMAMENT
    if (isset($_POST['add_emmagatzemament'])) {
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($capacitat && $preu)
            $storageManager->addEmmagatzemament($capacitat, $preu);
    } elseif (isset($_POST['delete_emmagatzemament'])) {
        $idEmmagatzemament = $_POST['idEmmagatzemament'] ?? null;
        if ($idEmmagatzemament)
            $storageManager->deleteEmmagatzemament($idEmmagatzemament);
    } elseif (isset($_POST['update_emmagatzemament'])) {
        $idEmmagatzemament = $_POST['idEmmagatzemament'] ?? null;
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($idEmmagatzemament && $capacitat && $preu)
            $storageManager->updateEmmagatzemament($idEmmagatzemament, $capacitat, $preu);
    }

    // RAMVIRTUAL
    if (isset($_POST['add_ramvirtual'])) {
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($capacitat && $preu)
            $ramManager->addRAMVIRTUAL($capacitat, $preu);
    } elseif (isset($_POST['delete_ramvirtual'])) {
        $idRAMV = $_POST['idRAMV'] ?? null;
        if ($idRAMV)
            $ramManager->deleteRAMVIRTUAL($idRAMV);
    } elseif (isset($_POST['update_ramvirtual'])) {
        $idRAMV = $_POST['idRAMV'] ?? null;
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        if ($idRAMV && $capacitat && $preu)
            $ramManager->updateRAMVIRTUAL($idRAMV, $capacitat, $preu);
    }

    // SGBD
    if (isset($_POST['add_sgbd'])) {
        $nom = $_POST['nom'] ?? null;
        $versio = $_POST['versio'] ?? null;
        $idConfig = $_POST['idConfig'] ?? null;
        if ($nom && $versio && $idConfig)
            $sgbdManager->addSGBD($nom, $versio, $idConfig);
    } elseif (isset($_POST['delete_sgbd'])) {
        $idSGBD = $_POST['idSGBD'] ?? null;
        if ($idSGBD)
            $sgbdManager->deleteSGBD($idSGBD);
    } elseif (isset($_POST['update_sgbd'])) {
        $idSGBD = $_POST['idSGBD'] ?? null;
        $nom = $_POST['nom'] ?? null;
        $versio = $_POST['versio'] ?? null;
        $idConfig = $_POST['idConfig'] ?? null;
        if ($idSGBD && $nom && $versio && $idConfig)
            $sgbdManager->updateSGBD($idSGBD, $nom, $versio, $idConfig);
    }

    // Redirigir després de processar
    header("Location: " . $_SERVER['PHP_SELF'] . "?section=" . $current_section);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestió de Recursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            height: 100vh;
        }

        .menu-button {
            padding: 1rem;
            border: none;
            background-color: #f8f9fa;
            cursor: pointer;
            width: 100%;
        }

        .menu-button:hover {
            background-color: #e2e6ea;
        }

        .menu-button.active {
            background-color: #cfe2ff;
            font-weight: bold;
        }

        .content {
            padding: 2rem;
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar">
            <button
                class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'configuracio' ? 'active' : '' ?>"
                onclick="location.href='?section=configuracio'">Configuració</button>
            <button
                class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'cpuvirtual' ? 'active' : '' ?>"
                onclick="location.href='?section=cpuvirtual'">CPU</button>
            <button
                class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'emmagatzemament' ? 'active' : '' ?>"
                onclick="location.href='?section=emmagatzemament'">Emmagatzematge</button>
            <button
                class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'ramvirtual' ? 'active' : '' ?>"
                onclick="location.href='?section=ramvirtual'">RAM</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'sgbd' ? 'active' : '' ?>"
                onclick="location.href='?section=sgbd'">SGBD</button>
        </nav>

        <!-- Content -->
        <div class="content">
            <?php
            // Mostrar la sección correspondiente
            $section = $_GET['section'] ?? 'configuracio';
            switch ($section) {
                case 'configuracio':
                    echo $configManager->getHTML();
                    break;
                case 'cpuvirtual':
                    echo $cpuManager->getHTML();
                    break;
                case 'emmagatzemament':
                    echo $storageManager->getHTML();
                    break;
                case 'ramvirtual':
                    echo $ramManager->getHTML();
                    break;
                case 'sgbd':
                    echo $sgbdManager->getHTML();
                    break;
                default:
                    echo "<h2 class='text-danger'>Secció desconeguda.</h2>";
                    break;
            }
            ?>
        </div>
    </div>
</body>

</html>