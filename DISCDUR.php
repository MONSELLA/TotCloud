<?php
// DISCDUR.php
// Classe per gestionar els Discs Durs amb paginació i cerca

class DISCDUR {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Afegir nou Disc Dur
    public function addDiscdur($capacitat, $preu, $nomFase, $nomTipus) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("INSERT INTO DISC_DUR (capacitat, preu, nomFase, idMaquina, nomTipus) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ddsis", $capacitat, $preu, $nomFase, $idMaquina, $nomTipus);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar Disc Dur
    public function deleteDiscdur($idDISC) {
        $stmt = $this->conn->prepare("DELETE FROM DISC_DUR WHERE idDISC = ?");
        $stmt->bind_param("i", $idDISC);
        $stmt->execute();
        $stmt->close();
    }

    // Actualitzar Disc Dur
    public function updateDiscdur($old_idDISC, $capacitat, $preu, $nomFase, $nomTipus) {
        $idMaquina = null; // Assignar valor null per defecte
        $stmt = $this->conn->prepare("UPDATE DISC_DUR SET capacitat = ?, preu = ?, nomFase = ?, idMaquina = ?, nomTipus = ? WHERE idDISC = ?");
        $stmt->bind_param("ddsisi", $capacitat, $preu, $nomFase, $idMaquina, $nomTipus, $old_idDISC);
        $stmt->execute();
        $stmt->close();
    }

    // Obtenir el codi HTML per mostrar els Discs Durs amb paginació i cerca
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
            FROM DISC_DUR 
            WHERE 
                idDISC LIKE ? OR 
                capacitat LIKE ? OR 
                preu LIKE ? OR 
                nomFase LIKE ? OR 
                nomTipus LIKE ? 
            LIMIT ?, ?
        ");

        // Assignació correcta de tipus: els primers sis són strings (LIKE) i els dos últims són enters (LIMIT).
        $stmt->bind_param(
            "sssssii", 
            $search_param, // idDISC
            $search_param, // capacitat
            $search_param, // preu
            $search_param, // nomFase
            $search_param, // nomTipus
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

        $html = '<h2>Disc Dur</h2>';

        // Formulari per afegir un nou Disc Dur
        $html .= '
            <form method="post">
                <div class="mb-3">
                    <input type="number" step="0.01" class="form-control" name="capacitat" placeholder="Capacitat (GB)" required>
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" class="form-control" name="preu" placeholder="Preu (€)" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomFase" placeholder="Fase" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomTipus" placeholder="Tipus" required>
                </div>
                <button type="submit" name="add_discdur" class="btn btn-success w-100">Afegir Disc Dur</button>
            </form>

            <br><br>

            <!-- Formulari per cercar Discs Durs -->
            <form method="get" class="mb-3">
                <input type="hidden" name="section" value="discdur">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cerca Disc Dur" value="'.htmlspecialchars($search).'">
                            <button type="submit" class="btn btn-primary">Cercar</button>
                        </div>
                    </div>
                </div>
            </form>

<!-- Taula de Discs Durs -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Capacitat (GB)</th>
                        <th>Preu (€)</th>
                        <th>Fase</th>
                        <th>Tipus</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            $idDISCESc = htmlspecialchars($row['idDISC'], ENT_QUOTES);
            $capacitatEsc = htmlspecialchars($row['capacitat'], ENT_QUOTES);
            $preuEsc = htmlspecialchars($row['preu'], ENT_QUOTES);
            $nomFaseEsc = htmlspecialchars($row['nomFase'], ENT_QUOTES);
            $nomTipusEsc = htmlspecialchars($row['nomTipus'], ENT_QUOTES);

            $html .= "<tr>
                        <td>{$idDISCESc}</td>
                        <td>{$capacitatEsc} GB</td>
                        <td>{$preuEsc} €</td>
                        <td>{$nomFaseEsc}</td>
                        <td>{$nomTipusEsc}</td>
                        <td>
                            <form method='post' style='display:inline-block;'>
                                <input type='hidden' name='idDISC' value='{$idDISCESc}'>
                                <button type='submit' name='delete_discdur' class='btn btn-danger'>Eliminar</button>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='mostrarFormularioActualizar(\"{$idDISCESc}\", \"{$capacitatEsc}\", \"{$preuEsc}\", \"{$nomFaseEsc}\", \"{$nomTipusEsc}\")'>Actualitzar</button>
                        </td>
                    </tr>";
        }
        $html .= '</tbody></table>';

        // Formulari per actualitzar un Disc Dur (apareix sota de la taula)
        $html .= '
            <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
                <form method="post">
                    <input type="hidden" name="old_idDISC" id="old_idDISC">
                    <div class="mb-3">
                        <label for="capacitat" class="form-label" style="font-weight: bold;">Capacitat (GB):</label>
                        <input type="number" step="0.01" class="form-control" name="capacitat" id="capacitat" required>
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
                        <label for="nomTipus" class="form-label" style="font-weight: bold;">Tipus:</label>
                        <input type="text" class="form-control" name="nomTipus" id="nomTipus" required>
                    </div>
                    <button type="submit" name="update_discdur" class="btn btn-success">Guardar Canvis</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
                </form>
                <br>
            </div>
        ';

        // Funcions JavaScript per mostrar i amagar el formulari
        $html .= '
            <script>
                function mostrarFormularioActualizar(idDISC, capacitat, preu, nomFase, nomTipus) {
                    // Assignar els valors als camps del formulari
                    document.getElementById("old_idDISC").value = idDISC;
                    document.getElementById("capacitat").value = capacitat;
                    document.getElementById("preu").value = preu;
                    document.getElementById("nomFase").value = nomFase;
                    document.getElementById("nomTipus").value = nomTipus;

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
                $html .= '<li class="page-item"><a class="page-link" href="?section=discdur&search='.urlencode($search).'&page='.$prev_page.'">Anterior</a></li>';
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
                    $html .= '<li class="page-item"><a class="page-link" href="?section=discdur&search='.urlencode($search).'&page='.$i.'">'.$i.'</a></li>';
                }
            }

            if ($end_page < $total_pages) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Botó "Següent"
            if ($page < $total_pages) {
                $next_page = $page + 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=discdur&search='.urlencode($search).'&page='.$next_page.'">Següent</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Següent</span></li>';
            }

            $html .= '</ul>';

            // Camp per anar a una pàgina específica
            $html .= '
                <form method="get" class="d-flex align-items-center mt-2">
                    <input type="hidden" name="section" value="discdur">
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
