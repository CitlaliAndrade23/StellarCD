
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

// Si no ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

// Si no es administrador
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

// Total de categorías
$consulta = $conexion->query(
    "SELECT COUNT(*) FROM categorias"
);

$totalCategorias = (int) $consulta->fetchColumn();

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

    <link rel="stylesheet"
          href="../assets/css/style.css?v=11">

    <style>
        /* PANEL ADMINISTRATIVO */

        .admin-contenedor {
            max-width: 1200px;
            margin: 0 auto;
            padding: 70px 25px 100px;
        }

        .admin-encabezado {
            margin-bottom: 45px;
        }

        .admin-encabezado h1 {
            font-size: clamp(32px, 5vw, 56px);
            margin: 15px 0;
        }

        .admin-encabezado h1 span {
            color: #ff5b99;
        }

        .admin-encabezado p {
            color: #c7c7d9;
        }

        .admin-estadisticas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 55px;
        }

        .admin-tarjeta {
            background: #23233a;
            border-radius: 16px;
            padding: 30px;
        }

        .admin-tarjeta h2 {
            font-size: 14px;
            color: #c7c7d9;
            margin-bottom: 20px;
        }

        .admin-tarjeta strong {
            display: block;
            font-size: 48px;
            color: #ff5b99;
        }

        .admin-seccion h2 {
            font-size: 28px;
            margin-bottom: 25px;
        }

        .admin-acciones {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .admin-accion {
            display: block;
            background: #23233a;
            padding: 30px;
            border-radius: 16px;
            color: #fff;
            text-decoration: none;
            transition: transform 0.2s, background 0.2s;
        }

        .admin-accion:hover {
            transform: translateY(-4px);
            background: #30304b;
        }

        .admin-accion span {
            font-size: 32px;
            color: #ff5b99;
        }

        .admin-accion h3 {
            margin: 15px 0 10px;
        }

        .admin-accion p {
            color: #c7c7d9;
        }

        @media (max-width: 768px) {
            .admin-estadisticas,
            .admin-acciones {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>

<body>

<!-- =====================================
     HEADER COMPARTIDO
===================================== -->

<?php require __DIR__ . '/../includes/header.php'; ?>


<!-- =====================================
     PANEL ADMINISTRATIVO
===================================== -->

<main class="admin-contenedor">

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
            gestionar los productos y pedidos.
        </p>

    </div>


    <!-- =====================================
         ESTADÍSTICAS
    ===================================== -->

    <section class="admin-estadisticas">

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


        <article class="admin-tarjeta">

            <h2>CATEGORÍAS</h2>

            <strong>
                <?php echo $totalCategorias; ?>
            </strong>

        </article>

    </section>


    <!-- =====================================
         ACCIONES ADMINISTRATIVAS
    ===================================== -->

    <section class="admin-seccion">

        <h2>GESTIONAR STELLAR CD ✦</h2>

        <div class="admin-acciones">

           
<!-- PRODUCTOS -->

<a href="productos.php" class="admin-accion">

    <span>✳</span>

    <h3>GESTIÓN DE PRODUCTOS</h3>

    <p>
        Consulta y administra los productos
        del catálogo de Stellar CD.
    </p>

</a>



            <!-- PEDIDOS -->

            <div class="admin-accion">

                <span>♡</span>

                <h3>GESTIÓN DE PEDIDOS</h3>

                <p>
                    Próximamente podrás consultar
                    pedidos y actualizar su estado.
                </p>

            </div>

        </div>

    </section>

</main>

</body>
</html>
