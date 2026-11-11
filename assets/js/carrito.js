
document.addEventListener('DOMContentLoaded', () => {

    // =====================================
    // 1. ELEMENTOS DEL CARRITO
    // =====================================

    const mensaje = document.getElementById('carritoMensaje');

    const tokenCSRF = document.querySelector(
        'meta[name="csrf-carrito"]'
    )?.content;

    let procesando = false;

    // =====================================
    // 2. FUNCIÓN PARA MODIFICAR EL CARRITO
    // =====================================

    async function modificarCarrito(accion, idDetalle) {

        if (procesando) return;

        if (!tokenCSRF) {
            mostrarError('No se encontró el token de seguridad.');
            return;
        }

        procesando = true;

        if (mensaje) {
            mensaje.textContent = '';
        }

        try {

            const respuesta = await fetch(
                '/stellar_cd/api/carrito.php',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    body: JSON.stringify({
                        accion: accion,
                        id_detalle_carrito: idDetalle,
                        csrf_token: tokenCSRF
                    })
                }
            );

            const datos = await respuesta.json();

            if (!respuesta.ok || !datos.exito) {

                throw new Error(
                    datos.mensaje ||
                    'No se pudo modificar el carrito.'
                );
            }

            // =====================================
            // 3. ACTUALIZAR CANTIDADES Y TOTAL
            // =====================================

            window.location.reload();

        } catch (error) {

            console.error('Error en el carrito:', error);

            mostrarError(error.message);

            procesando = false;
        }
    }

    // =====================================
    // 4. MOSTRAR ERRORES
    // =====================================

    function mostrarError(texto) {

        if (mensaje) {
            mensaje.textContent = texto;
        } else {
            alert(texto);
        }
    }

    // =====================================
    // 5. BOTONES PARA SUMAR CANTIDAD
    // =====================================

    document.querySelectorAll('.btn-sumar').forEach(boton => {

        boton.addEventListener('click', () => {

            modificarCarrito(
                'sumar',
                Number(boton.dataset.id)
            );

        });
    });

    // =====================================
    // 6. BOTONES PARA RESTAR CANTIDAD
    // =====================================

    document.querySelectorAll('.btn-restar').forEach(boton => {

        boton.addEventListener('click', () => {

            modificarCarrito(
                'restar',
                Number(boton.dataset.id)
            );

        });
    });

    // =====================================
    // 7. BOTONES PARA ELIMINAR PRODUCTOS
    // =====================================

    document.querySelectorAll('.carrito-eliminar').forEach(boton => {

        boton.addEventListener('click', () => {

            const confirmar = confirm(
                '¿Seguro que deseas eliminar este producto del carrito?'
            );

            if (!confirmar) {
                return;
            }

            modificarCarrito(
                'eliminar',
                Number(boton.dataset.id)
            );

        });
    });

});
