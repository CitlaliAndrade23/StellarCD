
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// =====================================
// 1. FUNCIÓN PARA RESPONDER EN JSON
// =====================================

function responder($exito, $mensaje, $codigo = 200, $idPedido = null) {

    http_response_code($codigo);

    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'id_pedido' => $idPedido
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================
// 2. VALIDAR MÉTODO POST
// =====================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', 405);
}

// =====================================
// 3. VALIDAR SESIÓN DEL CLIENTE
// =====================================

if (
    !isset($_SESSION['id_usuario']) ||
    ($_SESSION['rol'] ?? '') !== 'cliente'
) {
    responder(false, 'Debes iniciar sesión como cliente.', 401);
}

// =====================================
// 4. VALIDAR TOKEN CSRF
// =====================================

$entrada = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($entrada)) {
    responder(false, 'Solicitud inválida.', 400);
}

$token = $entrada['csrf_token'] ?? '';

if (
    !is_string($token) ||
    empty($_SESSION['csrf_carrito']) ||
    !hash_equals($_SESSION['csrf_carrito'], $token)
) {
    responder(false, 'Token de seguridad inválido.', 403);
}

// =====================================
// 5. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

$idUsuario = (int) $_SESSION['id_usuario'];

// =====================================
// 6. REGISTRAR PEDIDO
// =====================================

try {

    $conexion->beginTransaction();

    // Buscar y bloquear el carrito del usuario
    $consultaCarrito = $conexion->prepare(
        "SELECT id_carrito
         FROM carritos
         WHERE id_usuario = :usuario
         FOR UPDATE"
    );

    $consultaCarrito->execute([
        'usuario' => $idUsuario
    ]);

    $carrito = $consultaCarrito->fetch(PDO::FETCH_ASSOC);

    if (!$carrito) {
        $conexion->rollBack();

        responder(
            false,
            'No se encontró tu carrito.',
            400
        );
    }

    $idCarrito = (int) $carrito['id_carrito'];

    // Consultar los productos del carrito
    $consultaProductos = $conexion->prepare(
        "SELECT
            dc.id_producto,
            dc.cantidad,
            p.precio,
            p.disponible
         FROM detalle_carrito dc
         INNER JOIN productos p
            ON dc.id_producto = p.id_producto
         WHERE dc.id_carrito = :carrito
         FOR UPDATE"
    );

    $consultaProductos->execute([
        'carrito' => $idCarrito
    ]);

    $productos = $consultaProductos->fetchAll(PDO::FETCH_ASSOC);

    if (empty($productos)) {

        $conexion->rollBack();

        responder(
            false,
            'Tu carrito está vacío.',
            400
        );
    }

    // =====================================
    // 7. CALCULAR TOTAL
    // =====================================

    $totalCentavos = 0;

    foreach ($productos as $producto) {

        if ((int) $producto['disponible'] !== 1) {

            $conexion->rollBack();

            responder(
                false,
                'Uno de los productos no está disponible.',
                400
            );
        }

        $cantidad = (int) $producto['cantidad'];

        if ($cantidad <= 0) {

            $conexion->rollBack();

            responder(
                false,
                'Cantidad de producto inválida.',
                400
            );
        }

        $precioCentavos = (int) round(
            (float) $producto['precio'] * 100
        );

        if ($precioCentavos < 0) {

            $conexion->rollBack();

            responder(
                false,
                'Precio de producto inválido.',
                400
            );
        }

        $totalCentavos += $precioCentavos * $cantidad;
    }

    $total = number_format(
        $totalCentavos / 100,
        2,
        '.',
        ''
    );

    // =====================================
    // 8. INSERTAR PEDIDO
    // =====================================

    $insertarPedido = $conexion->prepare(
        "INSERT INTO pedidos
            (id_usuario, total, estado)
         VALUES
            (:usuario, :total, 'pendiente')"
    );

    $insertarPedido->execute([
        'usuario' => $idUsuario,
        'total' => $total
    ]);

    $idPedido = (int) $conexion->lastInsertId();

    // =====================================
    // 9. GUARDAR DETALLE DEL PEDIDO
    // =====================================

    $insertarDetalle = $conexion->prepare(
        "INSERT INTO detalle_pedido
            (id_pedido, id_producto, cantidad, precio_unitario)
         VALUES
            (:pedido, :producto, :cantidad, :precio)"
    );

    foreach ($productos as $producto) {

        $insertarDetalle->execute([
            'pedido' => $idPedido,
            'producto' => (int) $producto['id_producto'],
            'cantidad' => (int) $producto['cantidad'],
            'precio' => $producto['precio']
        ]);
    }

    // =====================================
    // 10. VACIAR CARRITO
    // =====================================

    $vaciarCarrito = $conexion->prepare(
        "DELETE FROM detalle_carrito
         WHERE id_carrito = :carrito"
    );

    $vaciarCarrito->execute([
        'carrito' => $idCarrito
    ]);

    // =====================================
    // 11. CONFIRMAR TRANSACCIÓN
    // =====================================

    $conexion->commit();

    $_SESSION['csrf_carrito'] = bin2hex(
        random_bytes(32)
    );

    responder(
        true,
        '¡Tu pedido fue registrado correctamente!',
        200,
        $idPedido
    );

} catch (Throwable $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log(
        'Error al confirmar pedido: ' . $e->getMessage()
    );

    responder(
        false,
        'Ocurrió un error al registrar el pedido.',
        500
    );
}
