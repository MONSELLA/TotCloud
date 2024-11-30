<?php
class CPUVIRTUAL
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addCPUV($velocitatRellotge, $preu)
    {
        $velocitatRellotge = $this->conn->real_escape_string($velocitatRellotge);
        $preu = $this->conn->real_escape_string($preu);
        $query = "INSERT INTO CPU_VIRTUAL (velocitatRellotge, preu) VALUES ('$velocitatRellotge', '$preu')";
        return $this->conn->query($query);
    }

    public function deleteCPUV($idCPUV)
    {
        $idCPUV = $this->conn->real_escape_string($idCPUV);
        $query = "DELETE FROM CPU_VIRTUAL WHERE idCPUV = '$idCPUV'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $cpuv_result = $this->conn->query("SELECT * FROM CPU_VIRTUAL");

        ob_start();
        ?>
        <form method="POST" class="mb-4 border p-4 bg-white shadow-sm rounded">
            <h4 class="text-primary">Afegir nova CPU Virtual</h4>
            <div class="mb-3">
                <label for="velocitatRellotge" class="form-label">Velocitat de Rellotge (GHz):</label>
                <input type="number" step="0.1" name="velocitatRellotge" id="velocitatRellotge" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu (€):</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_cpuv" class="btn btn-success w-100">Afegir CPU Virtual</button>
        </form>

        <h4 class="text-primary">Registres de CPU Virtual</h4>
        <table class="table table-striped bg-white shadow-sm rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Velocitat de Rellotge</th>
                    <th>Preu</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cpuv = $cpuv_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $cpuv['idCPUV'] ?></td>
                        <td><?= htmlspecialchars($cpuv['velocitatRellotge']) ?> GHz</td>
                        <td><?= htmlspecialchars($cpuv['preu']) ?> €</td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idCPUV" value="<?= $cpuv['idCPUV'] ?>">
                                <button type="submit" name="delete_cpuv" class="btn btn-danger btn-sm">Eliminar</button>
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