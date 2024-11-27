<?php
require_once('connexio.php');

$codpob = $_POST["codpob"];
$despob = $_POST["despob"];
$urlpob = $_POST["urlpob"];

$sql = "UPDATE poblacio SET despob = ?, urlpob = ? WHERE codpob = ?";

$stmt = $conn->prepare($sql);

// Asociar los parámetros a la sentencia SQL
$stmt->bind_param("sss", $despob, $urlpob, $codpob);  // "sss" indica que los tres parámetros son strings

// Ejecutar la sentencia
if ($stmt->execute()) {
    header("Location: poblacio.php");
    echo "Nuevo registro insertado correctamente.";
} else {
    echo "Error al insertar los datos: " . $stmt->error;
}

/////////////////codi ROBI/////
<?php
class Update
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function actualitzar_poblacio($codipobl, $descrpobl, $urlpobl)
    {
        $codiVell = $codipobl;
        $codiNou = null;

        if (strpos($codipobl, '/') !== false) {
            list($codiVell, $codiNou) = explode('/', $codipobl);
        }

        $consulta = "SELECT codpob FROM poblacio WHERE codpob = '$codiVell'";
        $resultat = $this->conn->query($consulta);


        if ($resultat === false) {
            die("Error en la consulta: " . $this->conn->error);
        }

        if ($resultat->num_rows > 0) {
            $updates = [];
            if ($codiNou !== null) {
                $updates[] = "codpob = '$codiNou'";
            } else {
                $updates[] = "codpob = '$codiVell'";
            }
            if ($descrpobl !== null) {
                $updates[] = "despob = '$descrpobl'";
            }
            if ($urlpobl !== null) {
                $updates[] = "urlpob = '$urlpobl'";
            }
        }

        if (!empty($updates)) {
            $actualizarSQL = "UPDATE poblacio SET " . implode(", ", $updates) . " WHERE codpob = '$codiVell'";
            if ($this->conn->query($actualizarSQL) === TRUE) {
                $message = "Registro actualizado correctamente.";
            } else {
                $error = "Error al actualizar: " . $this->conn->error;
            }
        }

    }

}


?>
