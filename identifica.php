<?php
session_start();

// Connexió a bd
include "connection.php";

// Verificar si ja hi ha una sessió iniciada
if (isset($_SESSION['user'])) {
    echo "Sessió ja iniciada per l'usuari: " . $_SESSION['user'];
    echo "<br>El teu ID és: " . $_SESSION['id'];  // Mostrar l'id guardat dins la sessió
} else {
    // Recollida de paràmetres
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $_POST['user'] ?? '';
        $pass = $_POST['pass'] ?? '';

        // Funció per verificar l'usuari en una taula específica
        function verificarUsuari($conn, $user, $pass, $taula)
        {

            // Determinar el camp id en funció de la taula
            if ($taula == 'client') {
                $idField = 'idClient';
            } elseif ($taula == 'personal') {
                $idField = 'idPersonal';
            }

            $query = "SELECT $idField, contrasenya FROM $taula WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['contrasenya'];

                // Verificar la contrasenya
                if (hash_equals($hashedPassword, crypt($pass, $hashedPassword))) {
                    return $row; // Retorna tota la fila (incloent l'id)
                }
            }
            return false;
        }

        // Comprovar a les dues taules
        $taules = ['client', 'personal'];
        $sessioIniciada = false;

        foreach ($taules as $taula) {
            $userData = verificarUsuari($conn, $user, $pass, $taula);
            if (verificarUsuari($conn, $user, $pass, $taula)) {
                $_SESSION['user'] = $user;
                // Guardar l'id dins la sessió segons la taula (client o personal)
                if ($taula == 'client') {
                    $_SESSION['id'] = $userData['idClient'];  // Per clients, usar 'idClient'
                } elseif ($taula == 'personal') {
                    $_SESSION['id'] = $userData['idPersonal'];  // Per personal, usar 'idPersonal'
                }
                
                echo "Sessió iniciada correctament!";
                $sessioIniciada = true;
                break;
            }
        }

        if (!$sessioIniciada) {
            echo "Error: Email o contrasenya incorrectes.";
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

    <?php
    // Mostrar missatge depenent del tipus d'usuari
    if (isset($taula) && $taula == 'client') {
        echo "<p>Hola, Client!</p>";
        echo "<br>El teu ID és: " . $_SESSION['id'];  // Mostrar l'id guardat dins la sessió
        // Redirecció per clients:
        // header("Location: client_dashboard.php");
    } elseif (isset($taula) && $taula == 'personal') {
        echo "<p>Hola, Personal!</p>";
        echo "<br>El teu ID és: " . $_SESSION['id'];  // Mostrar l'id guardat dins la sessió
        // Redirecció per personal:
        // header("Location: personal_dashboard.php");
    }
    ?>

    <form method="POST" action="logout.php">
        <center>
            <button type="submit">Tancar Sessió</button>
        </center>
    </form>
</body>

</html>