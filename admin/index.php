
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. VERIFICAR ACCESO
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    http_response_code(403);
    exit('Acceso no autorizado. Esta página es solo para administradores.');
}

// =====================================
// 3. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

// =====================================
// 4. CONSULTAR ESTADÍSTICAS
// =====================================

// Total de productos
$consulta = $conexion->query(
    "SELECT COUNT(*) FROM productos"
);

$totalProductos = (int) $consulta->fetchColumn();

// Productos disponibles
$consulta = $conexion->query(
    "SELECT COUNT(*) FROM productos WHERE disponible = 1"
);

$productosDisponibles = (int) $consulta->fetchColumn();

// Nombre del administrador
$nombreAdmin = htmlspecialchars(
    $_SESSION['nombre_usuario'] ?? 'Administrador',
    ENT_QUOTES,
    'UTF-8'
);
?>

<!DOCTYPE html>
<html lang="es">


<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Panel de administrador | Stellar CD</title>

    <!-- ESTILOS GENERALES -->
    <link rel="stylesheet"
          href="../assets/css/style.css?v=16">

    <!-- ESTILOS RESPONSIVE -->
    <link rel="stylesheet"
          href="../assets/css/responsive.css?v=5">

    <!-- JAVASCRIPT GENERAL -->
    <script src="../assets/js/main.js?v=5" defer></script>

</head>


<body>

<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="admin-contenedor">

    <!-- ENCABEZADO -->

    <div class="admin-encabezado">

        <span class="etiqueta">
            STELLAR CD / ADMIN PANEL
        </span>

        <h1>
            HOLA,
            <span><?php echo $nombreAdmin; ?> ✳</span>
        </h1>

        <p>
            Bienvenido al panel de administración
            de Stellar CD. Desde aquí podrás
            gestionar los productos de tu catálogo.
        </p>

    </div>

    <!-- ESTADÍSTICAS -->

    <section class="admin-estadisticas"
             aria-label="Estadísticas de productos">

        <article class="admin-tarjeta">

            <h2>PRODUCTOS REGISTRADOS</h2>

            <strong>
                <?php echo $totalProductos; ?>
            </strong>

        </article>

        <article class="admin-tarjeta">

            <h2>PRODUCTOS DISPONIBLES</h2>

            <strong>
                <?php echo $productosDisponibles; ?>
            </strong>

        </article>

    </section>

    <!-- GESTIÓN DE PRODUCTOS -->

    <section class="admin-seccion">

        <h2>GESTIONAR STELLAR CD ✦</h2>

        <div class="admin-acciones">

            <a href="productos.php" class="admin-accion">

                <span aria-hidden="true">✳</span>

                <h3>GESTIÓN DE PRODUCTOS</h3>

                <p>
                    Consulta, agrega, edita y administra
                    los productos del catálogo de Stellar CD.
                </p>

            </a>

        </div>

    </section>

</main>

</body>
</html>
