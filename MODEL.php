<?php
// MODEL.php
// Classe per gestionar els Models amb paginació i cerca

class MODEL {
    private $conn;

    // Constructor
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Afegir un nou Model
    public function addModel($nom, $nomMarca) {
        $stmt = $this->conn->prepare("INSERT INTO MODEL (nom, nomMarca) VALUES (?, ?)");
        $stmt->bind_param("ss", $nom, $nomMarca);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar un Model
    public function deleteModel($nom) {
        $stmt = $this->conn->prepare("DELETE FROM MODEL WHERE nom = ?");
        $stmt->bind_param("s", $nom);
        $stmt->execute();
        $stmt->close();
    }

    // Actualitzar un Model
    public function updateModel($old_nom, $new_nom, $nomMarca) {
        $stmt = $this->conn->prepare("UPDATE MODEL SET nom = ?, nomMarca = ? WHERE nom = ?");
        $stmt->bind_param("sss", $new_nom, $nomMarca, $old_nom);
        $stmt->execute();
        $stmt->close();
    }

    // Obtenir el codi HTML per mostrar els Models amb paginació i cerca
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
        $stmt = $this->conn->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM MODEL WHERE nom LIKE ? OR nomMarca LIKE ? LIMIT ?, ?");
        $stmt->bind_param("ssii", $search_param, $search_param, $offset, $records_per_page);
        $stmt->execute();
        $result = $stmt->get_result();

        // Obtenir el total de registres trobats
        $total_result = $this->conn->query("SELECT FOUND_ROWS() AS total");
        $total_row = $total_result->fetch_assoc();
        $total_records = $total_row['total'];

        // Calcular el nombre total de pàgines
        $total_pages = ceil($total_records / $records_per_page);

        $html = '<h2>Model</h2>';

        // Formulari per afegir un nou Model
        $html .= '
            <form method="post">
                <div class="mb-3">
                    <input type="text" class="form-control" name="nom" placeholder="Nom" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="nomMarca" placeholder="Marca" required>
                </div>
                <button type="submit" name="add_model" class="btn btn-success w-100">Afegir Model</button>
            </form>

            <br><br>

            <!-- Formulari per cercar Models -->
            <form method="get" class="mb-3">
                <input type="hidden" name="section" value="model">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cerca Model" value="'.htmlspecialchars($search).'">
                            <button type="submit" class="btn btn-primary">Cercar</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Taula de Models -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Marca</th>
                        <th>Accions</th>
                    </tr>
                </thead>
                <tbody>';
        while ($row = $result->fetch_assoc()) {
            $nomEsc = htmlspecialchars($row['nom'], ENT_QUOTES);
            $nomMarcaEsc = htmlspecialchars($row['nomMarca'], ENT_QUOTES);

            $html .= "<tr>
                        <td>{$nomEsc}</td>
                        <td>{$nomMarcaEsc}</td>
                        <td>
                            <form method='post' style='display:inline-block;'>
                                <input type='hidden' name='nom' value='{$nomEsc}'>
                                <button type='submit' name='delete_model' class='btn btn-danger'>Eliminar</button>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='mostrarFormularioActualizar(\"{$nomEsc}\", \"{$nomMarcaEsc}\")'>Actualitzar</button>
                        </td>
                    </tr>";
        }
        $html .= '</tbody></table>';

        // Formulari per actualitzar un Model (apareix sota de la taula)
        $html .= '
            <div id="formulario-actualizar" style="display: none; margin-top: 20px;">
                <form method="post">
                    <input type="hidden" name="old_nom" id="old_nom">
                    <div class="mb-3">
                        <label for="new_nom" class="form-label" style="font-weight: bold;">Nou Nom:</label>
                        <input type="text" class="form-control" name="new_nom" id="new_nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomMarca" class="form-label" style="font-weight: bold;">Marca:</label>
                        <input type="text" class="form-control" name="nomMarca" id="nomMarca" required>
                    </div>
                    <button type="submit" name="update_model" class="btn btn-success">Guardar Canvis</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarFormulario()">Cancel·lar</button>
                </form>
                <br>
            </div>
        ';

        // Funcions JavaScript per mostrar i amagar el formulari
        $html .= '
            <script>
                function mostrarFormularioActualizar(nom, nomMarca) {
                    document.getElementById("old_nom").value = nom;
                    document.getElementById("new_nom").value = nom;
                    document.getElementById("nomMarca").value = nomMarca;
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
                $html .= '<li class="page-item"><a class="page-link" href="?section=model&search='.urlencode($search).'&page='.$prev_page.'">Anterior</a></li>';
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
                    $html .= '<li class="page-item"><a class="page-link" href="?section=model&search='.urlencode($search).'&page='.$i.'">'.$i.'</a></li>';
                }
            }

            if ($end_page < $total_pages) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            // Botó "Següent"
            if ($page < $total_pages) {
                $next_page = $page + 1;
                $html .= '<li class="page-item"><a class="page-link" href="?section=model&search='.urlencode($search).'&page='.$next_page.'">Següent</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">Següent</span></li>';
            }

            $html .= '</ul>';

            // Camp per anar a una pàgina específica
            $html .= '
                <form method="get" class="d-flex align-items-center mt-2">
                    <input type="hidden" name="section" value="model">
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
