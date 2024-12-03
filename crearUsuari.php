<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $llinatge1 = $_POST['llinatge1'] ?? '';
    $llinatge2 = $_POST['llinatge2'] ?? '';
    $nick = $_POST['nick'] ?? '';
    $contrasenya = $_POST['contrasenya'] ?? '';
    $email = $_POST['email'] ?? '';

    // Verificar que els camps obligatoris no estiguin buits
    if (empty($nom) || empty($llinatge1) || empty($nick) || empty($contrasenya) || empty($email)) {
        echo "Error: Els camps 'Nom', 'Primer cognom', 'Nick', 'Contrasenya' i 'Email' no poden estar buits.";
        exit;
    }

    // Determinar la taula en funció del domini del correo electrònic
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain === "totcloud.com") {
        $table = 'personal';
    } else {
        $table = 'client';
    }

    // Inserir el nou usuari
    $salt = '$2y$10$' . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);
    $hashedPass = crypt($contrasenya, $salt);
    $insertQuery = "INSERT INTO $table (nom, llinatge1, llinatge2, nick, contrasenya, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $nom, $llinatge1, $llinatge2, $nick, $hashedPass, $email);

    if ($stmt->execute()) {
        echo "Usuari creat correctament a la taula $table. <a href='login.html'>Torna a l'inici</a>";
    } else {
        echo "Error al crear l'usuari: " . $conn->error;
    }
}