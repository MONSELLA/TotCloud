<?php
// SO.php
// Classe per gestionar els Sistemes Operatius amb paginació i cerca

class SO {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Afegir un nou Sistema Operatiu
    public function addSO($nom, $versio) {
        $stmt = $this->conn->prepare("INSERT INTO SISTEMA_OPERATIU (nom, versio) VALUES (?, ?)");
        $stmt->bind_param("ss", $nom, $versio);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar un Sistema Operatiu
    public function deleteSO($idSO) {
        $stmt = $this->conn->prepare("DELETE FROM SISTEMA_OPERATIU WHERE idSO = ?");
        $stmt->bind_param("i", $idSO);
        $stmt->execute();
        $stmt->close();
    }

    // Actualitzar un Sistema Operatiu
    public function updateSO($old_idSO, $nom, $versio) {
        $stmt = $this->conn->prepare("UPDATE SISTEMA_OPERATIU SET nom = ?, versio = ? WHERE idSO = ?");
        $stmt->bind_param("ssi", $nom, $versio, $old_idSO);
        $stmt->execute();
        $stmt->close();
    }

    // Obtenir el codi HTML per mostrar els Sistemes Operatius amb paginació i cerca
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
            FROM SISTEMA_OPERATIU 
            WHERE 
                idSO LIKE ? OR 
                nom LIKE ? OR 
                versio LIKE ? 
            LIMIT ?, ?
        ");

        // Assignació correcta de tipus: els primers tres són strings (LIKE) i els dos últims són enters (LIMIT).
        $stmt->bind_param(
            "sssii", 
            $search_param, // idSO
            $search_param, // nom
            $search_param, // versio
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

        $html = '<h2>Sistema Operatiu</h2>';

        // Formulari per afegir un nou Sistema Operatiu
        $html .= '
            <form method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" name="nom" placeholder="Nom" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="versio" placeholder="Versió" required>
                </div>
                <button type="submit" name="add_so" class="btn btn-success w-100">Afegir Sistema Operatiu</button>
            </form>

            <br><br>

            <!-- Formulari per cercar Sistemes Operatius -->
            <form method="get" class="mb-3">
                <input type="hidden" name="section" value="so">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cerca Sistema Operatiu" value="'.htmlspecialchars($search).'">
                            <button type="submit" class="btn btn-primary">Cercar</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Taula de Sistemes Operatius -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Versió</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            $idSOEsc = htmlspecialchars($row['idSO'], ENT_QUOTES);
            $nomEsc = htmlspecialchars($row['nom'], ENT_QUOTES);
            $versioEsc = htmlspecialchars($row['versio'], ENT_QUOTES);

            $html .= "<tr>
                        <td>{$idSOEsc}</td>
                        <td>{$nomEsc}</td>
                        <td>{$versioEsc}</td>
                        <td>
                            <form method='post' style='display:inline-block;'>
                                <input type='hidden' name='idSO' value='{$idSOEsc}'>
                                <button type='submit' name='delete_so' class='btn btn-danger'>Eliminar</button>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='mostrarFormularioActualizar(\"{$idSOEsc}\", \"{$nomEsc}\", \"{$versioEsc}\")'>Actualitzar</button>
                        </td>
                    </tr>";
        }
        $html .= '</tbody></table>';

        // Formulari per actualitzar un Sistema Operatiu (apareix sota de la taula)
        $html .= '
            <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
                <form method="post">
                    <input type="hidden" name="old_idSO" id="old_idSO">
                    <div class="mb-3">
                        <label for="nom" class="form-label" style="font-weight: bold;">Nom:</label>
                        <input type="text" class="form-control" name="nom" id="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="versio" class="form-label" style="font-weight: bold;">Versió:</label>
                        <input type="text" class="form-control" name="versio" id="versio" required>
                    </div>
                    <button type="submit" name="update_so" class="btn btn-success">Guardar Canvis</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
                </form>
                <br>
            </div>
        ';

        // Funcions JavaScript per mostrar i amagar el formulari
        $html .= '
            <script>
                function mostrarFormularioActualizar(idSO, nom, versio) {
                    // Assignar els valors als camps del formulari
                    document.getElementById("old_idSO").value = idSO;
                    document.getElementById("nom").value = nom;
                    document.getElementById("versio").value = versio;

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
                $html .= '<li class="page-item"><a class="page-link" href="?section=so&search='.urlencode($search).'&page='.$prev_page.'">Anterior</a></li>';
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
                    $html .= '<li class="page-item"><a class="page-link" href="?section=so&search='.urlencode($search).'&page='.$i.'">'.$i.'</a></li>';
                }
            }

            if ($end_page < $total_pages) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Botó "Següent"
            if ($page < $total_pages) {
                $next_page = $page + 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=so&search='.urlencode($search).'&page='.$next_page.'">Següent</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Següent</span></li>';
            }

            $html .= '</ul>';

            // Camp per anar a una pàgina específica
            $html .= '
                <form method="get" class="d-flex align-items-center mt-2">
                    <input type="hidden" name="section" value="so">
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
