<?php
class CONFIGURACIO
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function addConfiguracio($certSsl, $port, $maxConnexions, $rvaConnexions)
    {
        $certSsl = $this->conn->real_escape_string($certSsl);
        $port = $this->conn->real_escape_string($port);
        $maxConnexions = $this->conn->real_escape_string($maxConnexions);
        $rvaConnexions = $this->conn->real_escape_string($rvaConnexions);

        $query = "INSERT INTO CONFIGURACIO (certSsl, port, maxConnexions, rvaConnexions) 
                  VALUES ('$certSsl', '$port', '$maxConnexions', '$rvaConnexions')";
        return $this->conn->query($query);
    }

    public function deleteConfiguracio($idConfig)
    {
        $idConfig = $this->conn->real_escape_string($idConfig);
        $query = "DELETE FROM CONFIGURACIO WHERE idConfig = '$idConfig'";
        return $this->conn->query($query);
    }

    public function updateConfiguracio($idConfig, $certSsl, $port, $maxConnexions, $rvaConnexions)
    {
        $idConfig = $this->conn->real_escape_string($idConfig);
        $certSsl = $this->conn->real_escape_string($certSsl);
        $port = $this->conn->real_escape_string($port);
        $maxConnexions = $this->conn->real_escape_string($maxConnexions);
        $rvaConnexions = $this->conn->real_escape_string($rvaConnexions);

        $query = "UPDATE CONFIGURACIO 
                  SET certSsl = '$certSsl', port = '$port', maxConnexions = '$maxConnexions', rvaConnexions = '$rvaConnexions' 
                  WHERE idConfig = '$idConfig'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        // Paginación y búsqueda
        $limit = 6;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';
        $searchQuery = $search ? "WHERE certSsl LIKE '%$search%' OR port LIKE '%$search%' OR maxConnexions LIKE '%$search%' OR rvaConnexions LIKE '%$search%'" : '';

        // Total de registros
        $totalQuery = "SELECT COUNT(*) AS total FROM CONFIGURACIO $searchQuery";
        $totalResult = $this->conn->query($totalQuery);
        $totalRecords = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Registros actuales
        $query = "SELECT * FROM CONFIGURACIO $searchQuery LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($query);

        ob_start(); ?>

        <!-- Formulario de búsqueda -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="section" value="configuracio">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cercar..."
                    value="<?= htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cercar</button>
                <a href="?section=configuracio" class="btn btn-danger" title="Limpiar búsqueda">✖</a>
            </div>
        </form>

        <!-- Formulario de agregar -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="certSsl" class="form-label">Certificat SSL:</label>
                <select name="certSsl" id="certSsl" class="form-select" required>
                    <option value="on">On</option>
                    <option value="off" selected>Off</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="port" class="form-label">Port:</label>
                <input type="number" name="port" id="port" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="maxConnexions" class="form-label">Connexions Máximes:</label>
                <input type="number" name="maxConnexions" id="maxConnexions" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="rvaConnexions" class="form-label">Revisió de Connexions:</label>
                <input type="number" name="rvaConnexions" id="rvaConnexions" class="form-control" required>
            </div>
            <button type="submit" name="add_configuracio" class="btn btn-success w-100">Afegir Configuració</button>
        </form>

        <!-- Tabla de registros -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Certificat SSL</th>
                    <th>Port</th>
                    <th>Connexions Máximes</th>
                    <th>Revisió de Connexions</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['idConfig']); ?></td>
                            <td><?= htmlspecialchars($row['certSsl']); ?></td>
                            <td><?= htmlspecialchars($row['port']); ?></td>
                            <td><?= htmlspecialchars($row['maxConnexions']); ?></td>
                            <td><?= htmlspecialchars($row['rvaConnexions']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="idConfig" value="<?= $row['idConfig']; ?>">
                                    <button type="submit" name="delete_configuracio" class="btn btn-danger">Eliminar</button>
                                </form>
                                <button type="button" class="btn btn-primary" onclick="mostrarFormularioActualizar(
                                        <?= $row['idConfig']; ?>, 
                                        '<?= htmlspecialchars($row['certSsl']); ?>', 
                                        '<?= htmlspecialchars($row['port']); ?>', 
                                        '<?= htmlspecialchars($row['maxConnexions']); ?>', 
                                        '<?= htmlspecialchars($row['rvaConnexions']); ?>'
                                    )">Actualizar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No s'han trobat resultats.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?section=configuracio&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Formulario para Actualizar -->
        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idConfig" id="idConfig-actualizar">
                <div class="mb-3">
                    <label for="certSsl-actualizar" class="form-label">Certificat SSL:</label>
                    <select name="certSsl" id="certSsl-actualizar" class="form-select" required>
                        <option value="on">On</option>
                        <option value="off">Off</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="port-actualizar" class="form-label">Port:</label>
                    <input type="number" name="port" id="port-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="maxConnexions-actualizar" class="form-label">Connexions Máximes:</label>
                    <input type="number" name="maxConnexions" id="maxConnexions-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="rvaConnexions-actualizar" class="form-label">Revisió de Connexions:</label>
                    <input type="number" name="rvaConnexions" id="rvaConnexions-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_configuracio" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idConfig, certSsl, port, maxConnexions, rvaConnexions) {
                document.getElementById('idConfig-actualizar').value = idConfig;
                document.getElementById('certSsl-actualizar').value = certSsl;
                document.getElementById('port-actualizar').value = port;
                document.getElementById('maxConnexions-actualizar').value = maxConnexions;
                document.getElementById('rvaConnexions-actualizar').value = rvaConnexions;
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