<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUser = $_POST['new_user'] ?? '';
    $newPass = $_POST['new_pass'] ?? '';
    $isPersonal = isset($_POST['is_personal']) ? true : false;

    // Verificar que els camps no estiguin buits
    if (empty($newUser) || empty($newPass)) {
        echo "Error: El camp usuari i contrasenya no poden estar buits.";
        exit;
    }

    // Inserir el nou usuari
    $salt = '$2y$10$' . substr(str_replace('+', '.', base64_encode(random_bytes(16))), 0, 22);
    $hashedPass = crypt($newPass, $salt);
    $insertQuery = "INSERT INTO $table (nick, contrasenya) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ss", $newUser, $hashedPass);

    if ($stmt->execute()) {
        echo "Usuari creat correctament a la taula $table. <a href='/laliga'>Torna al inici</a>";
    } else {
        echo "Error al crear l'usuari: " . $conn->error;
    }
}
?>
