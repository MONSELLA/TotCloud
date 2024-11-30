<?php
class RAMVIRTUAL
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addRAMVIRTUAL($capacitat, $preu)
    {
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "INSERT INTO RAM_VIRTUAL (capacitat, preu) VALUES ('$capacitat', '$preu')";
        return $this->conn->query($query);
    }

    public function deleteRAMVIRTUAL($idRAMV)
    {
        $idRAMV = $this->conn->real_escape_string($idRAMV);
        $query = "DELETE FROM RAM_VIRTUAL WHERE idRAMV = '$idRAMV'";
        return $this->conn->query($query);
    }

    public function updateRAMVIRTUAL($idRAMV, $capacitat, $preu)
    {
        $idRAMV = $this->conn->real_escape_string($idRAMV);
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "UPDATE RAM_VIRTUAL SET capacitat = '$capacitat', preu = '$preu' WHERE idRAMV = '$idRAMV'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $result = $this->conn->query("SELECT * FROM RAM_VIRTUAL");
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
            <button type="submit" name="add_ramvirtual" class="btn btn-success w-100">Afegir RAM Virtual</button>
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
                        <td><?= htmlspecialchars($row['idRAMV']); ?></td>
                        <td><?= htmlspecialchars($row['capacitat']); ?></td>
                        <td><?= htmlspecialchars($row['preu']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idRAMV" value="<?= $row['idRAMV']; ?>">
                                <button type="submit" name="delete_ramvirtual" class="btn btn-danger">Eliminar</button>
                            </form>
                            <button type="button" class="btn btn-primary"
                                onclick="mostrarFormularioActualizar(<?= $row['idRAMV']; ?>, '<?= htmlspecialchars($row['capacitat']); ?>', '<?= htmlspecialchars($row['preu']); ?>')">Actualizar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idRAMV" id="idRAMV-actualizar">
                <div class="mb-3">
                    <label for="capacitat-actualizar" class="form-label">Capacitat:</label>
                    <input type="number" step="0.01" name="capacitat" id="capacitat-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="preu-actualizar" class="form-label">Preu:</label>
                    <input type="number" step="0.01" name="preu" id="preu-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_ramvirtual" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idRAMV, capacitat, preu) {
                document.getElementById('idRAMV-actualizar').value = idRAMV;
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