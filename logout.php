<?php
session_start();
session_unset(); // Eliminar totes les variables de sessió
session_destroy(); // Destruir la sessió

// Redirigir al login
header("Location: login.html");
exit;
?>
