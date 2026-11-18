
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. VERIFICAR USUARIO
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    header('Location: iniciarSesion.php');
    exit;
}

// =====================================
// 3. GENERAR TOKEN CSRF
// =====================================

if (empty($_SESSION['csrf_carrito'])) {
    $_SESSION['csrf_carrito'] = bin2hex(random_bytes(32));
}

// =====================================
// 4. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/config/conexion.php';

$idUsuario = (int) $_SESSION['id_usuario'];

// =====================================
// 5. CONSULTAR PRODUCTOS DEL CARRITO
// =====================================

$sql = "SELECT
            dc.id_detalle_carrito,
            dc.cantidad,
            p.nombre,
            p.precio,
            p.imagen,
            p.disponible
        FROM detalle_carrito dc
        INNER JOIN carritos c
            ON dc.id_carrito = c.id_carrito
        INNER JOIN productos p
            ON dc.id_producto = p.id_producto
        WHERE c.id_usuario = :usuario
        ORDER BY dc.id_detalle_carrito DESC";

$consulta = $conexion->prepare($sql);

$consulta->execute([
    'usuario' => $idUsuario
]);

$productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// =====================================
// 6. CALCULAR TOTALES
// =====================================

$total = 0;
$totalUnidades = 0;

foreach ($productos as $producto) {

    $precio = (float) $producto['precio'];
    $cantidad = (int) $producto['cantidad'];

    $total += $precio * $cantidad;
    $totalUnidades += $cantidad;
}

// =====================================
// 7. FUNCIÓN PARA ESCAPAR HTML
// =====================================

function escaparCarrito($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Mi carrito | Stellar CD</title>

    <!-- TOKEN CSRF -->
    <meta name="csrf-carrito"
          content="<?php echo escaparCarrito(
              $_SESSION['csrf_carrito']
          ); ?>">

    <!-- ESTILOS GENERALES -->
    <link rel="stylesheet"
          href="assets/css/style.css?v=15">

    <style>

        /* CONTENEDOR GENERAL */

        .carrito-contenedor {
            max-width: 1100px;
            margin: 0 auto;
            padding: 70px 25px 100px;
        }

        .carrito-titulo {
            font-size: clamp(30px, 4vw, 46px);
            margin-bottom: 10px;
        }

        .carrito-titulo span {
            color: #ff5b99;
        }

        .carrito-subtitulo {
            color: #c7c7d9;
            margin-bottom: 35px;
        }

        /* TABLA DE PRODUCTOS */

        .carrito-tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .carrito-tabla-contenedor {
            background: #23233a;
            border-radius: 15px;
            overflow-x: auto;
        }

        .carrito-tabla th {
            background: #30304b;
            color: #ff82ae;
            padding: 18px;
            font-size: 12px;
            text-align: left;
        }

        .carrito-tabla td {
            padding: 20px 18px;
            border-bottom: 1px solid #393950;
            vertical-align: middle;
        }

        /* INFORMACIÓN DEL PRODUCTO */

        .carrito-producto {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .carrito-imagen {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 10px;
            background: #35354f;
            flex-shrink: 0;
        }

        .carrito-imagen-vacia {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ff5b99;
            font-size: 30px;
        }

        .carrito-nombre {
            font-weight: bold;
        }

        /* BOTONES DE CANTIDAD */

        .carrito-cantidad {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .carrito-cantidad button {
            background: #41415f;
            color: white;
            border: none;
            padding: 7px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .carrito-cantidad button:hover {
            background: #ff5b99;
            color: #151522;
        }

        /* BOTÓN ELIMINAR */

        .carrito-eliminar {
            background: #542c42;
            color: #ff9eb4;
            border: none;
            padding: 9px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .carrito-eliminar:hover {
            background: #74344f;
        }

        /* RESUMEN DEL PEDIDO */

        .carrito-resumen {
            background: #23233a;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
            max-width: 420px;
            margin-left: auto;
        }

        .carrito-resumen h2 {
            margin-bottom: 20px;
        }

        .carrito-total {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }

        .carrito-total span:last-child {
            color: #ff5b99;
        }

        /* CONFIRMAR PEDIDO */

        .carrito-finalizar {
            display: block;
            width: 100%;
            background: #ff5b99;
            color: #151522;
            text-align: center;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .carrito-finalizar:hover {
            background: #ff82ae;
        }

        .carrito-finalizar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* CARRITO VACÍO */

        .carrito-vacio {
            background: #23233a;
            padding: 45px;
            border-radius: 15px;
            text-align: center;
        }

        .carrito-volver {
            display: inline-block;
            color: #ff82ae;
            text-decoration: none;
            margin-top: 25px;
        }

        .carrito-volver:hover {
            text-decoration: underline;
        }

        /* MENSAJES */

        .carrito-error {
            color: #ffb4cc;
            margin-top: 15px;
        }

        /* RESPONSIVE */

        @media (max-width: 768px) {

            .carrito-tabla {
                min-width: 700px;
            }

            .carrito-contenedor {
                padding: 40px 15px 70px;
            }
        }

    </style>

 
<!-- ESTILOS RESPONSIVE -->
<link rel="stylesheet"
      href="assets/css/responsive.css?v=6">

<!-- JAVASCRIPT DEL MENÚ HAMBURGUESA -->
<script src="assets/js/main.js?v=7" defer></script>

<!-- JAVASCRIPT DEL CARRITO -->
<script src="assets/js/carrito.js?v=4" defer></script>

<!-- GOOGLE ANALYTICS -->
<?php require_once __DIR__ . '/includes/analytics.php'; ?>

</head>

<body>

<!-- HEADER COMPARTIDO -->
<?php require __DIR__ . '/includes/header.php'; ?>

<main class="carrito-contenedor">

    <h1 class="carrito-titulo">
        MI <span>CARRITO. ✳</span>
    </h1>

    <p class="carrito-subtitulo">
        Revisa tus productos antes de realizar tu pedido.
    </p>

    <?php if (empty($productos)): ?>

        <!-- CARRITO VACÍO -->

        <div class="carrito-vacio">

            <h2>Tu carrito está vacío 🛒</h2>

            <p>
                Explora nuestros llaveros musicales.
            </p>

            <a href="catalogo.php"
               class="carrito-volver">
                VER CATÁLOGO
            </a>

        </div>

    <?php else: ?>

        <!-- TABLA DE PRODUCTOS -->

        <div class="carrito-tabla-contenedor">

            <table class="carrito-tabla">

                <thead>
                    <tr>
                        <th>PRODUCTO</th>
                        <th>PRECIO</th>
                        <th>CANTIDAD</th>
                        <th>SUBTOTAL</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($productos as $producto): ?>

                    <?php
                    $subtotal =
                        (float) $producto['precio'] *
                        (int) $producto['cantidad'];
                    ?>

                    <tr>

                        <!-- PRODUCTO -->

                        <td>

                            <div class="carrito-producto">

                                <?php if (!empty($producto['imagen'])): ?>

                                    <img
                                        class="carrito-imagen"
                                        src="assets/img/<?php
                                            echo escaparCarrito(
                                                basename($producto['imagen'])
                                            );
                                        ?>"
                                        alt="<?php
                                            echo escaparCarrito(
                                                $producto['nombre']
                                            );
                                        ?>">

                                <?php else: ?>

                                    <div class="carrito-imagen carrito-imagen-vacia">
                                        ✳
                                    </div>

                                <?php endif; ?>

                                <span class="carrito-nombre">
                                    <?php echo escaparCarrito(
                                        $producto['nombre']
                                    ); ?>
                                </span>

                            </div>

                        </td>

                        <!-- PRECIO -->

                        <td>
                            $<?php echo number_format(
                                (float) $producto['precio'],
                                2
                            ); ?>
                        </td>

                        <!-- CANTIDAD -->

                        <td>

                            <div class="carrito-cantidad">

                                <button
                                    type="button"
                                    class="btn-restar"
                                    aria-label="Restar una unidad"
                                    data-id="<?php
                                        echo (int) $producto['id_detalle_carrito'];
                                    ?>">
                                    −
                                </button>

                                <span>
                                    <?php echo (int) $producto['cantidad']; ?>
                                </span>

                                <button
                                    type="button"
                                    class="btn-sumar"
                                    aria-label="Sumar una unidad"
                                    data-id="<?php
                                        echo (int) $producto['id_detalle_carrito'];
                                    ?>">
                                    +
                                </button>

                            </div>

                        </td>

                        <!-- SUBTOTAL -->

                        <td>
                            $<?php echo number_format(
                                $subtotal,
                                2
                            ); ?>
                        </td>

                        <!-- ELIMINAR -->

                        <td>

                            <button
                                type="button"
                                class="carrito-eliminar"
                                data-id="<?php
                                    echo (int) $producto['id_detalle_carrito'];
                                ?>">
                                ELIMINAR
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <!-- RESUMEN DEL PEDIDO -->

        <div class="carrito-resumen">

            <h2>RESUMEN DEL PEDIDO</h2>

            <p>
                Productos: <?php echo $totalUnidades; ?>
            </p>

            <div class="carrito-total">

                <span>TOTAL</span>

                <span>
                    $<?php echo number_format(
                        $total,
                        2
                    ); ?> MXN
                </span>

            </div>

            <!-- CONFIRMAR PEDIDO -->

            <button
                type="button"
                id="btnConfirmarPedido"
                class="carrito-finalizar">
                CONFIRMAR PEDIDO
            </button>

            <p
                id="carritoMensaje"
                class="carrito-error"
                role="alert"
                aria-live="polite">
            </p>

        </div>

    <?php endif; ?>

    <!-- SEGUIR COMPRANDO -->

    <a href="catalogo.php"
       class="carrito-volver">
        ← SEGUIR COMPRANDO
    </a>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
