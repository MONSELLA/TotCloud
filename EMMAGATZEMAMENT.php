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

    public function updateEmmagatzemament($idEmmagatzemament, $capacitat, $preu)
    {
        $idEmmagatzemament = $this->conn->real_escape_string($idEmmagatzemament);
        $capacitat = $this->conn->real_escape_string($capacitat);
        $preu = $this->conn->real_escape_string($preu);
        $query = "UPDATE EMMAGATZEMAMENT SET capacitat = '$capacitat', preu = '$preu' WHERE idEmmagatzemament = '$idEmmagatzemament'";
        return $this->conn->query($query);
    }

    public function getHTML()
    {
        // Paginació
        $limit = 6;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Cerca de producets
        $search = isset($_GET['search']) ? $this->conn->real_escape_string($_GET['search']) : '';
        $searchQuery = $search ? "WHERE capacitat LIKE '%$search%' OR preu LIKE '%$search%'" : '';

        // Registres totals
        $totalQuery = "SELECT COUNT(*) AS total FROM EMMAGATZEMAMENT $searchQuery";
        $totalResult = $this->conn->query($totalQuery);
        $totalRecords = $totalResult->fetch_assoc()['total'];
        $totalPages = ceil($totalRecords / $limit);

        // Registres actuals
        $query = "SELECT * FROM EMMAGATZEMAMENT $searchQuery LIMIT $limit OFFSET $offset";
        $result = $this->conn->query($query);

        ob_start(); ?>
        <!-- Formulari de cerca -->
        <form method="GET" class="mb-4">
            <input type="hidden" name="section" value="emmagatzemament">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cercar per capacitat o preu..."
                    value="<?= htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Cercar</button>
                <a href="?section=emmagatzemament" class="btn btn-danger" title="Limpiar búsqueda">✖</a>
            </div>
        </form>


        <!-- Formulario d'agreagció-->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="capacitat" class="form-label">Capacitat:</label>
                <input type="number" step="0.01" name="capacitat" id="capacitat" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="preu" class="form-label">Preu:</label>
                <input type="number" step="0.01" name="preu" id="preu" class="form-control" required>
            </div>
            <button type="submit" name="add_emmagatzemament" class="btn btn-success w-100">Afegir Emmagatzemament</button>
        </form>

        <!-- Taula de registres -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Capacitat</th>
                    <th>Preu</th>
                    <th>Accions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['idEmmagatzemament']); ?></td>
                            <td><?= htmlspecialchars($row['capacitat']); ?></td>
                            <td><?= htmlspecialchars($row['preu']); ?></td>
                            <td>
                                <!-- Botó eliminar -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="idEmmagatzemament" value="<?= $row['idEmmagatzemament']; ?>">
                                    <button type="submit" name="delete_emmagatzemament" class="btn btn-danger">Eliminar</button>
                                </form>

                                <!-- Botón actualitzar -->
                                <button type="button" class="btn btn-primary"
                                    onclick="mostrarFormularioActualizar(<?= $row['idEmmagatzemament']; ?>, '<?= htmlspecialchars($row['capacitat']); ?>', '<?= htmlspecialchars($row['preu']); ?>')">Actualizar</button>
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

        <!-- Paginació -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                        <a class="page-link"
                            href="?section=emmagatzemament&page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <!-- Formulario per Actualitzar -->
        <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
            <form method="POST">
                <input type="hidden" name="idEmmagatzemament" id="idEmmagatzemament-actualizar">
                <div class="mb-3">
                    <label for="capacitat-actualizar" class="form-label">Capacitat:</label>
                    <input type="number" step="0.01" name="capacitat" id="capacitat-actualizar" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="preu-actualizar" class="form-label">Preu:</label>
                    <input type="number" step="0.01" name="preu" id="preu-actualizar" class="form-control" required>
                </div>
                <button type="submit" name="update_emmagatzemament" class="btn btn-success">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancelar</button>
            </form>
        </div>

        <script>
            function mostrarFormularioActualizar(idEmmagatzemament, capacitat, preu) {
                document.getElementById('idEmmagatzemament-actualizar').value = idEmmagatzemament;
                document.getElementById('capacitat-actualizar').value = capacitat;
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