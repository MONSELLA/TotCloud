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
        // Paginación
        $limit = 6;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Búsqueda
        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';
        $searchQuery = $search ? "WHERE velocitatRellotge LIKE '%$search%' OR preu LIKE '%$search%'" : '';

        // Total de registros
        $totalQuery = "SELECT COUNT(*) AS total FROM CPU_VIRTUAL $searchQuery";
        $totalResult = $this->conn->query($totalQuery);
        $totalRecords = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Registros actuales
        $query = "SELECT * FROM CPU_VIRTUAL $searchQuery LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($query);

        ob_start(); ?>
        <!-- Formulario de búsqueda -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="section" value="cpuvirtual">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cercar per velocitat o preu..."
                    value="<?= htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cercar</button>
                <a href="?section=cpuvirtual" class="btn btn-danger" title="Limpiar búsqueda">✖</a>
            </div>
        </form>


        <!-- Formulario de agregar -->
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

        <!-- Tabla de registros -->
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['idCPUV']); ?></td>
                            <td><?= htmlspecialchars($row['velocitatRellotge']); ?></td>
                            <td><?= htmlspecialchars($row['preu']); ?></td>
                            <td>
                                <!-- Botón Eliminar -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="idCPUV" value="<?= $row['idCPUV']; ?>">
                                    <button type="submit" name="delete_cpuvirtual" class="btn btn-danger">Eliminar</button>
                                </form>

                                <!-- Botón Actualizar -->
                                <button type="button" class="btn btn-primary"
                                    onclick="mostrarFormularioActualizar(<?= $row['idCPUV']; ?>, '<?= htmlspecialchars($row['velocitatRellotge']); ?>', '<?= htmlspecialchars($row['preu']); ?>')">Actualizar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No s'han trobat resultats.</td>
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
                            href="?section=cpuvirtual&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Formulario para Actualizar -->
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