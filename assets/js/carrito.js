
document.addEventListener('DOMContentLoaded', () => {

    // =====================================
    // 1. ELEMENTOS DEL CARRITO
    // =====================================

    const mensaje = document.getElementById('carritoMensaje');
    const btnConfirmar = document.getElementById('btnConfirmarPedido');

    const tokenCSRF = document.querySelector(
        'meta[name="csrf-carrito"]'
    )?.content;

    // Detectar XAMPP o InfinityFree
    const esLocal = ['localhost', '127.0.0.1'].includes(
        window.location.hostname
    );

    const rutaBase = esLocal ? '/stellar_cd/' : '/';

    let procesando = false;

    // =====================================
    // 2. MOSTRAR MENSAJES
    // =====================================

    function mostrarMensaje(texto, esError = true) {

        if (mensaje) {
            mensaje.textContent = texto;
            mensaje.style.color = esError
                ? '#ffb4cc'
                : '#a8f0c6';
        } else if (texto) {
            alert(texto);
        }
    }

    // =====================================
    // 3. MODIFICAR CARRITO
    // =====================================

    async function modificarCarrito(accion, idDetalle) {

        if (procesando) return;

        if (!tokenCSRF) {
            mostrarMensaje('No se encontró el token de seguridad.');
            return;
        }

        if (!Number.isInteger(idDetalle) || idDetalle <= 0) {
            mostrarMensaje('El producto seleccionado no es válido.');
            return;
        }

        procesando = true;
        mostrarMensaje('');

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

            window.location.reload();

        } catch (error) {

            console.error('Error en el carrito:', error);

            mostrarMensaje(error.message);

            procesando = false;
        }
    }

    // =====================================
    // 4. SUMAR PRODUCTOS
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
    // 5. RESTAR PRODUCTOS
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
    // 6. ELIMINAR PRODUCTOS
    // =====================================

    document.querySelectorAll('.carrito-eliminar').forEach(boton => {

        boton.addEventListener('click', () => {

            const confirmar = confirm(
                '¿Seguro que deseas eliminar este producto del carrito?'
            );

            if (!confirmar) return;

            modificarCarrito(
                'eliminar',
                Number(boton.dataset.id)
            );

        });
    });

    // =====================================
    // 7. CONFIRMAR PEDIDO
    // =====================================

    if (btnConfirmar) {

        btnConfirmar.addEventListener('click', async () => {

            if (procesando) return;

            if (!tokenCSRF) {
                mostrarMensaje('No se encontró el token de seguridad.');
                return;
            }

            const confirmar = confirm(
                '¿Deseas confirmar tu pedido de StellarCD?'
            );

            if (!confirmar) return;

            procesando = true;

            btnConfirmar.disabled = true;
            btnConfirmar.textContent = 'PROCESANDO PEDIDO...';

            mostrarMensaje('');

            try {

                const respuesta = await fetch(
                    `${rutaBase}api/confirmar_pedido.php`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            csrf_token: tokenCSRF
                        })
                    }
                );

                const datos = await respuesta.json();

                if (!respuesta.ok || !datos.exito) {
                    throw new Error(
                        datos.mensaje ||
                        'No se pudo confirmar el pedido.'
                    );
                }

                mostrarMensaje(
                    `¡Pedido #${datos.id_pedido} registrado correctamente!`,
                    false
                );

                btnConfirmar.textContent = 'PEDIDO REGISTRADO';

                setTimeout(() => {
                    window.location.reload();
                }, 2000);

            } catch (error) {

                console.error('Error al confirmar pedido:', error);

                mostrarMensaje(error.message);

                btnConfirmar.disabled = false;
                btnConfirmar.textContent = 'CONFIRMAR PEDIDO';

                procesando = false;
            }

        });
    }

});
