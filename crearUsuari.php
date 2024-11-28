<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $llinatge1 = $_POST['llinatge1'] ?? '';
    $llinatge2 = $_POST['llinatge2'] ?? '';
    $nick = $_POST['nick'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';
    $email = $_POST['email'] ?? '';

    // Verificar que los campos obligatorios no estén vacíos
    if (empty($nom) || empty($llinatge1) || empty($nick) || empty($contrasenya) || empty($email)) {
        echo "Error: Los campos 'Nom', 'Primer cognom (llinatge1)', 'Nickname', 'Contrasenya' y 'Email' no pueden estar vacíos.";
        exit;
    }

    // Determinar la tabla en función del dominio del correo electrónico
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain === "totcloud.com") {
        $table = 'personal';
    } else {
        $table = 'client';
    }

    // Verificar si el usuario ya existe en la tabla correspondiente
    $checkQuery = "SELECT * FROM $table WHERE nick = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $nick);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: El usuario ya existe en la tabla $table. Elige otro nickname.";
        exit;
    }

    // Si el usuario no existe, insertar el nuevo usuario
    $salt = '$2y$10$' . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);
    $hashedPass = crypt($contrasenya, $salt);
    $insertQuery = "INSERT INTO $table (nom, llinatge1, llinatge2, nick, contrasenya, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $nom, $llinatge1, $llinatge2, $nick, $hashedPass, $email);

    if ($stmt->execute()) {
        echo "Usuario creado correctamente en la tabla $table. <a href='/laliga'>Volver al inicio</a>";
    } else {
        echo "Error al crear el usuario: " . $conn->error;
    }
}
