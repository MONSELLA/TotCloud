<?php
class EMMAGATZEMAMENT
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addEmmagatzemament($capacitat, $preu)
    {
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "INSERT INTO EMMAGATZEMAMENT (capacitat, preu) VALUES ('$capacitat', '$preu')";
        return $this->conn->query($query);
    }

    public function deleteEmmagatzemament($idEmmagatzemament)
    {
        $idEmmagatzemament = $this->conn->real_escape_string($idEmmagatzemament);
        $query = "DELETE FROM EMMAGATZEMAMENT WHERE idEmmagatzemament = '$idEmmagatzemament'";
        return $this->conn->query($query);
    }

    public function updateEmmagatzemament($idEmmagatzemament, $capacitat, $preu)
    {
        $idEmmagatzemament = $this->conn->real_escape_string($idEmmagatzemament);
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "UPDATE EMMAGATZEMAMENT SET capacitat = '$capacitat', preu = '$preu' WHERE idEmmagatzemament = '$idEmmagatzemament'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $result = $this->conn->query("SELECT * FROM EMMAGATZEMAMENT");
        ob_start(); ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="capacitat" class="form-label">Capacitat:</label>
                <input type="number" step="0.01" name="capacitat" id="capacitat" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu:</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_emmagatzemament" class="btn btn-success w-100">Afegir Emmagatzemament</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Capacitat</th>
                    <th>Preu</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idEmmagatzemament']); ?></td>
                        <td><?= htmlspecialchars($row['capacitat']); ?></td>
                        <td><?= htmlspecialchars($row['preu']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idEmmagatzemament" value="<?= $row['idEmmagatzemament']; ?>">
                                <button type="submit" name="delete_emmagatzemament" class="btn btn-danger">Eliminar</button>
                            </form>
                            <button type="button" class="btn btn-primary"
                                onclick="mostrarFormularioActualizar(<?= $row['idEmmagatzemament']; ?>, '<?= htmlspecialchars($row['capacitat']); ?>', '<?= htmlspecialchars($row['preu']); ?>')">Actualizar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idEmmagatzemament" id="idEmmagatzemament-actualizar">
                <div class="mb-3">
                    <label for="capacitat-actualizar" class="form-label">Capacitat:</label>
                    <input type="number" step="0.01" name="capacitat" id="capacitat-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="preu-actualizar" class="form-label">Preu:</label>
                    <input type="number" step="0.01" name="preu" id="preu-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_emmagatzemament" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idEmmagatzemament, capacitat, preu) {
                document.getElementById('idEmmagatzemament-actualizar').value = idEmmagatzemament;
                document.getElementById('capacitat-actualizar').value = capacitat;
                document.getElementById('preu-actualizar').value = preu;
                document.getElementById('formulario-actualizar').style.display = 'block';
            }

            function cerrarFormulario() {
                document.getElementById('formulario-actualizar').style.display = 'none';
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>