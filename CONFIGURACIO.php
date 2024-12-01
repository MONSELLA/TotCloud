<?php
class CONFIGURACIO
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addConfiguracio($port)
    {
        $port = $this->conn->real_escape_string($port);
        $query = "INSERT INTO CONFIGURACIO (port) VALUES ('$port')";
        return $this->conn->query($query);
    }

    public function deleteConfiguracio($idConfig)
    {
        $idConfig = $this->conn->real_escape_string($idConfig);
        $query = "DELETE FROM CONFIGURACIO WHERE idConfig = '$idConfig'";
        return $this->conn->query($query);
    }

    public function updateConfiguracio($idConfig, $port)
    {
        $idConfig = $this->conn->real_escape_string($idConfig);
        $port = $this->conn->real_escape_string($port);
        $query = "UPDATE CONFIGURACIO SET port = '$port' WHERE idConfig = '$idConfig'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $result = $this->conn->query("SELECT * FROM CONFIGURACIO");
        ob_start(); ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="port" class="form-label">Port:</label>
                <input type="number" name="port" id="port" class="form-control" required>
            </div>
            <button type="submit" name="add_configuracio" class="btn btn-success w-100">Afegir Configuració</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Port</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idConfig']); ?></td>
                        <td><?= htmlspecialchars($row['port']); ?></td>
                        <td>
                            <!-- Botó Eliminar -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idConfig" value="<?= $row['idConfig']; ?>">
                                <button type="submit" name="delete_configuracio" class="btn btn-danger">Eliminar</button>
                            </form>

                            <!-- Botó Actualizar -->
                            <button type="button" class="btn btn-primary"
                                onclick="mostrarFormularioActualizar(<?= $row['idConfig']; ?>, '<?= htmlspecialchars($row['port']); ?>')">Actualizar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Formulari par Actualitzar -->
        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idConfig" id="idConfig-actualizar">
                <div class="mb-3">
                    <label for="port-actualizar" class="form-label">Port:</label>
                    <input type="number" name="port" id="port-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_configuracio" class="btn btn-success">Guardar canvis</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idConfig, port) {
                document.getElementById('idConfig-actualizar').value = idConfig;
                document.getElementById('port-actualizar').value = port;
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