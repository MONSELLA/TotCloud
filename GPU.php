<?php
// GPU.php
// Classe per gestionar les GPU amb paginació i cerca

class GPU {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Afegir una nova GPU
    public function addGPU($nuclis, $preu, $idVRAM, $nomFase, $nomModel) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("INSERT INTO GPU (nuclis, preu, idVRAM, nomFase, nomModel, idMaquina) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idsssi", $nuclis, $preu, $idVRAM, $nomFase, $nomModel, $idMaquina);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar una GPU
    public function deleteGPU($idGPU) {
        $stmt = $this->conn->prepare("DELETE FROM GPU WHERE idGPU = ?");
        $stmt->bind_param("i", $idGPU);
        $stmt->execute();
        $stmt->close();
    }

    // Actualitzar una GPU
    public function updateGPU($old_idGPU, $nuclis, $preu, $idVRAM, $nomFase, $nomModel) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("UPDATE GPU SET nuclis = ?, preu = ?, idVRAM = ?, nomFase = ?, nomModel = ?, idMaquina = ? WHERE idGPU = ?");
        $stmt->bind_param("idsssii", $nuclis, $preu, $idVRAM, $nomFase, $nomModel, $idMaquina, $old_idGPU);
        $stmt->execute();
        $stmt->close();
    }

    // Obtenir el codi HTML per mostrar les GPU amb paginació i cerca
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
            FROM GPU 
            WHERE 
                idGPU LIKE ? OR 
                nuclis LIKE ? OR 
                preu LIKE ? OR 
                idVRAM LIKE ? OR 
                nomFase LIKE ? OR 
                nomModel LIKE ? 
            LIMIT ?, ?
        ");

        // Assignació correcta de tipus: els primers sis són strings (LIKE) i els dos últims són enters (LIMIT).
        $stmt->bind_param(
            "ssssssii", 
            $search_param, // idGPU
            $search_param, // nuclis
            $search_param, // preu
            $search_param, // idVRAM
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

        $html = '<h2>GPU</h2>';

        // Formulari per afegir una nova GPU
        $html .= '
            <form method="post">
                <div class="mb-3">
                    <input type="number" class="form-control" name="nuclis" placeholder="Nuclis" required>
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" class="form-control" name="preu" placeholder="Preu (€)" required>
                </div>
                <div class="mb-3">
                    <input type="number" class="form-control" name="idVRAM" placeholder="ID VRAM" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomFase" placeholder="Fase" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomModel" placeholder="Model" required>
                </div>
                <button type="submit" name="add_gpu" class="btn btn-success w-100">Afegir GPU</button>
            </form>

            <br><br>

            <!-- Formulari per cercar GPU -->
            <form method="get" class="mb-3">
                <input type="hidden" name="section" value="gpu">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cerca GPU" value="'.htmlspecialchars($search).'">
                            <button type="submit" class="btn btn-primary">Cercar</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Taula de GPU -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nuclis</th>
                        <th>Preu (€)</th>
                        <th>ID VRAM</th>
                        <th>Fase</th>
                        <th>Model</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            $idGPUEsc = htmlspecialchars($row['idGPU'], ENT_QUOTES);
            $nuclisEsc = htmlspecialchars($row['nuclis'], ENT_QUOTES);
            $preuEsc = htmlspecialchars($row['preu'], ENT_QUOTES);
            $idVRAMEsc = htmlspecialchars($row['idVRAM'], ENT_QUOTES);
            $nomFaseEsc = htmlspecialchars($row['nomFase'], ENT_QUOTES);
            $nomModelEsc = htmlspecialchars($row['nomModel'], ENT_QUOTES);

            $html .= "<tr>
                        <td>{$idGPUEsc}</td>
                        <td>{$nuclisEsc}</td>
                        <td>{$preuEsc} €</td>
                        <td>{$idVRAMEsc}</td>
                        <td>{$nomFaseEsc}</td>
                        <td>{$nomModelEsc}</td>
                        <td>
                            <form method='post' style='display:inline-block;'>
                                <input type='hidden' name='idGPU' value='{$idGPUEsc}'>
                                <button type='submit' name='delete_gpu' class='btn btn-danger'>Eliminar</button>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='mostrarFormularioActualizar(\"{$idGPUEsc}\", \"{$nuclisEsc}\", \"{$preuEsc}\", \"{$idVRAMEsc}\", \"{$nomFaseEsc}\", \"{$nomModelEsc}\")'>Actualitzar</button>
                        </td>
                    </tr>";
        }
        $html .= '</tbody></table>';

        // Formulari per actualitzar una GPU (apareix sota de la taula)
        $html .= '
            <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
                <form method="post">
                    <input type="hidden" name="old_idGPU" id="old_idGPU">
                    <div class="mb-3">
                        <label for="nuclis" class="form-label" style="font-weight: bold;">Nuclis:</label>
                        <input type="number" class="form-control" name="nuclis" id="nuclis" required>
                    </div>
                    <div class="mb-3">
                        <label for="preu" class="form-label" style="font-weight: bold;">Preu (€):</label>
                        <input type="number" step="0.01" class="form-control" name="preu" id="preu" required>
                    </div>
                    <div class="mb-3">
                        <label for="idVRAM" class="form-label" style="font-weight: bold;">ID VRAM:</label>
                        <input type="number" class="form-control" name="idVRAM" id="idVRAM" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomFase" class="form-label" style="font-weight: bold;">Fase:</label>
                        <input type="text" class="form-control" name="nomFase" id="nomFase" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomModel" class="form-label" style="font-weight: bold;">Model:</label>
                        <input type="text" class="form-control" name="nomModel" id="nomModel" required>
                    </div>
                    <button type="submit" name="update_gpu" class="btn btn-success">Guardar Canvis</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
                </form>
                <br>
            </div>
        ';

        // Funcions JavaScript per mostrar i amagar el formulari
        $html .= '
            <script>
                function mostrarFormularioActualizar(idGPU, nuclis, preu, idVRAM, nomFase, nomModel) {
                    // Assignar els valors als camps del formulari
                    document.getElementById("old_idGPU").value = idGPU;
                    document.getElementById("nuclis").value = nuclis;
                    document.getElementById("preu").value = preu;
                    document.getElementById("idVRAM").value = idVRAM;
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
                $html .= '<li class="page-item"><a class="page-link" href="?section=gpu&search='.urlencode($search).'&page='.$prev_page.'">Anterior</a></li>';
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
                    $html .= '<li class="page-item"><a class="page-link" href="?section=gpu&search='.urlencode($search).'&page='.$i.'">'.$i.'</a></li>';
                }
            }

            if ($end_page < $total_pages) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Botó "Següent"
            if ($page < $total_pages) {
                $next_page = $page + 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=gpu&search='.urlencode($search).'&page='.$next_page.'">Següent</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Següent</span></li>';
            }

            $html .= '</ul>';

            // Camp per anar a una pàgina específica
            $html .= '
                <form method="get" class="d-flex align-items-center mt-2">
                    <input type="hidden" name="section" value="gpu">
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
