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
            <button class="btn position-relative" onclick="mostrarCistella()">
                <img src="images/cesta.png" alt="Cistella" class="img-fluid" style="max-width: 1.8em; height: auto;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartCount">
                    0
                    <span class="visually-hidden">elements a la cistella</span>
                </span>
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

    <!-- Modal per mostrar la cistella -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cartModalLabel">La Teva Cistella</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>CPUs</h5>
                    <ul id="cartCpus" class="list-group mb-3"></ul>
                    <h5>GPUs</h5>
                    <ul id="cartGpus" class="list-group mb-3"></ul>
                    <h5>Discs Durs</h5>
                    <ul id="cartDiscsDurs" class="list-group mb-3"></ul>
                    <h5>RAMs</h5>
                    <ul id="cartRams" class="list-group mb-3"></ul>
                    <h5>Sistema Operatiu</h5>
                    <ul id="cartSisOp" class="list-group mb-3"></ul>
                    <h4>Total: <span id="cartTotal">0</span>€</h4>
                </div>
                <div class="modal-footer">
                    <button id="finalitzar" type="button" class="btn btn-primary">Finalitzar Compra</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let currentFilter = null; // Variable per emmagatzemar el filtre actual
        let asc = "ASC";
        let brand = "";
        // Variables per guardar els components que l'usuari vol comprar (es guarda l'id)
        let cpus = new Map();
        let gpus = new Map();
        let discs_durs = new Map();
        let sis_op = new Map();
        let rams = new Map();

        // Funció per agafar els components de la base de dades
        function fetchData(queryType, asc, brand, callback) {
            fetch(`fetchData.php?query=${queryType}&asc=${asc}&brand=${brand}`)
                .then(response => {
                    console.log(`Response for ${queryType} ${asc} ${brand}:`, response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`Data received for ${queryType}:`, data);
                    if (data.error) {
                        document.getElementById('componentesContainer').innerHTML =
                            `<p class="text-center text-secondary">${data.error}</p>`;
                        console.error(`Error from server: ${data.error}`);
                        return;
                    }
                    if (!data || !Array.isArray(data) || data.length === 0) {
                        document.getElementById('componentesContainer').innerHTML =
                            `<p class="text-center text-secondary">No s'han trobat components per aquest filtre.</p>`;
                        console.error(`No valid data received for ${queryType}.`);
                        return;
                    }
                    callback(data);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById('componentesContainer').innerHTML =
                        `<p class="text-center text-secondary">Hi ha hagut un error en carregar els components.</p>`;
                });
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
            // Comprovem que hi hagi un filtre aplicat
            if (!currentFilter) {
                document.getElementById('componentesContainer').innerHTML =
                    `<p class="text-center text-secondary">Seleccioneu un filtre per mostrar els components disponibles.</p>`;
                return;
            }

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
                                    <p class="card-text">Preu: ${component.preu}€</p>
                                    <button id="boto_${component.idcpu}" class="btn btn-success" onclick="addToCart('cpu', ${component.idcpu}, ${component.preu}, '${component.nomMarca}', '${component.nom}')">Afegir a la Cistella</button>
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
                                    <p class="card-text">VRAM -><br>Generació: ${component.generacio}GB<br>Capacitat: ${component.capacitat}</p>
                                    <p class="card-text">Preu: ${component.preu}€</p>
                                    <button id="boto_${component.idgpu}" class="btn btn-success" onclick="addToCart('gpu', ${component.idgpu}, ${component.preu}, '${component.nomMarca}', '${component.nom}')">Afegir a la Cistella</button>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'disc_dur':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.nomTipus}</h5>
                                    <p class="card-text">Capacitat: ${component.capacitat}GB</p>
                                    <p class="card-text">Preu: ${component.preu}€</p>
                                    <button id="boto_${component.iddisc}" class="btn btn-success" onclick="addToCart('disc_dur', ${component.iddisc}, ${component.preu}, '${component.nomTipus}', ${component.capacitat})">Afegir a la Cistella</button>
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
                                    <p class="card-text">Preu: ${component.preu}€</p>
                                    <button id="boto_${component.idram}" class="btn btn-success" onclick="addToCart('ram', ${component.idram}, ${component.preu}, '${component.generacio}', ${component.capacitat})">Afegir a la Cistella</button>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                case 'sistema_operatiu':
                    data.forEach(component => {
                        html += `
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h5 class="card-title">${component.nom}</h5>
                                    <p class="card-text">Versió: ${component.versio}</p>
                                    <button id="boto_${component.idSO}" class="btn btn-success" onclick="addToCart('sistema_operatiu', ${component.idSO}, null, '${component.nom}', ${component.versio})">Afegir a la Cistella</button>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    break;
                default:
                    break;
            }
            document.getElementById('componentesContainer').innerHTML = html;
        }

        // Funció per afegir un component a la cistella
        function addToCart(componentType, componentId, componentPrice, info1, info2) {
            switch (componentType) {
                case 'cpu':
                    cpus.set(componentId, [componentPrice, info1, info2]);
                    document.getElementById("boto_" + componentId).disabled = true;
                    break;
                case 'gpu':
                    gpus.set(componentId, [componentPrice, info1, info2]);
                    document.getElementById("boto_" + componentId).disabled = true;
                    break;
                case 'disc_dur':
                    discs_durs.set(componentId, [componentPrice, info1, info2]);
                    document.getElementById("boto_" + componentId).disabled = true;
                    break;
                case 'ram':
                    rams.set(componentId, [componentPrice, info1, info2]);
                    document.getElementById("boto_" + componentId).disabled = true;
                    break;
                case 'sistema_operatiu':
                    sis_op.set(componentId, [componentPrice, info1, info2]);
                    //Desactivar tots els botons associats a un sistema operatiu mentres hi hagi un a la cistella
                    let i = 1
                    while (document.getElementById("boto_" + i) != null) {
                        document.getElementById("boto_" + i).disabled = true;
                        i++;
                    }
                    break;
                default:
                    console.error("Tipus de component desconegut:", componentType);
                    return;
            }
            updateCartCount();
            console.log(`Cistella actual: 
                CPUs: ${Array.from(cpus.entries())},
                GPUs: ${Array.from(gpus.entries())},
                Discs durs: ${Array.from(discs_durs.entries())},
                RAMs: ${Array.from(rams.entries())},
                Sistemes operatius: ${Array.from(sis_op.entries())}
            `);
        }

        // Funció per actualitzar el contador de la cistella
        function updateCartCount() {
            const count = cpus.size + gpus.size + discs_durs.size + rams.size + sis_op.size;
            document.getElementById('cartCount').innerText = count;
        }

        // Funció per mostrar la cistella en la modal
        function mostrarCistella() {
            const currentModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
            if (currentModal) {
                currentModal.hide();
            }

            // Pujar les llistes
            const cartCpus = document.getElementById('cartCpus');
            const cartGpus = document.getElementById('cartGpus');
            const cartDiscsDurs = document.getElementById('cartDiscsDurs');
            const cartRams = document.getElementById('cartRams');
            const cartSisOp = document.getElementById('cartSisOp');
            const cartTotal = document.getElementById('cartTotal');

            // Pujar els elements a les llistes
            cartCpus.innerHTML = '';
            cpus.forEach((arr, id) => {
                cartCpus.innerHTML += `<li class="list-group-item">CPU ${arr[1]} ${arr[2]}, Preu: ${arr[0]}€ <button class="btn btn-sm btn-danger float-end" onclick="removeFromCart('cpu', ${id})">Eliminar</button></li>`;
            });

            cartGpus.innerHTML = '';
            gpus.forEach((preu, id) => {
                cartGpus.innerHTML += `<li class="list-group-item">GPU ${arr[1]} ${arr[2]}, Preu: ${arr[0]}€ <button class="btn btn-sm btn-danger float-end" onclick="removeFromCart('gpu', ${id})">Eliminar</button></li>`;
            });

            cartDiscsDurs.innerHTML = '';
            discs_durs.forEach((preu, id) => {
                cartDiscsDurs.innerHTML += `<li class="list-group-item">Disc Dur ${arr[1]} ${arr[2]}, Preu: ${arr[0]}€ <button class="btn btn-sm btn-danger float-end" onclick="removeFromCart('disc_dur', ${id})">Eliminar</button></li>`;
            });

            cartRams.innerHTML = '';
            rams.forEach((preu, id) => {
                cartRams.innerHTML += `<li class="list-group-item">RAM ${arr[1]} ${arr[2]}, Preu: ${arr[0]}€ <button class="btn btn-sm btn-danger float-end" onclick="removeFromCart('ram', ${id})">Eliminar</button></li>`;
            });

            cartSisOp.innerHTML = '';
            sis_op.forEach((preu, id) => {
                cartSisOp.innerHTML += `<li class="list-group-item">${arr[1]} ${arr[2]}, Preu:N/A <button class="btn btn-sm btn-danger float-end" onclick="removeFromCart('sistema_operatiu', ${id})">Eliminar</button></li>`;
            });

            // Calcular el total
            let total = 0;
            cpus.forEach(preu => {
                total += parseFloat(preu);
            });
            gpus.forEach(preu => {
                total += parseFloat(preu);
            });
            discs_durs.forEach(preu => {
                total += parseFloat(preu);
            });
            rams.forEach(preu => {
                total += parseFloat(preu);
            });
            sis_op.forEach(preu => {
                if (preu !== null) total += parseFloat(preu);
            });

            cartTotal.innerText = total.toFixed(2);

            if (cpus.size < 1 || rams.size < 1 || discs_durs.size < 1 || sis_op < 1) {
                document.getElementById("finalitzar").disabled = true;
            } else {
                document.getElementById("finalitzar").disabled = false;
            }

            // Mostrar la modal
            const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
            cartModal.show();
        }

        // Funció per eliminar un component de la cistella
        function removeFromCart(componentType, componentId) {
            switch (componentType) {
                case 'cpu':
                    cpus.delete(componentId);
                    document.getElementById("boto_" + componentId).disabled = false;
                    break;
                case 'gpu':
                    gpus.delete(componentId);
                    document.getElementById("boto_" + componentId).disabled = false;
                    break;
                case 'disc_dur':
                    discs_durs.delete(componentId);
                    document.getElementById("boto_" + componentId).disabled = false;
                    break;
                case 'ram':
                    rams.delete(componentId);
                    document.getElementById("boto_" + componentId).disabled = false;
                    break;
                case 'sistema_operatiu':
                    sis_op.delete(componentId);
                    let i = 1
                    while (document.getElementById("boto_" + i) != null) {
                        document.getElementById("boto_" + i).disabled = false;
                        i++;
                    }
                    break;
                default:
                    console.error("Tipus de component desconegut:", componentType);
                    return;
            }
            updateCartCount();
            mostrarCistella(); // Actualitzar la modal per reflectir els canvis
        }
    </script>
</body>

</html>