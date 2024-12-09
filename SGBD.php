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
        // Paginació
        $limit = 6;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Cerca
        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';
        $searchQuery = $search ? "WHERE nom LIKE '%$search%' OR versio LIKE '%$search%' OR idConfig LIKE '%$search%'" : '';

        // Registres totals
        $totalQuery = "SELECT COUNT(*) AS total FROM SGBD $searchQuery";
        $totalResult = $this->conn->query($totalQuery);
        $totalRecords = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Registres actuals
        $query = "SELECT * FROM SGBD $searchQuery LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($query);
        $configurations = $this->getConfigurations();

        ob_start(); ?>
        <!-- Formulari de cerca -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="section" value="sgbd">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cercar per nom, versio o configuració..."
                    value="<?= htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cercar</button>
                <a href="?section=sgbd" class="btn btn-danger" title="Limpiar búsqueda">✖</a>
            </div>
        </form>


        <!-- Formulari d'agregació -->
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

        <!-- Tabla de registres -->
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['idSGBD']); ?></td>
                            <td><?= htmlspecialchars($row['nom']); ?></td>
                            <td><?= htmlspecialchars($row['versio']); ?></td>
                            <td><?= htmlspecialchars($row['idConfig']); ?></td>
                            <td>
                                <!-- Botón Eliminar -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="idSGBD" value="<?= $row['idSGBD']; ?>">
                                    <button type="submit" name="delete_sgbd" class="btn btn-danger">Eliminar</button>
                                </form>

                                <!-- Botó actualitzar -->
                                <button type="button" class="btn btn-primary"
                                    onclick="mostrarFormularioActualizar(<?= $row['idSGBD']; ?>, '<?= htmlspecialchars($row['nom']); ?>', '<?= htmlspecialchars($row['versio']); ?>', <?= $row['idConfig']; ?>)">Actualizar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No s'han trobat resultats.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginació -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?section=sgbd&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Formulari per actualitzar -->
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
                <button type="submit" name="update_sgbd" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
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