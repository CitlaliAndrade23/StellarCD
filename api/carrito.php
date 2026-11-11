
<?php
// =====================================
// API DEL CARRITO - STELLAR CD
// =====================================

// 1. INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// =====================================
// 2. RESPUESTAS JSON
// =====================================

function responder($exito, $mensaje, $codigo = 200) {
    http_response_code($codigo);

    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

// =====================================
// 3. VERIFICAR SESIÓN
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    responder(
        false,
        'Debes iniciar sesión para utilizar el carrito.',
        401
    );
}

// =====================================
// 4. VERIFICAR MÉTODO POST
// =====================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', 405);
}

// =====================================
// 5. LEER DATOS JSON
// =====================================

$entrada = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($entrada)) {
    responder(false, 'Datos inválidos.', 400);
}

// =====================================
// 6. VALIDAR TOKEN CSRF
// =====================================

$tokenSesion = $_SESSION['csrf_carrito'] ?? '';
$tokenRecibido = $entrada['csrf_token'] ?? '';

if (
    !is_string($tokenSesion) ||
    $tokenSesion === '' ||
    !is_string($tokenRecibido) ||
    !hash_equals($tokenSesion, $tokenRecibido)
) {
    responder(
        false,
        'Token de seguridad inválido.',
        403
    );
}

// =====================================
// 7. VALIDAR ACCIÓN
// =====================================

$accion = $entrada['accion'] ?? '';

if (!in_array(
    $accion,
    ['agregar', 'sumar', 'restar', 'eliminar'],
    true
)) {
    responder(false, 'Acción no válida.', 400);
}

// =====================================
// 8. CONECTAR A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

$idUsuario = (int) $_SESSION['id_usuario'];

try {

    $conexion->beginTransaction();

    // =====================================
    // 9. AGREGAR PRODUCTO
    // =====================================

    if ($accion === 'agregar') {

        $idProducto = filter_var(
            $entrada['id_producto'] ?? null,
            FILTER_VALIDATE_INT
        );

        $cantidad = filter_var(
            $entrada['cantidad'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            !$idProducto ||
            !$cantidad ||
            $cantidad < 1 ||
            $cantidad > 99
        ) {
            $conexion->rollBack();

            responder(
                false,
                'Producto o cantidad no válidos.',
                400
            );
        }

        // Verificar que el producto exista y esté disponible
        $consulta = $conexion->prepare(
            "SELECT id_producto
             FROM productos
             WHERE id_producto = :producto
             AND disponible = 1"
        );

        $consulta->execute([
            'producto' => $idProducto
        ]);

        if (!$consulta->fetchColumn()) {
            $conexion->rollBack();

            responder(
                false,
                'Producto no disponible.',
                404
            );
        }

        // =====================================
        // 10. CREAR O LOCALIZAR CARRITO
        // =====================================

        $consulta = $conexion->prepare(
            "INSERT INTO carritos (id_usuario)
             VALUES (:usuario)
             ON DUPLICATE KEY UPDATE
             id_carrito = LAST_INSERT_ID(id_carrito)"
        );

        $consulta->execute([
            'usuario' => $idUsuario
        ]);

        // Obtener ID después de ejecutar INSERT
        $idCarrito = (int) $conexion->lastInsertId();

        // =====================================
        // 11. BLOQUEAR CARRITO
        // =====================================

        $consulta = $conexion->prepare(
            "SELECT id_carrito
             FROM carritos
             WHERE id_carrito = :carrito
             AND id_usuario = :usuario
             FOR UPDATE"
        );

        $consulta->execute([
            'carrito' => $idCarrito,
            'usuario' => $idUsuario
        ]);

        if (!$consulta->fetchColumn()) {
            $conexion->rollBack();

            responder(
                false,
                'No se pudo identificar el carrito.',
                404
            );
        }

        // =====================================
        // 12. BUSCAR PRODUCTO EN CARRITO
        // =====================================

        $consulta = $conexion->prepare(
            "SELECT id_detalle_carrito, cantidad
             FROM detalle_carrito
             WHERE id_carrito = :carrito
             AND id_producto = :producto
             AND id_personalizacion IS NULL
             FOR UPDATE"
        );

        $consulta->execute([
            'carrito' => $idCarrito,
            'producto' => $idProducto
        ]);

        $detalle = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($detalle) {

            // Producto existente: sumar cantidad
            $nuevaCantidad =
                (int) $detalle['cantidad'] + $cantidad;

            if ($nuevaCantidad > 99) {
                $conexion->rollBack();

                responder(
                    false,
                    'Máximo 99 unidades por producto.',
                    400
                );
            }

            $consulta = $conexion->prepare(
                "UPDATE detalle_carrito
                 SET cantidad = :cantidad
                 WHERE id_detalle_carrito = :detalle
                 AND id_carrito = :carrito"
            );

            $consulta->execute([
                'cantidad' => $nuevaCantidad,
                'detalle' => $detalle['id_detalle_carrito'],
                'carrito' => $idCarrito
            ]);

        } else {

            // Producto nuevo: insertar
            $consulta = $conexion->prepare(
                "INSERT INTO detalle_carrito
                 (id_carrito, id_producto, cantidad)
                 VALUES (:carrito, :producto, :cantidad)"
            );

            $consulta->execute([
                'carrito' => $idCarrito,
                'producto' => $idProducto,
                'cantidad' => $cantidad
            ]);
        }

        $conexion->commit();

        responder(
            true,
            'Producto agregado al carrito.'
        );
    }

    // =====================================
    // 13. SUMAR, RESTAR O ELIMINAR
    // =====================================

    $idDetalle = filter_var(
        $entrada['id_detalle_carrito'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$idDetalle || $idDetalle <= 0) {
        $conexion->rollBack();

        responder(
            false,
            'Producto del carrito no válido.',
            400
        );
    }

    // =====================================
    // 14. LOCALIZAR Y BLOQUEAR CARRITO
    // =====================================

    $consulta = $conexion->prepare(
        "SELECT id_carrito
         FROM carritos
         WHERE id_usuario = :usuario
         FOR UPDATE"
    );

    $consulta->execute([
        'usuario' => $idUsuario
    ]);

    $idCarrito = $consulta->fetchColumn();

    if (!$idCarrito) {
        $conexion->rollBack();

        responder(
            false,
            'No tienes un carrito activo.',
            404
        );
    }

    // =====================================
    // 15. LOCALIZAR DETALLE DEL CARRITO
    // =====================================

    $consulta = $conexion->prepare(
        "SELECT
            dc.id_detalle_carrito,
            dc.cantidad,
            p.disponible
         FROM detalle_carrito dc
         LEFT JOIN productos p
            ON dc.id_producto = p.id_producto
         WHERE dc.id_detalle_carrito = :detalle
         AND dc.id_carrito = :carrito
         FOR UPDATE"
    );

    $consulta->execute([
        'detalle' => $idDetalle,
        'carrito' => $idCarrito
    ]);

    $detalle = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$detalle) {
        $conexion->rollBack();

        responder(
            false,
            'Producto no encontrado en tu carrito.',
            404
        );
    }

    $cantidadActual = (int) $detalle['cantidad'];

    // =====================================
    // 16. ELIMINAR PRODUCTO
    // =====================================

    if (
        $accion === 'eliminar' ||
        ($accion === 'restar' && $cantidadActual <= 1)
    ) {

        $consulta = $conexion->prepare(
            "DELETE FROM detalle_carrito
             WHERE id_detalle_carrito = :detalle
             AND id_carrito = :carrito"
        );

        $consulta->execute([
            'detalle' => $idDetalle,
            'carrito' => $idCarrito
        ]);

        $mensaje = 'Producto eliminado del carrito.';

    } else {

        // =====================================
        // 17. SUMAR O RESTAR
        // =====================================

        if ($accion === 'sumar') {

            // Verificar disponibilidad
            if ((int) $detalle['disponible'] !== 1) {
                $conexion->rollBack();

                responder(
                    false,
                    'Producto no disponible.',
                    400
                );
            }

            // Máximo 99 unidades
            if ($cantidadActual >= 99) {
                $conexion->rollBack();

                responder(
                    false,
                    'Máximo 99 unidades por producto.',
                    400
                );
            }

            $nuevaCantidad = $cantidadActual + 1;

        } else {

            $nuevaCantidad = $cantidadActual - 1;
        }

        // =====================================
        // 18. ACTUALIZAR CANTIDAD
        // =====================================

        $consulta = $conexion->prepare(
            "UPDATE detalle_carrito
             SET cantidad = :cantidad
             WHERE id_detalle_carrito = :detalle
             AND id_carrito = :carrito"
        );

        $consulta->execute([
            'cantidad' => $nuevaCantidad,
            'detalle' => $idDetalle,
            'carrito' => $idCarrito
        ]);

        $mensaje = 'Cantidad actualizada correctamente.';
    }

    // =====================================
    // 19. GUARDAR CAMBIOS
    // =====================================

    $conexion->commit();

    responder(true, $mensaje);

} catch (PDOException $e) {

    // =====================================
    // 20. MANEJAR ERRORES
    // =====================================

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log(
        'Error en carrito: ' . $e->getMessage()
    );

    responder(
        false,
        'No se pudo actualizar el carrito.',
        500
    );
}
