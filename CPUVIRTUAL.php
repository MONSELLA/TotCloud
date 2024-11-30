<?php
class CPUVIRTUAL
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addCPUVIRTUAL($velocitatRellotge, $preu)
    {
        $velocitatRellotge = $this->conn->real_escape_string($velocitatRellotge);
        $preu = $this->conn->real_escape_string($preu);
        $query = "INSERT INTO CPU_VIRTUAL (velocitatRellotge, preu) VALUES ('$velocitatRellotge', '$preu')";
        return $this->conn->query($query);
    }

    public function deleteCPUVIRTUAL($idCPUV)
    {
        $idCPUV = $this->conn->real_escape_string($idCPUV);
        $query = "DELETE FROM CPU_VIRTUAL WHERE idCPUV = '$idCPUV'";
        return $this->conn->query($query);
    }

    public function updateCPUVIRTUAL($idCPUV, $velocitatRellotge, $preu)
    {
        $idCPUV = $this->conn->real_escape_string($idCPUV);
        $velocitatRellotge = $this->conn->real_escape_string($velocitatRellotge);
        $preu = $this->conn->real_escape_string($preu);
        $query = "UPDATE CPU_VIRTUAL SET velocitatRellotge = '$velocitatRellotge', preu = '$preu' WHERE idCPUV = '$idCPUV'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $result = $this->conn->query("SELECT * FROM CPU_VIRTUAL");
        ob_start(); ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="velocitatRellotge" class="form-label">Velocitat Rellotge:</label>
                <input type="number" step="0.01" name="velocitatRellotge" id="velocitatRellotge" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu:</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_cpuvirtual" class="btn btn-success w-100">Afegir CPU Virtual</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Velocitat Rellotge</th>
                    <th>Preu</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idCPUV']); ?></td>
                        <td><?= htmlspecialchars($row['velocitatRellotge']); ?></td>
                        <td><?= htmlspecialchars($row['preu']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idCPUV" value="<?= $row['idCPUV']; ?>">
                                <button type="submit" name="delete_cpuvirtual" class="btn btn-danger">Eliminar</button>
                            </form>
                            <button type="button" class="btn btn-primary"
                                onclick="mostrarFormularioActualizar(<?= $row['idCPUV']; ?>, '<?= htmlspecialchars($row['velocitatRellotge']); ?>', '<?= htmlspecialchars($row['preu']); ?>')">Actualizar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idCPUV" id="idCPUV-actualizar">
                <div class="mb-3">
                    <label for="velocitatRellotge-actualizar" class="form-label">Velocitat Rellotge:</label>
                    <input type="number" step="0.01" name="velocitatRellotge" id="velocitatRellotge-actualizar"
                        class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="preu-actualizar" class="form-label">Preu:</label>
                    <input type="number" step="0.01" name="preu" id="preu-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_cpuvirtual" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idCPUV, velocitatRellotge, preu) {
                document.getElementById('idCPUV-actualizar').value = idCPUV;
                document.getElementById('velocitatRellotge-actualizar').value = velocitatRellotge;
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