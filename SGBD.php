<?php
class SGBD
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addSGBD($nom, $versio, $idConfig)
    {
        $nom = $this->conn->real_escape_string($nom);
        $versio = $this->conn->real_escape_string($versio);
        $idConfig = $this->conn->real_escape_string($idConfig);
        $query = "INSERT INTO SGBD (nom, versio, idConfig) VALUES ('$nom', '$versio', '$idConfig')";
        return $this->conn->query($query);
    }

    public function deleteSGBD($idSGBD)
    {
        $idSGBD = $this->conn->real_escape_string($idSGBD);
        $query = "DELETE FROM SGBD WHERE idSGBD = '$idSGBD'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $sgbd_result = $this->conn->query("SELECT * FROM SGBD");
        $config_result = $this->conn->query("SELECT * FROM CONFIGURACIO");

        ob_start();
        ?>
        <h4 class="text-primary">Afegir un nou SGBD</h4>
        <form method="POST" class="mb-4 border p-4 bg-white shadow-sm rounded">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" name="nom" id="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="versio" class="form-label">Versió:</label>
                <input type="text" name="versio" id="versio" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="idConfig" class="form-label">Configuració:</label>
                <select name="idConfig" id="idConfig" class="form-select" required>
                    <option value="" disabled selected>Selecciona una configuració</option>
                    <?php while ($config = $config_result->fetch_assoc()): ?>
                        <option value="<?= $config['idConfig'] ?>">
                            <?= htmlspecialchars($config['ssl']) ?> (Port: <?= $config['port'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="add_sgbd" class="btn btn-success w-100">Afegir SGBD</button>
        </form>

        <h4 class="text-primary">Registres de SGBD</h4>
        <table class="table table-striped bg-white shadow-sm rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Versió</th>
                    <th>Configuració</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sgbd = $sgbd_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sgbd['idSGBD'] ?></td>
                        <td><?= htmlspecialchars($sgbd['nom']) ?></td>
                        <td><?= htmlspecialchars($sgbd['versio']) ?></td>
                        <td><?= $sgbd['idConfig'] ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idSGBD" value="<?= $sgbd['idSGBD'] ?>">
                                <button type="submit" name="delete_sgbd" class="btn btn-danger btn-sm">Eliminar</button>
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