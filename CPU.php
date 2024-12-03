<?php
// CPU.php
// Classe per gestionar les CPU amb paginació i cerca

class CPU {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Afegir una nova CPU
    public function addCPU($velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("INSERT INTO CPU (velocitatRellotge, nuclis, preu, nomFase, nomModel, idMaquina) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("didssi", $velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel, $idMaquina);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar una CPU
    public function deleteCPU($idCpu) {
        $stmt = $this->conn->prepare("DELETE FROM CPU WHERE idCpu = ?");
        $stmt->bind_param("i", $idCpu);
        $stmt->execute();
        $stmt->close();
    }

    // Actualitzar una CPU
    public function updateCPU($old_idCpu, $velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("UPDATE CPU SET velocitatRellotge = ?, nuclis = ?, preu = ?, nomFase = ?, nomModel = ?, idMaquina = ? WHERE idCpu = ?");
        $stmt->bind_param("didssii", $velocitatRellotge, $nuclis, $preu, $nomFase, $nomModel, $idMaquina, $old_idCpu);
        $stmt->execute();
        $stmt->close();
    }

    // Obtenir el codi HTML per mostrar les CPU amb paginació i cerca
    public function getHTML() {
        // Obtenir el número de pàgina actual
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Nombre de registres per pàgina
        $records_per_page = 5;
        $offset = ($page - 1) * $records_per_page;

        // Obtenir el terme de cerca
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        // Preparar la consulta amb cerca
        $search_param = "%" . $search . "%";
        $stmt = $this->conn->prepare("
            SELECT SQL_CALC_FOUND_ROWS * 
            FROM CPU 
            WHERE 
                idCpu LIKE ? OR
                velocitatRellotge LIKE ? OR 
                nuclis LIKE ? OR 
                preu LIKE ? OR 
                nomFase LIKE ? OR 
                nomModel LIKE ?
            LIMIT ?, ?
        ");

        // Corregir els tipus: tots els paràmetres `LIKE` són strings (s), però `LIMIT` necessita enters (i).
        $stmt->bind_param(
            "ssssssii", 
            $search_param, // idCpu
            $search_param, // velocitatRellotge
            $search_param, // nuclis
            $search_param, // preu
            $search_param, // nomFase
            $search_param, // nomModel
            $offset,       // OFFSET (enter)
            $records_per_page // LIMIT (enter)
        );

        $stmt->execute();
        $result = $stmt->get_result();


        // Obtenir el total de registres trobats
        $total_result = $this->conn->query("SELECT FOUND_ROWS() AS total");
        $total_row = $total_result->fetch_assoc();
        $total_records = $total_row['total'];

        // Calcular el nombre total de pàgines
        $total_pages = ceil($total_records / $records_per_page);

        $html = '<h2>CPU</h2>';

        // Formulari per afegir una nova CPU
        $html .= '
            <form method="post">
                <div class="mb-3">
                    <input type="number" step="0.01" class="form-control" name="velocitatRellotge" placeholder="Velocitat Rellotge (GHz)" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="nuclis" placeholder="Núclis" required>
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" class="form-control" name="preu" placeholder="Preu (€)" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomFase" placeholder="Fase" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomModel" placeholder="Model" required>
                </div>
                <button type="submit" name="add_cpu" class="btn btn-success w-100">Afegir CPU</button>
            </form>

            <br><br>

            <!-- Formulari per cercar CPU -->
            <form method="get" class="mb-3">
                <input type="hidden" name="section" value="cpu">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cerca CPU" value="'.htmlspecialchars($search).'">
                            <button type="submit" class="btn btn-primary">Cercar</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Taula de CPU -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Velocitat Rellotge</th>
                        <th>Núclis</th>
                        <th>Preu</th>
                        <th>Fase</th>
                        <th>Model</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            $idCpuEsc = htmlspecialchars($row['idCpu'], ENT_QUOTES);
            $velocitatRellotgeEsc = htmlspecialchars($row['velocitatRellotge'], ENT_QUOTES);
            $nuclisEsc = htmlspecialchars($row['nuclis'], ENT_QUOTES);
            $preuEsc = htmlspecialchars($row['preu'], ENT_QUOTES);
            $nomFaseEsc = htmlspecialchars($row['nomFase'], ENT_QUOTES);
            $nomModelEsc = htmlspecialchars($row['nomModel'], ENT_QUOTES);

            // Passar tots els paràmetres necessaris a la funció JavaScript
            $html .= "<tr>
                        <td>{$idCpuEsc}</td>
                        <td>{$velocitatRellotgeEsc} GHz</td>
                        <td>{$nuclisEsc}</td>
                        <td>{$preuEsc} €</td>
                        <td>{$nomFaseEsc}</td>
                        <td>{$nomModelEsc}</td>
                        <td>
                            <form method='post' style='display:inline-block;'>
                                <input type='hidden' name='idCpu' value='{$idCpuEsc}'>
                                <button type='submit' name='delete_cpu' class='btn btn-danger'>Eliminar</button>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='mostrarFormularioActualizar(\"{$idCpuEsc}\", \"{$velocitatRellotgeEsc}\", \"{$nuclisEsc}\", \"{$preuEsc}\", \"{$nomFaseEsc}\", \"{$nomModelEsc}\")'>Actualitzar</button>
                        </td>
                    </tr>";
        }
        $html .= '</tbody></table>';

        // Formulari per actualitzar una CPU (apareix sota de la taula)
        $html .= '
            <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
                <form method="post">
                    <input type="hidden" name="old_idCpu" id="old_idCpu">
                    <div class="mb-3">
                        <label for="velocitatRellotge" class="form-label" style="font-weight: bold;">Velocitat Rellotge (GHz):</label>
                        <input type="number" step="0.01" class="form-control" name="velocitatRellotge" id="velocitatRellotge" required>
                    </div>
                    <div class="mb-3">
                        <label for="nuclis" class="form-label" style="font-weight: bold;">Núclis:</label>
                        <input type="number" class="form-control" name="nuclis" id="nuclis" required>
                    </div>
                    <div class="mb-3">
                        <label for="preu" class="form-label" style="font-weight: bold;">Preu (€):</label>
                        <input type="number" step="0.01" class="form-control" name="preu" id="preu" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomFase" class="form-label" style="font-weight: bold;">Fase:</label>
                        <input type="text" class="form-control" name="nomFase" id="nomFase" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomModel" class="form-label" style="font-weight: bold;">Model:</label>
                        <input type="text" class="form-control" name="nomModel" id="nomModel" required>
                    </div>
                    <button type="submit" name="update_cpu" class="btn btn-success">Guardar Canvis</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
                </form>
                <br>
            </div>
        ';

        // Funcions JavaScript per mostrar i amagar el formulari
        $html .= '
            <script>
                function mostrarFormularioActualizar(idCpu, velocitatRellotge, nuclis, preu, nomFase, nomModel) {
                    // Assignar els valors als camps del formulari
                    document.getElementById("old_idCpu").value = idCpu;
                    document.getElementById("velocitatRellotge").value = velocitatRellotge;
                    document.getElementById("nuclis").value = nuclis;
                    document.getElementById("preu").value = preu;
                    document.getElementById("nomFase").value = nomFase;
                    document.getElementById("nomModel").value = nomModel;

                    // Mostrar el formulari
                    document.getElementById("formulario-actualizar").style.display = "block";
                }

                function cerrarFormulario() {
                    document.getElementById("formulario-actualizar").style.display = "none";
                }
            </script>
        ';

        // Navegació de pàgines
        if ($total_pages > 1) {
            $html .= '<nav aria-label="Page navigation">';
            $html .= '<ul class="pagination">';

            // Botó "Anterior"
            if ($page > 1) {
                $prev_page = $page - 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=cpu&search='.urlencode($search).'&page='.$prev_page.'">Anterior</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Anterior</span></li>';
            }

            // Números de pàgina (amb un màxim de 5 pàgines visibles)
            $max_visible_pages = 5;
            $start_page = max(1, $page - floor($max_visible_pages / 2));
            $end_page = min($total_pages, $start_page + $max_visible_pages - 1);

            if ($start_page > 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $page) {
                    $html .= '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
                } else {
                    $html .= '<li class="page-item"><a class="page-link" href="?section=cpu&search='.urlencode($search).'&page='.$i.'">'.$i.'</a></li>';
                }
            }

            if ($end_page < $total_pages) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Botó "Següent"
            if ($page < $total_pages) {
                $next_page = $page + 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=cpu&search='.urlencode($search).'&page='.$next_page.'">Següent</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Següent</span></li>';
            }

            $html .= '</ul>';

            // Camp per anar a una pàgina específica
            $html .= '
                <form method="get" class="d-flex align-items-center mt-2">
                    <input type="hidden" name="section" value="cpu">
                    <input type="hidden" name="search" value="'.htmlspecialchars($search).'">
                    <label for="go_to_page" class="form-label mb-0 mx-2">Anar a la pàgina:</label>
                    <input type="number" min="1" max="'.$total_pages.'" class="form-control" name="page" id="go_to_page" style="width:100px;">
                    <button type="submit" class="btn btn-primary mx-2">Anar</button>
                </form>
            ';

            $html .= '</nav>';
        }

        $stmt->close();

        return $html;
    }
}
?>
