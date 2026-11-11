
<?php
// =====================================
// 1. INICIAR SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================
// 2. VERIFICAR ADMINISTRADOR
// =====================================

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../iniciarSesion.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

// =====================================
// 3. ACEPTAR SOLO POST
// =====================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

// =====================================
// 4. VALIDAR TOKEN CSRF
// =====================================

if (
    empty($_SESSION['csrf_admin']) ||
    !isset($_POST['csrf_token']) ||
    !is_string($_POST['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_admin'],
        $_POST['csrf_token']
    )
) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

// =====================================
// 5. VALIDAR ID DEL PRODUCTO
// =====================================

$idProducto = filter_var(
    $_POST['id_producto'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$idProducto || $idProducto <= 0) {
    header('Location: productos.php?error=id');
    exit;
}

// =====================================
// 6. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

try {

    // =====================================
    // 7. INICIAR TRANSACCIÓN
    // =====================================

    $conexion->beginTransaction();

    // Buscar producto y bloquearlo mientras se procesa
    $consulta = $conexion->prepare(
        "SELECT id_producto, imagen
         FROM productos
         WHERE id_producto = :id_producto
         FOR UPDATE"
    );

    $consulta->execute([
        'id_producto' => $idProducto
    ]);

    $producto = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        $conexion->rollBack();
        header('Location: productos.php?error=noexiste');
        exit;
    }

    // =====================================
    // 8. COMPROBAR PEDIDOS ASOCIADOS
    // =====================================

    $consulta = $conexion->prepare(
        "SELECT COUNT(*)
         FROM detalle_pedido
         WHERE id_producto = :id_producto"
    );

    $consulta->execute([
        'id_producto' => $idProducto
    ]);

    $totalPedidos = (int) $consulta->fetchColumn();

    if ($totalPedidos > 0) {

        $conexion->rollBack();

        header('Location: productos.php?error=pedidos');
        exit;
    }

    // =====================================
    // 9. ELIMINAR PRODUCTO
    // =====================================

    $consulta = $conexion->prepare(
        "DELETE FROM productos
         WHERE id_producto = :id_producto"
    );

    $consulta->execute([
        'id_producto' => $idProducto
    ]);

    $conexion->commit();

    // =====================================
    // 10. ELIMINAR IMAGEN NO COMPARTIDA
    // =====================================

    $imagen = $producto['imagen'] ?? '';

    // Solo eliminar archivos generados por el sistema
    if (
        is_string($imagen) &&
        preg_match(
            '/^producto_[a-f0-9]{24}\.(jpg|png|webp)$/',
            $imagen
        )
    ) {

        $consulta = $conexion->prepare(
            "SELECT COUNT(*)
             FROM productos
             WHERE imagen = :imagen"
        );

        $consulta->execute([
            'imagen' => $imagen
        ]);

        $totalReferencias = (int) $consulta->fetchColumn();

        if ($totalReferencias === 0) {

            $rutaImagen = __DIR__ .
                '/../assets/img/' .
                $imagen;

            if (is_file($rutaImagen)) {
                @unlink($rutaImagen);
            }
        }
    }

    // =====================================
    // 11. REGRESAR AL LISTADO
    // =====================================

    header('Location: productos.php?eliminado=1');
    exit;

} catch (PDOException $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    header('Location: productos.php?error=eliminar');
    exit;
}
