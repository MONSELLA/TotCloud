<?php
// Incloure les classes necessàries
include "connection.php";
include "CPU.php";
include "RAM.php";
include "GPU.php";
include "DISCDUR.php";
include "TIPUS.php";
include "SO.php";
include "MODEL.php";
include "MARCA.php";
include "VRAM.php";
include "GENERACIO.php";
include "FASE.php";

// Crear instàncies de les classes
$cpuManager = new CPU($conn);
$ramManager = new RAM($conn);
$gpuManager = new GPU($conn);
$discdurManager = new DISCDUR($conn);
$tipusManager = new TIPUS($conn);
$soManager = new SO($conn);
$modelManager = new MODEL($conn);
$marcaManager = new MARCA($conn);
$vramManager = new VRAM($conn);
$generacioManager = new GENERACIO($conn);
$faseManager = new FASE($conn);

// Gestionar sol·licituds POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_section = $_GET['section'] ?? 'cpu'; // Secció actual per defecte

    // CPU
    if (isset($_POST['add_cpu'])) {
        $velocitatRellotge = $_POST['velocitatRellotge'] ?? null;
        $nuclis = $_POST['nuclis'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomModel = $_POST['nomModel'] ?? null;
        if ($velocitatRellotge && $nuclis && $preu && $nomFase && $nomModel) {
            $cpuManager->addCPU($velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel);
        }
    } elseif (isset($_POST['delete_cpu'])) {
        $idCpu = $_POST['idCpu'] ?? null;
        if ($idCpu) {
            $cpuManager->deleteCPU($idCpu);
        }
    } elseif (isset($_POST['update_cpu'])) {
        // Implementar actualització de CPU
        $idCpu = $_POST['old_idCpu'] ?? null;
        $velocitatRellotge = $_POST['velocitatRellotge'] ?? null;
        $nuclis = $_POST['nuclis'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomModel = $_POST['nomModel'] ?? null;
        if ($idCpu && $velocitatRellotge && $nuclis && $preu && $nomFase && $nomModel) {
            $cpuManager->updateCPU($idCpu, $velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel);
        }
    }

    // RAM
    if (isset($_POST['add_ram'])) {
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $generacio = $_POST['generacio'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        if ($capacitat && $preu && $generacio && $nomFase) {
            $ramManager->addRAM($capacitat, $preu, $generacio, $nomFase);
        }
    } elseif (isset($_POST['delete_ram'])) {
        $idRAM = $_POST['idRAM'] ?? null;
        if ($idRAM) {
            $ramManager->deleteRAM($idRAM);
        }
    } elseif (isset($_POST['update_ram'])) {
        // Implementar actualització de RAM
        $idRAM = $_POST['old_idRAM'] ?? null;
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $generacio = $_POST['generacio'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        if ($idRAM && $capacitat && $preu && $generacio && $nomFase) {
            $ramManager->updateRAM($idRAM, $capacitat, $preu, $generacio, $nomFase);
        }
    }

    // GPU
    if (isset($_POST['add_gpu'])) {
        $nuclis = $_POST['nuclis'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $idVRAM = $_POST['idVRAM'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomModel = $_POST['nomModel'] ?? null;
        if ($nuclis && $preu && $idVRAM && $nomFase && $nomModel) {
            $gpuManager->addGPU($nuclis, $preu, $idVRAM, $nomFase, $nomModel);
        }
    } elseif (isset($_POST['delete_gpu'])) {
        $idGPU = $_POST['idGPU'] ?? null;
        if ($idGPU) {
            $gpuManager->deleteGPU($idGPU);
        }
    } elseif (isset($_POST['update_gpu'])) {
        // Implementar actualització de GPU
        $idGPU = $_POST['old_idGPU'] ?? null;
        $nuclis = $_POST['nuclis'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $idVRAM = $_POST['idVRAM'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomModel = $_POST['nomModel'] ?? null;
        if ($idGPU && $nuclis && $preu && $idVRAM && $nomFase && $nomModel) {
            $gpuManager->updateGPU($idGPU, $nuclis, $preu, $idVRAM, $nomFase, $nomModel);
        }
    }

    // DISC DUR
    if (isset($_POST['add_discdur'])) {
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomTipus = $_POST['nomTipus'] ?? null;
        if ($capacitat && $preu && $nomFase && $nomTipus) {
            $discdurManager->addDiscdur($capacitat, $preu, $nomFase, $nomTipus);
        }
    } elseif (isset($_POST['delete_discdur'])) {
        $idDISC = $_POST['idDISC'] ?? null;
        if ($idDISC) {
            $discdurManager->deleteDiscdur($idDISC);
        }
    } elseif (isset($_POST['update_discdur'])) {
        // Implementar actualització de Disc Dur
        $idDISC = $_POST['old_idDISC'] ?? null;
        $capacitat = $_POST['capacitat'] ?? null;
        $preu = $_POST['preu'] ?? null;
        $nomFase = $_POST['nomFase'] ?? null;
        $nomTipus = $_POST['nomTipus'] ?? null;
        if ($idDISC && $capacitat && $preu && $nomFase && $nomTipus) {
            $discdurManager->updateDiscdur($idDISC, $capacitat, $preu, $nomFase, $nomTipus);
        }
    }

    // TIPUS
    if (isset($_POST['add_tipus'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $tipusManager->addTipus($nom);
        }
    } elseif (isset($_POST['delete_tipus'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $tipusManager->deleteTipus($nom);
        }
    } elseif (isset($_POST['update_tipus'])) {
        // Implementar actualització de Tipus
        $oldNom = $_POST['old_nom'] ?? null;
        $newNom = $_POST['new_nom'] ?? null;
        if ($oldNom && $newNom) {
            $tipusManager->updateTipus($oldNom, $newNom);
        }
    }

    // SO
    if (isset($_POST['add_so'])) {
        $nom = $_POST['nom'] ?? null;
        $versio = $_POST['versio'] ?? null;
        if ($nom && $versio) {
            $soManager->addSO($nom, $versio);
        }
    } elseif (isset($_POST['delete_so'])) {
        $idSO = $_POST['idSO'] ?? null;
        if ($idSO) {
            $soManager->deleteSO($idSO);
        }
    } elseif (isset($_POST['update_so'])) {
        // Implementar actualització de SO
        $idSO = $_POST['old_idSO'] ?? null;
        $nom = $_POST['nom'] ?? null;
        $versio = $_POST['versio'] ?? null;
        if ($idSO && $nom && $versio) {
            $soManager->updateSO($idSO, $nom, $versio);
        }
    }

    // MODEL
    if (isset($_POST['add_model'])) {
        $nom = $_POST['nom'] ?? null;
        $nomMarca = $_POST['nomMarca'] ?? null;
        if ($nom && $nomMarca) {
            $modelManager->addModel($nom, $nomMarca);
        }
    } elseif (isset($_POST['delete_model'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $modelManager->deleteModel($nom);
        }
    } elseif (isset($_POST['update_model'])) {
        // Implementar actualització de MODEL
        $idModel = $_POST['old_nom'] ?? null;
        $nom = $_POST['new_nom'] ?? null;
        $nomMarca = $_POST['nomMarca'] ?? null;
        if ($idModel && $nom && $nomMarca) {
            $modelManager->updateModel($idModel, $nom, $nomMarca);
        }
    }

    // MARCA
    if (isset($_POST['add_marca'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $marcaManager->addMarca($nom);
        }
    } elseif (isset($_POST['delete_marca'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $marcaManager->deleteMarca($nom);
        }
    } elseif (isset($_POST['update_marca'])) {
        $newNom = $_POST['new_nom'] ?? null;
        $oldNom = $_POST['old_nom'] ?? null;
        if ($oldNom && $newNom) {
            $marcaManager->updateMarca($oldNom, $newNom);
        }
    }

    // VRAM
    if (isset($_POST['add_vram'])) {
        $capacitat = $_POST['capacitat'] ?? null;
        $generacio = $_POST['generacio'] ?? null;
        if ($capacitat && $generacio) {
            $vramManager->addVRAM($capacitat, $generacio);
        }
    } elseif (isset($_POST['delete_vram'])) {
        $idVRAM = $_POST['idVRAM'] ?? null;
        if ($idVRAM) {
            $vramManager->deleteVRAM($idVRAM);
        }
    } elseif (isset($_POST['update_vram'])) {
        // Implementar actualització de VRAM
        $idVRAM = $_POST['old_idVRAM'] ?? null;
        $capacitat = $_POST['capacitat'] ?? null;
        $generacio = $_POST['generacio'] ?? null;
        if ($idVRAM && $capacitat && $generacio) {
            $vramManager->updateVRAM($idVRAM, $capacitat, $generacio);
        }
    }

    // GENERACIO
    if (isset($_POST['add_generacio'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $generacioManager->addGeneracio($nom);
        }
    } elseif (isset($_POST['delete_generacio'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $generacioManager->deleteGeneracio($nom);
        }
    } elseif (isset($_POST['update_generacio'])) {
        // Implementar actualització de GENERACIO
        $idGeneracio = $_POST['old_nom'] ?? null;
        $nom = $_POST['new_nom'] ?? null;
        if ($idGeneracio && $nom) {
            $generacioManager->updateGeneracio($idGeneracio, $nom);
        }
    }

    // FASE
    if (isset($_POST['add_fase'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $faseManager->addFase($nom);
        }
    } elseif (isset($_POST['delete_fase'])) {
        $nom = $_POST['nom'] ?? null;
        if ($nom) {
            $faseManager->deleteFase($nom);
        }
    } elseif (isset($_POST['update_fase'])) {
        // Implementar actualització de FASE
        $idFase = $_POST['old_nom'] ?? null;
        $nom = $_POST['new_nom'] ?? null;
        if ($idFase && $nom) {
            $faseManager->updateFase($idFase, $nom);
        }
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
    <title>Gestió de Components</title>
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

    <!-- Encabezado -->
    <nav class="navbar navbar-dark" style="background-color: #343a40;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">TotCloud</span>
            <!-- Botón de menú -->
            <a href="personal.html" class="btn btn-primary">MENU</a>
        </div>
    </nav>

    <div class="d-flex">
        <!-- Barra lateral -->
        <nav class="sidebar">
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'cpu' ? 'active' : '' ?>"
                onclick="location.href='?section=cpu'">CPU</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'ram' ? 'active' : '' ?>"
                onclick="location.href='?section=ram'">RAM</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'gpu' ? 'active' : '' ?>"
                onclick="location.href='?section=gpu'">GPU</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'discdur' ? 'active' : '' ?>"
                onclick="location.href='?section=discdur'">Disc Dur</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'tipus' ? 'active' : '' ?>"
                onclick="location.href='?section=tipus'">Tipus</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'so' ? 'active' : '' ?>"
                onclick="location.href='?section=so'">S.O</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'model' ? 'active' : '' ?>"
                onclick="location.href='?section=model'">Model</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'marca' ? 'active' : '' ?>"
                onclick="location.href='?section=marca'">Marca</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'vram' ? 'active' : '' ?>"
                onclick="location.href='?section=vram'">VRAM</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'generacio' ? 'active' : '' ?>"
                onclick="location.href='?section=generacio'">Generació</button>
            <button class="menu-button <?= isset($_GET['section']) && $_GET['section'] === 'fase' ? 'active' : '' ?>"
                onclick="location.href='?section=fase'">Fase</button>
        </nav>

        <!-- Contingut -->
        <div class="content">
            <?php
            // Mostrar la secció corresponent
            $section = $_GET['section'] ?? 'cpu';
            switch ($section) {
                case 'cpu':
                    echo $cpuManager->getHTML();
                    break;
                case 'ram':
                    echo $ramManager->getHTML();
                    break;
                case 'gpu':
                    echo $gpuManager->getHTML();
                    break;
                case 'discdur':
                    echo $discdurManager->getHTML();
                    break;
                case 'tipus':
                    echo $tipusManager->getHTML();
                    break;
                case 'so':
                    echo $soManager->getHTML();
                    break;
                case 'model':
                    echo $modelManager->getHTML();
                    break;
                case 'marca':
                    echo $marcaManager->getHTML();
                    break;
                case 'vram':
                    echo $vramManager->getHTML();
                    break;
                case 'generacio':
                    echo $generacioManager->getHTML();
                    break;
                case 'fase':
                    echo $faseManager->getHTML();
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
