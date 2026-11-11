
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
// 3. CONEXIÓN A MYSQL
// =====================================

require_once __DIR__ . '/../config/conexion.php';

// Token para proteger el formulario
if (empty($_SESSION['csrf_admin'])) {
    $_SESSION['csrf_admin'] = bin2hex(random_bytes(32));
}

// =====================================
// 4. CONSULTAR CATEGORÍAS
// =====================================

$consulta = $conexion->query(
    "SELECT id_categoria, nombre
     FROM categorias
     ORDER BY nombre ASC"
);

$categorias = $consulta->fetchAll(PDO::FETCH_ASSOC);

// =====================================
// 5. VARIABLES DEL FORMULARIO
// =====================================

$error = '';

$nombre = '';
$descripcion = '';
$precio = '';
$idCategoria = '';
$disponible = 1;

// =====================================
// 6. GUARDAR PRODUCTO
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $idCategoria = $_POST['id_categoria'] ?? '';
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    $imagenNombre = null;
    $imagenRuta = null;

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_admin'],
            (string) $_POST['csrf_token']
        )
    ) {
        $error = 'Solicitud no válida. Actualiza la página.';

    } elseif ($nombre === '' || mb_strlen($nombre) > 150) {

        $error = 'Ingresa un nombre válido (máximo 150 caracteres).';

    } elseif ($precio === '' ||
              !preg_match('/^\d{1,8}(\.\d{1,2})?$/', $precio) ||
              (float) $precio <= 0) {

        $error = 'Ingresa un precio válido mayor a cero.';

    } elseif (
        filter_var($idCategoria, FILTER_VALIDATE_INT) === false ||
        (int) $idCategoria <= 0
    ) {

        $error = 'Selecciona una categoría válida.';

    } else {

        // Comprobar que la categoría exista
        $consultaCategoria = $conexion->prepare(
            "SELECT COUNT(*) FROM categorias
             WHERE id_categoria = :id_categoria"
        );

        $consultaCategoria->execute([
            'id_categoria' => (int) $idCategoria
        ]);

        if (!$consultaCategoria->fetchColumn()) {
            $error = 'La categoría seleccionada no existe.';
        }
    }

    // =====================================
    // 7. VALIDAR IMAGEN
    // =====================================

    if ($error === '' && isset($_FILES['imagen'])) {

        $archivo = $_FILES['imagen'];

        if ($archivo['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($archivo['error'] !== UPLOAD_ERR_OK) {

                $error = 'No se pudo subir la imagen.';

            } elseif ($archivo['size'] > 3 * 1024 * 1024) {

                $error = 'La imagen no debe superar los 3 MB.';

            } else {

                $finfo = new finfo(FILEINFO_MIME_TYPE);

                $tipo = $finfo->file($archivo['tmp_name']);

                $tiposPermitidos = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];

                if (!isset($tiposPermitidos[$tipo])) {

                    $error = 'Solo se permiten imágenes JPG, PNG o WebP.';

                } elseif (@getimagesize($archivo['tmp_name']) === false) {

                    $error = 'El archivo no es una imagen válida.';

                } else {

                    $extension = $tiposPermitidos[$tipo];

                    $imagenNombre =
                        'producto_' .
                        bin2hex(random_bytes(12)) .
                        '.' . $extension;

                    $directorio = __DIR__ . '/../assets/img/';

                    if (!is_dir($directorio) ||
                        !is_writable($directorio)) {

                        $error = 'La carpeta de imágenes no está disponible.';

                    } else {

                        $imagenRuta = $directorio . $imagenNombre;

                        if (!move_uploaded_file(
                            $archivo['tmp_name'],
                            $imagenRuta
                        )) {

                            $error = 'No se pudo guardar la imagen.';
                        }
                    }
                }
            }
        }
    }

    // =====================================
    // 8. INSERTAR EN MYSQL
    // =====================================

    if ($error === '') {

        try {

            $sql = "INSERT INTO productos
                        (nombre, descripcion, precio,
                         imagen, id_categoria, disponible)
                    VALUES
                        (:nombre, :descripcion, :precio,
                         :imagen, :id_categoria, :disponible)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'imagen' => $imagenNombre,
                'id_categoria' => (int) $idCategoria,
                'disponible' => $disponible
            ]);

            // Evitar registros duplicados al actualizar
            header('Location: productos.php?creado=1');
            exit;

        } catch (PDOException $e) {

            // Eliminar la imagen si no se guardó el producto
            if ($imagenRuta !== null && is_file($imagenRuta)) {
                unlink($imagenRuta);
            }

            $error = 'No fue posible guardar el producto.';
        }
    }
}

// Evitar mostrar contenido no seguro
function escapar($valor) {
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

    <title>Agregar producto | Stellar CD</title>

    <link rel="stylesheet"
          href="../assets/css/style.css?v=11">

    <style>
        .admin-form-contenedor {
            max-width: 850px;
            margin: 0 auto;
            padding: 65px 25px 100px;
        }

        .admin-form-contenedor h1 {
            font-size: clamp(30px, 5vw, 48px);
            margin: 15px 0;
        }

        .admin-form-contenedor h1 span {
            color: #ff5b99;
        }

        .admin-form-intro {
            color: #c7c7d9;
            margin-bottom: 35px;
        }

        .admin-form-tarjeta {
            background: #23233a;
            border-radius: 16px;
            padding: 35px;
        }

        .admin-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .admin-campo {
            display: flex;
            flex-direction: column;
            gap: 9px;
            margin-bottom: 22px;
        }

        .admin-campo label {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #ff82ae;
        }

        .admin-campo input,
        .admin-campo textarea,
        .admin-campo select {
            width: 100%;
            padding: 14px;
            background: #151522;
            border: 1px solid #41415f;
            border-radius: 8px;
            color: white;
            font: inherit;
            box-sizing: border-box;
        }

        .admin-campo textarea {
            min-height: 120px;
            resize: vertical;
        }

        .admin-campo input:focus,
        .admin-campo textarea:focus,
        .admin-campo select:focus {
            outline: 2px solid #ff5b99;
            outline-offset: 1px;
        }

        .admin-check {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .admin-check input {
            accent-color: #ff5b99;
            width: 18px;
            height: 18px;
        }

        .admin-boton-guardar {
            padding: 16px 25px;
            background: #ff5b99;
            color: #151522;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .admin-boton-guardar:hover {
            background: #ff82ae;
        }

        .admin-boton-cancelar {
            color: #ff82ae;
            text-decoration: none;
            margin-left: 20px;
        }

        .admin-error {
            padding: 15px;
            margin-bottom: 25px;
            background: #542c42;
            color: #ffb4cc;
            border-radius: 8px;
        }

        .admin-ayuda {
            color: #c7c7d9;
            font-size: 12px;
            margin-top: 5px;
        }

        @media (max-width: 650px) {
            .admin-form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .admin-form-tarjeta {
                padding: 22px;
            }

            .admin-boton-cancelar {
                display: inline-block;
                margin-top: 20px;
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

<!-- HEADER COMPARTIDO -->
<?php require __DIR__ . '/../includes/header.php'; ?>

<main class="admin-form-contenedor">

    <span class="etiqueta">
        STELLAR CD / ADD PRODUCT
    </span>

    <h1>
        NUEVO <span>PRODUCTO. ✳</span>
    </h1>

    <p class="admin-form-intro">
        Agrega un nuevo mini CD al catálogo de Stellar CD.
    </p>

    <?php if ($error !== ''): ?>

        <div class="admin-error" role="alert">
            <?php echo escapar($error); ?>
        </div>

    <?php endif; ?>

    <form
        method="POST"
        enctype="multipart/form-data"
        class="admin-form-tarjeta">

        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo escapar($_SESSION['csrf_admin']); ?>">

        <!-- NOMBRE -->
        <div class="admin-campo">

            <label for="nombre">NOMBRE DEL PRODUCTO</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="150"
                value="<?php echo escapar($nombre); ?>"
                placeholder="Ej. Mini CD Edición Especial"
                required>

        </div>

        <!-- DESCRIPCIÓN -->
        <div class="admin-campo">

            <label for="descripcion">DESCRIPCIÓN</label>

            <textarea
                id="descripcion"
                name="descripcion"
                placeholder="Describe el producto..."><?php
                    echo escapar($descripcion);
                ?></textarea>

        </div>

        <div class="admin-form-grid">

            <!-- PRECIO -->
            <div class="admin-campo">

                <label for="precio">PRECIO (MXN)</label>

                <input
                    type="number"
                    id="precio"
                    name="precio"
                    min="0.01"
                    step="0.01"
                    value="<?php echo escapar($precio); ?>"
                    placeholder="Ej. 95.00"
                    required>

            </div>

            <!-- CATEGORÍA -->
            <div class="admin-campo">

                <label for="id_categoria">CATEGORÍA</label>

                <select
                    id="id_categoria"
                    name="id_categoria"
                    required>

                    <option value="">Selecciona una categoría</option>

                    <?php foreach ($categorias as $categoria): ?>

                        <option
                            value="<?php echo (int) $categoria['id_categoria']; ?>"
                            <?php
                                echo (string) $idCategoria ===
                                     (string) $categoria['id_categoria']
                                     ? 'selected'
                                     : '';
                            ?>>

                            <?php echo escapar($categoria['nombre']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <!-- IMAGEN -->
        <div class="admin-campo">

            <label for="imagen">IMAGEN DEL PRODUCTO</label>

            <input
                type="file"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/png,image/webp">

            <span class="admin-ayuda">
                Formatos permitidos: JPG, PNG o WebP.
                Tamaño máximo: 3 MB.
            </span>

        </div>

        <!-- DISPONIBILIDAD -->
        <div class="admin-check">

            <input
                type="checkbox"
                id="disponible"
                name="disponible"
                value="1"
                <?php echo $disponible === 1 ? 'checked' : ''; ?>>

            <label for="disponible">
                Disponible en el catálogo público
            </label>

        </div>

        <!-- BOTONES -->
        <button
            type="submit"
            class="admin-boton-guardar">

            GUARDAR PRODUCTO ↗

        </button>

        <a href="productos.php" class="admin-boton-cancelar">
            CANCELAR
        </a>

    </form>

</main>

</body>
</html>
