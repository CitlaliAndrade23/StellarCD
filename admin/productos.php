
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. PROTEGER EL ACCESO
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}


// Token de seguridad para eliminar productos
if (empty($_SESSION['csrf_admin'])) {
    $_SESSION['csrf_admin'] = bin2hex(random_bytes(32));
}


// =====================================
// 3. CONECTAR CON MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

// =====================================
// 4. CONSULTAR PRODUCTOS
// =====================================

$sql = "SELECT
            p.id_producto,
            p.nombre,
            p.descripcion,
            p.precio,
            p.imagen,
            p.disponible,
            c.nombre AS categoria
        FROM productos p
        LEFT JOIN categorias c
            ON p.id_categoria = c.id_categoria
        ORDER BY p.id_producto DESC";

$consulta = $conexion->query($sql);

$productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Función para evitar mostrar HTML peligroso
function escaparAdmin($valor) {
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

    <title>Gestión de productos | Stellar CD</title>

    <link rel="stylesheet"
          href="../assets/css/style.css?v=11">

    <style>
        /* =====================================
           GESTIÓN DE PRODUCTOS
        ===================================== */

        .admin-productos {
            max-width: 1200px;
            margin: 0 auto;
            padding: 70px 25px 100px;
        }

        .admin-productos-encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
            margin-bottom: 35px;
        }

        .admin-productos-encabezado h1 {
            font-size: clamp(30px, 4vw, 48px);
            margin: 12px 0;
        }

        .admin-productos-encabezado h1 span {
            color: #ff5b99;
        }

        .admin-productos-encabezado p {
            color: #c7c7d9;
        }

        .admin-boton-agregar {
            display: inline-block;
            padding: 15px 22px;
            border-radius: 8px;
            background: #ff5b99;
            color: #151522;
            font-weight: bold;
            text-decoration: none;
            white-space: nowrap;
        }

        .admin-boton-agregar:hover {
            background: #ff82ae;
        }

        .admin-tabla-contenedor {
            background: #23233a;
            border-radius: 16px;
            overflow-x: auto;
        }

        .admin-tabla {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .admin-tabla th {
            background: #30304b;
            padding: 18px;
            font-size: 12px;
            letter-spacing: 1px;
            color: #ff82ae;
        }

        .admin-tabla td {
            padding: 18px;
            border-bottom: 1px solid #393950;
            vertical-align: middle;
        }

        .admin-tabla tr:last-child td {
            border-bottom: none;
        }

        .admin-imagen-producto {
            width: 65px;
            height: 65px;
            background: #35354f;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .admin-imagen-producto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .admin-imagen-producto span {
            font-size: 28px;
            color: #ff5b99;
        }

        .admin-producto-nombre {
            font-weight: bold;
        }

        .admin-producto-descripcion {
            display: block;
            margin-top: 5px;
            color: #c7c7d9;
            font-size: 12px;
        }

        .admin-estado {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .admin-estado.activo {
            background: #244c3b;
            color: #83e0ae;
        }

        .admin-estado.inactivo {
            background: #52343e;
            color: #ff9eb4;
        }

        .admin-acciones-tabla {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .admin-boton-editar,
        .admin-boton-eliminar {
            display: inline-block;
            padding: 9px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
        }

        .admin-boton-editar {
            background: #41415f;
            color: #fff;
        }

        .admin-boton-eliminar {
            background: #542c42;
            color: #ff9eb4;
        }

        .admin-boton-volver {
            display: inline-block;
            margin-top: 30px;
            color: #ff82ae;
            text-decoration: none;
        }

        .admin-sin-productos {
            padding: 35px;
            color: #c7c7d9;
        }

        @media (max-width: 768px) {
            .admin-productos-encabezado {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-tabla {
                min-width: 800px;
            }
        }

        
/* FORMULARIO ELIMINAR */
.admin-form-eliminar {
    display: inline-block;
    margin: 0;
}

button.admin-boton-eliminar {
    font-family: inherit;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    background: #542c42;
    color: #ff9eb4;
    padding: 9px 12px;
    border-radius: 7px;
}

button.admin-boton-eliminar:hover {
    background: #74344f;
}

/* MENSAJES */
.admin-mensaje-exito,
.admin-mensaje-error {
    padding: 15px 20px;
    margin: 20px 0;
    border-radius: 8px;
    font-size: 14px;
}

.admin-mensaje-exito {
    background: #204b3b;
    color: #9af0c5;
}

.admin-mensaje-error {
    background: #542c42;
    color: #ffb4cc;
}

    </style>

</head>

<body>

<!-- HEADER COMPARTIDO -->
<?php require __DIR__ . '/../includes/header.php'; ?>


<main class="admin-productos">

    <!-- ENCABEZADO -->
    <div class="admin-productos-encabezado">

        <div>
            <span class="etiqueta">
                STELLAR CD / PRODUCT MANAGEMENT
            </span>

            <h1>
                GESTIÓN DE
                <span>PRODUCTOS. ✳</span>
            </h1>

            <p>
                Consulta los productos registrados
                en el catálogo de Stellar CD.
            </p>
        </div>

       
<a href="agregarProducto.php" class="admin-boton-agregar">
    + AGREGAR PRODUCTO
</a>


    </div>

<!-- MENSAJES DE RESULTADO -->

<?php if (($_GET['eliminado'] ?? '') === '1'): ?>

    <div class="admin-mensaje-exito" role="status">
        ✳ Producto eliminado correctamente.
    </div>

<?php elseif (isset($_GET['error'])): ?>

    <div class="admin-mensaje-error" role="alert">

        <?php
        switch ($_GET['error']) {

            case 'pedidos':
                echo 'Este producto tiene pedidos asociados. '
                   . 'Puedes desactivarlo desde EDITAR.';
                break;

            case 'noexiste':
                echo 'El producto no existe.';
                break;

            case 'id':
                echo 'El ID del producto no es válido.';
                break;

            default:
                echo 'No fue posible eliminar el producto.';
        }
        ?>

    </div>

<?php endif; ?>


    <!-- TABLA DE PRODUCTOS -->
    <div class="admin-tabla-contenedor">

        <?php if (empty($productos)): ?>

            <p class="admin-sin-productos">
                Todavía no hay productos registrados.
            </p>

        <?php else: ?>

            <table class="admin-tabla">

                <thead>
                    <tr>
                        <th>IMAGEN</th>
                        <th>PRODUCTO</th>
                        <th>CATEGORÍA</th>
                        <th>PRECIO</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($productos as $producto): ?>

                        <tr>

                            <!-- IMAGEN -->
                            <td>
                                <div class="admin-imagen-producto">

                                    <?php if (!empty($producto['imagen'])): ?>

                                        <img
                                            src="../assets/img/<?php
                                                echo escaparAdmin(
                                                    basename($producto['imagen'])
                                                );
                                            ?>"
                                            alt="<?php
                                                echo escaparAdmin(
                                                    $producto['nombre']
                                                );
                                            ?>">

                                    <?php else: ?>

                                        <span>✳</span>

                                    <?php endif; ?>

                                </div>
                            </td>


                            <!-- NOMBRE Y DESCRIPCIÓN -->
                            <td>
                                <span class="admin-producto-nombre">
                                    <?php
                                        echo escaparAdmin(
                                            $producto['nombre']
                                        );
                                    ?>
                                </span>

                                <span class="admin-producto-descripcion">
                                    <?php
                                        echo escaparAdmin(
                                            $producto['descripcion'] ?? ''
                                        );
                                    ?>
                                </span>
                            </td>


                            <!-- CATEGORÍA -->
                            <td>
                                <?php
                                    echo escaparAdmin(
                                        $producto['categoria'] ?? 'Sin categoría'
                                    );
                                ?>
                            </td>


                            <!-- PRECIO -->
                            <td>
                                <strong>
                                    $<?php
                                        echo number_format(
                                            (float) $producto['precio'],
                                            2
                                        );
                                    ?> MXN
                                </strong>
                            </td>


                            <!-- ESTADO -->
                            <td>

                                <?php if ((int) $producto['disponible'] === 1): ?>

                                    <span class="admin-estado activo">
                                        DISPONIBLE
                                    </span>

                                <?php else: ?>

                                    <span class="admin-estado inactivo">
                                        NO DISPONIBLE
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ACCIONES -->
                            <td>
                                <div class="admin-acciones-tabla">

                                    
<a
    href="editarProducto.php?id=<?php
        echo (int) $producto['id_producto'];
    ?>"
    class="admin-boton-editar">

    EDITAR

</a>


                                    
<form
    action="eliminarProducto.php"
    method="POST"
    class="admin-form-eliminar"
    onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Esta acción no se puede deshacer.');">

    <input
        type="hidden"
        name="id_producto"
        value="<?php echo (int) $producto['id_producto']; ?>">

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars(
            $_SESSION['csrf_admin'],
            ENT_QUOTES,
            'UTF-8'
        ); ?>">

    <button
        type="submit"
        class="admin-boton-eliminar">

        ELIMINAR

    </button>

</form>


                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>


    <!-- VOLVER AL DASHBOARD -->
    <a href="index.php" class="admin-boton-volver">
        ← VOLVER AL PANEL
    </a>

</main>

</body>
</html>
