<?php
session_start();

// Connexió a bd
include "connection.php";

// Verificar si ja hi ha una sessió iniciada
if (isset($_SESSION['user'])) {
    echo "Sessió ja iniciada per l'usuari: " . $_SESSION['user'];
} else {
    // Recollida de paràmetres
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['user'] ?? '';
        $pass = $_POST['pass'] ?? '';

        // Funció per verificar l'usuari en una taula específica
        function verificarUsuari($conn, $user, $pass, $taula)
        {
            $query = "SELECT contrasenya FROM $taula WHERE nick = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['contrasenya'];

                // Verificar la contrasenya
                if (hash_equals($hashedPassword, crypt($pass, $hashedPassword))) {
                    return true;
                }
            }
            return false;
        }

        // Comprovar a les dues taules
        $taules = ['client', 'personal'];
        $sessioIniciada = false;

        foreach ($taules as $taula) {
            if (verificarUsuari($conn, $user, $pass, $taula)) {
                $_SESSION['user'] = $user;
                echo "Sessió iniciada correctament!";
                $sessioIniciada = true;
                break;
            }
        }

        if (!$sessioIniciada) {
            echo "Error: Usuari o contrasenya incorrectes.";
            exit;
        }
    } else {
        echo "Error: No s'han rebut dades.";
        exit;
    }
}
?>

<!-- Mostrar menú d'opcions si la sessió s'ha iniciat correctament -->
<html>

<body>
    <center><img src="images/cloud.png" width="75"></center>
</body>

</html>
