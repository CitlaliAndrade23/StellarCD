
<?php
// API JSON de productos - Stellar CD

// Indicar que la respuesta será JSON
header('Content-Type: application/json; charset=utf-8');

// Permitir únicamente consultas GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    http_response_code(405);

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    // Conectar con la base de datos
    require_once __DIR__ . '/../config/conexion.php';

    // Consultar productos disponibles
    $sql = "SELECT
                p.id_producto,
                p.nombre,
                p.descripcion,
                p.precio,
                p.imagen,
                c.nombre AS categoria
            FROM productos p
            INNER JOIN categorias c
                ON p.id_categoria = c.id_categoria
            WHERE p.disponible = 1
            ORDER BY p.id_producto DESC";

    $consulta = $conexion->query($sql);

    $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Preparar los datos para JSON
    foreach ($productos as &$producto) {
        $producto['id_producto'] = (int) $producto['id_producto'];
        $producto['precio'] = (float) $producto['precio'];

        if (!empty($producto['imagen'])) {
            $producto['imagen'] =
                '/stellar_cd/assets/img/' .
                rawurlencode(basename($producto['imagen']));
        } else {
            $producto['imagen'] = null;
        }
    }

    unset($producto);

    // Enviar respuesta JSON
    echo json_encode([
        'exito' => true,
        'total' => count($productos),
        'productos' => $productos
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al consultar los productos'
    ], JSON_UNESCAPED_UNICODE);
}
