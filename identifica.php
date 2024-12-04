<?php
session_start();

// Connexió a la base de dades
include "connection.php";

// Funció per verificar l'usuari en una taula específica
function verificarUsuari($conn, $user, $pass, $taula)
{
    // Determinar el camp id en funció de la taula
    if ($taula == 'client') {
        $idField = 'idClient';
    } elseif ($taula == 'personal') {
        $idField = 'idPersonal';
    } else {
        return false;
    }

    // Preparar la consulta
    $query = "SELECT $idField, contrasenya FROM $taula WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        // Manejar error de preparació de la consulta
        return false;
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['contrasenya'];

        // Verificar la contrasenya
        if (password_verify($pass, $hashedPassword)) { // Recomanació: Utilitzar password_verify
            return $row; // Retorna tota la fila (incloent l'id)
        }
    }
    return false;
}

// Funció per redirigir l'usuari segons el tipus
function redirigirUsuari($taula) {
    if ($taula == 'client') {
        header("Location: client.html");
        exit();
    } elseif ($taula == 'personal') {
        header("Location: personal.html");
        exit();
    }
}

// Verificar si ja hi ha una sessió iniciada
if (isset($_SESSION['user'])) {
    // Utilitzar el tipus d'usuari emmagatzemat en la sessió
    if (isset($_SESSION['taula'])) {
        redirigirUsuari($_SESSION['taula']);
    } else {
        // Si per alguna raó no hi ha el tipus d'usuari, tancar la sessió per seguretat
        session_unset();
        session_destroy();
        echo "Sessió invàlida. Intenta iniciar sessió de nou.";
        exit();
    }
} else {
    // Recollida de paràmetres
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['user'] ?? '';
        $pass = $_POST['pass'] ?? '';

        // Comprovar a les dues taules
        $taules = ['client', 'personal'];
        $sessioIniciada = false;

        foreach ($taules as $taula) {
            $userData = verificarUsuari($conn, $user, $pass, $taula);
            if ($userData) { // Si s'ha trobat l'usuari
                $_SESSION['user'] = $user;
                $_SESSION['taula'] = $taula; // Emmagatzemar el tipus d'usuari
                // Guardar l'id dins la sessió segons la taula (client o personal)
                if ($taula == 'client') {
                    $_SESSION['id'] = $userData['idClient'];  // Per clients, usar 'idClient'
                } elseif ($taula == 'personal') {
                    $_SESSION['id'] = $userData['idPersonal'];  // Per personal, usar 'idPersonal'
                }
                $sessioIniciada = true;
                redirigirUsuari($taula); // Redirigir immediatament després de la login
                break;
            }
        }

        if (!$sessioIniciada) {
            echo "Email o contrasenya incorrectes.";
            exit();
        }
    } else {
        echo "Error: No s'han rebut dades.";
        exit();
    }
}
?>
