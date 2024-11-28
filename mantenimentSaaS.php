<?php
// Incluir el archivo de conexión
include "connection.php";

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Manejo de solicitudes GET y POST
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['table'])) {
    // Mostrar registros de la tabla seleccionada
    $table = $conn->real_escape_string($_GET['table']); // Proteger contra inyección SQL
    $query = "SELECT * FROM $table";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table><tr>";
        while ($field = $result->fetch_field()) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>"; // Escapar valores para evitar XSS
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay registros en la tabla $table.</p>";
    }
    exit(); // Evitar que se cargue el HTML si solo se solicita la tabla
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insertar nuevo registro en la tabla seleccionada
    if (isset($_POST['table'], $_POST['field1'], $_POST['field2'])) {
        $table = $conn->real_escape_string($_POST['table']);
        $field1 = $conn->real_escape_string($_POST['field1']);
        $field2 = $conn->real_escape_string($_POST['field2']);

        // Ajusta los campos de acuerdo con la estructura de tus tablas
        $query = "INSERT INTO $table (campo1, campo2) VALUES ('$field1', '$field2')";
        if ($conn->query($query)) {
            echo "<p>Registro añadido correctamente a la tabla $table.</p>";
        } else {
            echo "<p>Error al añadir el registro: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Recursos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="main-container">
        <div class="sidebar">
            <h2>Menú</h2>
            <button onclick="loadData('SGBD')">SGBD</button>
            <button onclick="loadData('CONFIGURACION')">Configuración</button>
            <button onclick="loadData('RAM')">RAM</button>
            <button onclick="loadData('CPU')">CPU</button>
            <button onclick="loadData('EMMAGATZEMAMENT')">Emmagatzemament</button>
        </div>
        <div class="content">
            <div id="form-container">
                <!-- Aquí se cargará el formulario -->
            </div>
            <div id="data-container">
                <!-- Aquí se mostrarán los registros -->
            </div>
        </div>
    </div>

    <script>
        function loadData(table) {
            fetch(`mantenimentSaaS.php?table=${table}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('data-container').innerHTML = data;
                    document.getElementById('form-container').innerHTML = `
                        <form class="record-form" method="POST" action="mantenimentSaaS.php">
                            <h3>Añadir Registro a ${table}</h3>
                            <label for="field1">Campo 1:</label>
                            <input type="text" name="field1" required>

                            <label for="field2">Campo 2:</label>
                            <input type="text" name="field2" required>

                            <input type="hidden" name="table" value="${table}">
                            <input type="submit" value="Guardar">
                        </form>
                    `;
                });
        }
    </script>
</body>

</html>