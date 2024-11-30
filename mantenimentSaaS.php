<?php
// Incluir el archivo de conexión y las clases necesarias
include "connection.php";
include "SGBD.php";
include "CONFIGURACIO.php";
include "RAMVIRTUAL.php";
include "CPUVIRTUAL.php";
include "EMMAGATZEMAMENT.php";

// Crear instancias de las clases
$sgbdManager = new SGBD($conn);
$configManager = new CONFIGURACIO($conn);
$ramvManager = new RAMVIRTUAL($conn);
$cpuvManager = new CPUVIRTUAL($conn); // Nueva clase integrada
$emmManager = new EMMAGATZEMAMENT($conn); // Nueva clase integrada

// Manejar solicitudes GET para cargar contenido dinámico
if (isset($_GET['section'])) {
    if ($_GET['section'] === 'sgbd') {
        echo $sgbdManager->getHTML(); // Generar HTML dinámico para SGBD
    } elseif ($_GET['section'] === 'configuracio') {
        echo $configManager->getHTML(); // Generar HTML dinámico para Configuració
    } elseif ($_GET['section'] === 'ram') {
        echo $ramvManager->getHTML(); // Generar HTML dinámico para RAM Virtual
    } elseif ($_GET['section'] === 'cpu') {
        echo $cpuvManager->getHTML(); // Generar HTML dinámico para CPU Virtual
    } elseif ($_GET['section'] === 'emmagatzematge') {
        echo $emmManager->getHTML(); // Generar HTML dinámico para Emmagatzematge
    } else {
        echo "<h2 class='text-danger'>Secció desconeguda.</h2>";
    }
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
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
        }

        .menu-button {
            padding: 1rem;
            border: none;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .menu-button:hover {
            background-color: #e2e6ea;
        }

        .menu-button.active {
            background-color: #cfe2ff;
            font-weight: bold;
        }

        .content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <!-- Menú lateral -->
    <nav class="sidebar">
        <button class="menu-button" onclick="loadContent('sgbd', this)">SGBD</button>
        <button class="menu-button" onclick="loadContent('configuracio', this)">Configuració</button>
        <button class="menu-button" onclick="loadContent('ram', this)">RAM</button>
        <button class="menu-button" onclick="loadContent('cpu', this)">CPU</button>
        <button class="menu-button" onclick="loadContent('emmagatzematge', this)">Emmagatzematge</button>
    </nav>

    <!-- Contenido principal -->
    <div class="content" id="main-content">
        <!-- Este contenido se reemplazará dinámicamente -->
    </div>

    <script>
        // Función para cargar dinámicamente el contenido
        function loadContent(section, button) {
            const content = document.getElementById('main-content');
            const buttons = document.querySelectorAll('.menu-button');

            // Limpiar el estado activo de los botones
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active'); // Marcar el botón seleccionado

            // Eliminar el contenido existente
            content.innerHTML = ''; // Limpiar completamente el contenedor

            // Realizar la solicitud AJAX
            fetch(`mantenimentSaaS.php?section=${section}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error en la solicitud: " + response.statusText);
                    }
                    return response.text();
                })
                .then(data => {
                    content.innerHTML = data; // Insertar el contenido dinámico
                })
                .catch(error => {
                    console.error("Error cargando contenido:", error);
                    content.innerHTML = `<h2 class="text-danger">Error carregant la secció.</h2>`;
                });
        }
    </script>
</body>

</html>