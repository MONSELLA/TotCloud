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

    public function getHTML()
    {
        $config_result = $this->conn->query("SELECT * FROM CONFIGURACIO");

        ob_start();
        ?>
        <h4 class="text-primary">Afegir una nova Configuració</h4>
        <form method="POST" class="mb-4 border p-4 bg-white shadow-sm rounded">
            <div class="mb-3">
                <label for="port" class="form-label">Port:</label>
                <input type="number" name="port" id="port" class="form-control" required>
            </div>
            <button type="submit" name="add_configuracio" class="btn btn-success w-100">Afegir Configuració</button>
        </form>

        <h4 class="text-primary">Registres de Configuració</h4>
        <table class="table table-striped bg-white shadow-sm rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Port</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($config = $config_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $config['idConfig'] ?></td>
                        <td><?= htmlspecialchars($config['port']) ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idConfig" value="<?= $config['idConfig'] ?>">
                                <button type="submit" name="delete_configuracio" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }
}
?>