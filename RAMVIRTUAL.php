<?php
class RAMVIRTUAL
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addRAMV($capacitat, $preu)
    {
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "INSERT INTO RAM_VIRTUAL (capacitat, preu) VALUES ('$capacitat', '$preu')";
        return $this->conn->query($query);
    }

    public function deleteRAMV($idRAMV)
    {
        $idRAMV = $this->conn->real_escape_string($idRAMV);
        $query = "DELETE FROM RAM_VIRTUAL WHERE idRAMV = '$idRAMV'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $ramv_result = $this->conn->query("SELECT * FROM RAM_VIRTUAL");

        ob_start();
        ?>
        <form method="POST" class="mb-4 border p-4 bg-white shadow-sm rounded">
            <h4 class="text-primary">Afegir nova RAM Virtual</h4>
            <div class="mb-3">
                <label for="capacitat" class="form-label">Capacitat (GB):</label>
                <input type="number" name="capacitat" id="capacitat" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu (€):</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_ramv" class="btn btn-success w-100">Afegir RAM Virtual</button>
        </form>

        <h4 class="text-primary">Registres de RAM Virtual</h4>
        <table class="table table-striped bg-white shadow-sm rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Capacitat</th>
                    <th>Preu</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($ramv = $ramv_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $ramv['idRAMV'] ?></td>
                        <td><?= htmlspecialchars($ramv['capacitat']) ?> GB</td>
                        <td><?= htmlspecialchars($ramv['preu']) ?> €</td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idRAMV" value="<?= $ramv['idRAMV'] ?>">
                                <button type="submit" name="delete_ramv" class="btn btn-danger btn-sm">Eliminar</button>
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