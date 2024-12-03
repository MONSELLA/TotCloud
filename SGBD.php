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

    public function updateSGBD($idSGBD, $nom, $versio, $idConfig)
    {
        $idSGBD = $this->conn->real_escape_string($idSGBD);
        $nom = $this->conn->real_escape_string($nom);
        $versio = $this->conn->real_escape_string($versio);
        $idConfig = $this->conn->real_escape_string($idConfig);
        $query = "UPDATE SGBD SET nom = '$nom', versio = '$versio', idConfig = '$idConfig' WHERE idSGBD = '$idSGBD'";
        return $this->conn->query($query);
    }

    public function getConfigurations()
    {
        $query = "SELECT * FROM CONFIGURACIO";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        $result = $this->conn->query("SELECT * FROM SGBD");
        $configurations = $this->getConfigurations();
        ob_start(); ?>
        <form method="POST" class="mb-4">
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
                    <?php while ($config = $configurations->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($config['idConfig']); ?>">
                            <?= htmlspecialchars($config['port']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="add_sgbd" class="btn btn-success w-100">Afegir SGBD</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Versió</th>
                    <th>ID Configuració</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idSGBD']); ?></td>
                        <td><?= htmlspecialchars($row['nom']); ?></td>
                        <td><?= htmlspecialchars($row['versio']); ?></td>
                        <td><?= htmlspecialchars($row['idConfig']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idSGBD" value="<?= $row['idSGBD']; ?>">
                                <button type="submit" name="delete_sgbd" class="btn btn-danger">Eliminar</button>
                            </form>
                            <button type="button" class="btn btn-primary"
                                onclick="mostrarFormularioActualizar(<?= $row['idSGBD']; ?>, '<?= htmlspecialchars($row['nom']); ?>', '<?= htmlspecialchars($row['versio']); ?>', <?= $row['idConfig']; ?>)">Actualizar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idSGBD" id="idSGBD-actualizar">
                <div class="mb-3">
                    <label for="nom-actualizar" class="form-label">Nom:</label>
                    <input type="text" name="nom" id="nom-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="versio-actualizar" class="form-label">Versió:</label>
                    <input type="text" name="versio" id="versio-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="idConfig-actualizar" class="form-label">Configuració:</label>
                    <select name="idConfig" id="idConfig-actualizar" class="form-select" required>
                        <option value="" disabled>Selecciona una configuració</option>
                        <?php
                        $configurations = $this->getConfigurations();
                        while ($config = $configurations->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($config['idConfig']); ?>">
                                <?= htmlspecialchars($config['port']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="update_sgbd" class="btn btn-success">Guardar canvis</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idSGBD, nom, versio, idConfig) {
                document.getElementById('idSGBD-actualizar').value = idSGBD;
                document.getElementById('nom-actualizar').value = nom;
                document.getElementById('versio-actualizar').value = versio;
                document.getElementById('idConfig-actualizar').value = idConfig;
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