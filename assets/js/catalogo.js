
document.addEventListener('DOMContentLoaded', () => {

    const contenedor = document.getElementById('listaProductos');

    if (!contenedor) return;

    // =====================================
    // 1. DETECTAR ENTORNO
    // =====================================

    const esLocal = ['localhost', '127.0.0.1'].includes(
        window.location.hostname
    );

    const rutaBase = esLocal ? '/stellar_cd/' : '/';

    // =====================================
    // 2. CORREGIR RUTAS DE IMÁGENES
    // =====================================

    function obtenerUrlImagen(ruta) {

        if (!ruta || typeof ruta !== 'string') {
            return null;
        }

        const rutaOriginal = ruta.trim();

        if (!rutaOriginal) {
            return null;
        }

        // Permitir imágenes externas HTTPS
        if (/^https:\/\//i.test(rutaOriginal)) {
            return rutaOriginal;
        }

        // Rechazar otros esquemas y URLs externas no permitidas
        if (
            /^[a-z][a-z0-9+.-]*:/i.test(rutaOriginal) ||
            rutaOriginal.startsWith('//')
        ) {
            return null;
        }

        // Eliminar parámetros antes de normalizar
        const rutaSinDominio = rutaOriginal
            .replace(/^\/+/, '')
            .replace(/^stellar_cd\//i, '');

        // Las imágenes locales deben estar en assets/img
        if (
            !rutaSinDominio.startsWith('assets/img/') ||
            rutaSinDominio.split('/').includes('..')
        ) {
            return null;
        }

        return rutaBase + rutaSinDominio;
    }

    // =====================================
    // 3. MOSTRAR ÍCONO SI NO HAY IMAGEN
    // =====================================

    function mostrarImagenAlternativa(contenedorImagen) {

        contenedorImagen.replaceChildren();

        const icono = document.createElement('span');
        icono.textContent = '✦';
        icono.setAttribute('aria-hidden', 'true');

        contenedorImagen.appendChild(icono);
    }

    // =====================================
    // 4. AGREGAR PRODUCTOS AL CARRITO
    // =====================================

    async function agregarAlCarrito(idProducto, boton) {

        const textoOriginal = boton.textContent;

        const tokenCSRF = document.querySelector(
            'meta[name="csrf-carrito"]'
        )?.content;

        if (!tokenCSRF) {
            alert('No se encontró el token de seguridad.');
            return;
        }

        boton.disabled = true;
        boton.textContent = 'AGREGANDO...';

        try {

            const respuesta = await fetch(
                `${rutaBase}api/carrito.php`,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    credentials: 'same-origin',

                    body: JSON.stringify({
                        accion: 'agregar',
                        id_producto: idProducto,
                        cantidad: 1,
                        csrf_token: tokenCSRF
                    })
                }
            );

            const datos = await respuesta.json();

            if (!respuesta.ok || !datos.exito) {
                throw new Error(
                    datos.mensaje ||
                    'No se pudo agregar el producto.'
                );
            }

            boton.textContent = '✓ AGREGADO';

            setTimeout(() => {
                boton.textContent = textoOriginal;
                boton.disabled = false;
            }, 1500);

        } catch (error) {

            console.error('Error al agregar producto:', error);

            alert(error.message);

            boton.textContent = textoOriginal;
            boton.disabled = false;
        }
    }

    // =====================================
    // 5. CARGAR PRODUCTOS DESDE LA API
    // =====================================

    async function cargarProductos() {

        try {

            const respuesta = await fetch(
                `${rutaBase}api/productos.php`,
                {
                    credentials: 'same-origin',
                    cache: 'no-store'
                }
            );

            if (!respuesta.ok) {
                throw new Error(
                    'Error al consultar la API de productos.'
                );
            }

            const datos = await respuesta.json();

            if (!datos.exito) {
                throw new Error(
                    datos.mensaje ||
                    'La API devolvió un error.'
                );
            }

            if (!Array.isArray(datos.productos)) {
                throw new Error(
                    'La respuesta de productos no es válida.'
                );
            }

            contenedor.replaceChildren();

            if (datos.productos.length === 0) {
                contenedor.textContent =
                    'No hay productos disponibles.';
                return;
            }

            datos.productos.forEach(producto => {

                // =====================================
                // TARJETA DEL PRODUCTO
                // =====================================

                const tarjeta = document.createElement('article');
                tarjeta.className = 'tarjeta';

                // =====================================
                // IMAGEN DEL PRODUCTO
                // =====================================

                const imagenContenedor = document.createElement('div');
                imagenContenedor.className = 'imagen-producto';

                const urlImagen = obtenerUrlImagen(producto.imagen);

                if (urlImagen) {

                    const imagen = document.createElement('img');

                    imagen.src = urlImagen;
                    imagen.alt = producto.nombre || 'Producto Stellar CD';
                    imagen.loading = 'lazy';
                    imagen.decoding = 'async';

                    // Si la imagen falla, mostrar decoración
                    imagen.onerror = function () {

                        console.warn(
                            'No se pudo cargar la imagen:',
                            urlImagen
                        );

                        mostrarImagenAlternativa(imagenContenedor);
                    };

                    imagenContenedor.appendChild(imagen);

                } else {

                    mostrarImagenAlternativa(imagenContenedor);
                }

                // =====================================
                // INFORMACIÓN DEL PRODUCTO
                // =====================================

                const informacion = document.createElement('div');
                informacion.className = 'informacion';

                const categoria = document.createElement('small');
                categoria.textContent = producto.categoria ?? '';

                const nombre = document.createElement('h3');
                nombre.textContent = producto.nombre ?? '';

                const descripcion = document.createElement('p');
                descripcion.textContent =
                    producto.descripcion ?? '';

                const precio = document.createElement('strong');

                precio.textContent =
                    '$' + Number(producto.precio).toFixed(2) + ' MXN';

                // =====================================
                // BOTÓN AGREGAR AL CARRITO
                // =====================================

                const botonCarrito = document.createElement('button');

                botonCarrito.type = 'button';
                botonCarrito.className = 'boton-agregar-carrito';
                botonCarrito.textContent = '🛒 AGREGAR AL CARRITO';

                botonCarrito.addEventListener('click', () => {

                    agregarAlCarrito(
                        Number(producto.id_producto),
                        botonCarrito
                    );
                });

                // =====================================
                // CONSTRUIR TARJETA
                // =====================================

                informacion.append(
                    categoria,
                    nombre,
                    descripcion,
                    precio,
                    botonCarrito
                );

                tarjeta.append(
                    imagenContenedor,
                    informacion
                );

                contenedor.appendChild(tarjeta);
            });

        } catch (error) {

            console.error('Error al cargar productos:', error);

            contenedor.textContent =
                'No se pudieron cargar los productos.';
        }
    }

    // =====================================
    // 6. INICIAR CATÁLOGO
    // =====================================

    cargarProductos();

});
