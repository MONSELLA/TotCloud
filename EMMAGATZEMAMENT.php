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

    public function getHTML()
    {
        $emm_result = $this->conn->query("SELECT * FROM EMMAGATZEMAMENT");

        ob_start();
        ?>
        <form method="POST" class="mb-4 border p-4 bg-white shadow-sm rounded">
            <h4 class="text-primary">Afegir Emmagatzematge</h4>
            <div class="mb-3">
                <label for="capacitat" class="form-label">Capacitat (GB):</label>
                <input type="number" name="capacitat" id="capacitat" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu (€):</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_emm" class="btn btn-success w-100">Afegir Emmagatzematge</button>
        </form>

        <h4 class="text-primary">Registres d'Emmagatzematge</h4>
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
                <?php while ($emm = $emm_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $emm['idEmmagatzemament'] ?></td>
                        <td><?= htmlspecialchars($emm['capacitat']) ?> GB</td>
                        <td><?= htmlspecialchars($emm['preu']) ?> €</td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idEmmagatzemament" value="<?= $emm['idEmmagatzemament'] ?>">
                                <button type="submit" name="delete_emm" class="btn btn-danger btn-sm">Eliminar</button>
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