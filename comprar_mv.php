<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TotCloud - Comprar Màquina Virtual</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        footer {
            margin-top: auto;
        }

        #searchBar {
            display: none;
        }

        #clearFilterButton {
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Barra de navegació -->
    <nav class="navbar navbar-dark" style="background-color: #343a40;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">TotCloud</span>
            <button class="btn">
                <img src="images/cesta.png" alt="Cistella" class="img-fluid" style="max-width: 1.8em; height: auto;">
            </button>
        </div>
    </nav>

    <!-- Contingut principal -->
    <main class="container mt-5">
        <h4 class="text-black text-primary px-2">Seleccioni els components...</h4>
        <div class="row">
            <!-- Panell central -->
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Filtres a l'esquerra -->
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="filtroMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                Filtres
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filtroMenu">
                                <li><a class="dropdown-item" href="#" onclick="applyFilter('cpu')">CPU</a></li>
                                <li><a class="dropdown-item" href="#" onclick="applyFilter('gpu')">GPU</a></li>
                                <li><a class="dropdown-item" href="#" onclick="applyFilter('disc_dur')">Disc Dur</a></li>
                                <li><a class="dropdown-item" href="#" onclick="applyFilter('ram')">RAM</a></li>
                                <li><a class="dropdown-item" href="#" onclick="applyFilter('sistema_operatiu')">Sistema Operatiu</a></li>
                            </ul>
                            <!-- Botó per eliminar el filtre -->
                            <button class="btn btn-danger" id="clearFilterButton" onclick="clearFilter()">X</button>
                        </div>

                        <!-- Barra de cerca, inicialment oculta -->
                        <div id="searchBar">
                            <div class="col-12">
                                <input type="text" class="form-control" id="searchInput" placeholder="Marca del component..." oninput="filterBySearch()">
                            </div>
                        </div>

                        <!-- Filtres a la dreta -->
                        <div>
                            <select class="form-select" style="width: auto;" id="ordenPrecio" onchange="sortPrice()">
                                <option value="asc">Preu: Menor a Major</option>
                                <option value="desc">Preu: Major a Menor</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Aquí es mostraran els components -->
                        <div id="componentesContainer" class="row">
                            <!-- Els components seran generats dinàmicament -->
                            <p class="text-center text-secondary">Seleccioneu un filtre per mostrar els components disponibles.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Peu de pàgina -->
    <footer class="text-center py-3 mt-auto" style="background-color: #343a40; color: #f8f9fa;">
        <div class="container">
            <p class="mb-0">&copy; 2024 TotCloud. Tots els drets reservats.</p>
            <p class="mb-0">Contacte: info@totcloud.com | Telèfon: +34 900 123 456</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentFilter = null; // Variable per emmagatzemar el filtre actual
        let asc = "ASC";
        let brand = "";
        //variables per guardar els components que l'usuari vol comprar (es guarda l'id)
        let cpus = array();
        let gpus = array();
        let discs_durs = array();
        let sis_op = 0;
        let rams = array();

        //Funció per agafar els components de la base de dades
        function fetchData(queryType, asc, brand, callback) {
            fetch(`fetchData.php?query=${queryType}&asc=${asc}&brand=${brand}`)
                .then(response => {
                    console.log(`Response for ${queryType} ${asc} ${brand}:`, response);
                    return response.json();
                })
                .then(data => {
                    console.log(`Data received for ${queryType}:`, data);
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        document.getElementById('componentesContainer').innerHTML =
                            `<p class="text-center text-secondary">No s'han trobat components per aquest filtre.</p>`;
                        console.error(`No valid data received for ${queryType}.`);
                        return;
                    }
                    callback(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Funció per aplicar els filtres
        function applyFilter(filter) {
            currentFilter = filter; // Emmagatzema el filtre actual

            // Mostrar la barra de cerca només per a "CPU" o "GPU"
            if (filter === 'cpu' || filter === 'gpu') {
                document.getElementById('searchBar').style.display = 'block';
            } else {
                document.getElementById('searchBar').style.display = 'none';
            }

            document.getElementById('clearFilterButton').style.display = 'inline-block'; // Mostra el botó per eliminar el filtre
            ferConsulta();
        }

        // Funció per ordenar els preus
        function sortPrice() {
            const order = document.getElementById('ordenPrecio').value;
            order === 'asc' ? asc = "ASC" : asc = "DESC";
            ferConsulta();
        }

        // Funció per eliminar el filtre
        function clearFilter() {
            currentFilter = null; // Elimina el filtre actual
            brand = "";
            document.getElementById('componentesContainer').innerHTML = `<p class="text-center text-secondary">Seleccioneu un filtre per mostrar els components disponibles.</p>`;
            document.getElementById('clearFilterButton').style.display = 'none'; // Amaga el botó per eliminar el filtre
            document.getElementById('searchBar').style.display = 'none'; // Amaga la barra de cerca
        }

        // Funció per filtrar per la cerca en el camp d'entrada
        function filterBySearch() {
            brand = document.getElementById('searchInput').value.toLowerCase();
            ferConsulta();
        }

        // Funció per fer la consulta i obtenir els components segons el filtre
        function ferConsulta() {
            // Cridem la funció fetchData per obtenir les dades
            fetchData(currentFilter, asc, brand, function(data) {
                mostrarComponents(data, currentFilter);
            });
        }

        // Funció per mostrar els components a la pàgina
        function mostrarComponents(data, currentFilter) {
            let html = '';
            switch (currentFilter) {
                case 'cpu':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.nomMarca} ${component.nom}</h5>
                                    <p class="card-text">Nuclis: ${component.nuclis}</p>
                                    <p class="card-text">Velocitat de Rellotge: ${component.valocitatRellotge}GHz</p>
                                    <p class="card-text" id="preu_${component.idcpu}">Preu: ${component.preu}€</p>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'gpu':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.nomMarca} ${component.nom}</h5>
                                    <p class="card-text">Nuclis: ${component.nuclis}</p>
                                    <p class="card-text">VRAM -></br>Capacitat: ${component.vram}GB</br>Generacio:</p>
                                    <p class="card-text" id="preu_${component.idgpu}">Preu: ${component.preu}€</p>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'dis_dur':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.nomTipus}</h5>
                                    <p class="card-text">Capacitat: ${component.capacitat}GB</p>
                                    <p class="card-text" id="preu_${component.iddisc}">Preu: ${component.preu}€</p>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'ram':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.generacio}</h5>
                                    <p class="card-text">Capacitat: ${component.capacitat}GB</p>
                                    <p class="card-text" id="preu_${component.idram}">Preu: ${component.preu}€</p>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'sistema_operatiu':
                    break;
                default:
                    break;
            }
            document.getElementById('componentesContainer').innerHTML = html;
        }
    </script>
</body>

</html>