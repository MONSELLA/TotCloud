<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TotCloud - Página Principal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-dark" style="background-color: #343a40;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">TotCloud</span>
            <button class="btn">
                <img src="images/cesta.png" alt="Cesta" class="img-fluid" style="max-width: 1.8em; height: auto;">
            </button>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="d-flex flex-column justify-content-center align-items-center vh-100">
        <div class="text-center mb-4">
            <h1 class="text-black text-primary">Benvingut a TotCloud</h1>
            <p class="text-secondary">Selecciona una opció per continuar:</p>
        </div>
        <div class="d-flex flex-column gap-3" style="max-width: 300px; width: 100%;">
            <a href="#" class="btn btn-primary btn-lg w-100">Consultar els meus productes</a>
            <a href="comprar_mv.php" class="btn btn-secondary btn-lg w-100">Comprar màquina virtual</a>
            <a href="#" class="btn btn-secondary btn-lg w-100">Comprar base de dades</a>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="text-center py-3 mt-auto" style="background-color: #343a40; color: #f8f9fa;">
        <div class="container">
            <p class="mb-0">&copy; 2024 TotCloud. Tots els drets reservats.</p>
            <p class="mb-0">Contacte: info@totcloud.com | Telèfon: +34 900 123 456</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>