
document.addEventListener("DOMContentLoaded", () => {

    const formulario = document.getElementById("formPersonaliza");

    if (!formulario) return;

    const campos = [
        {
            input: "imagenPortada",
            preview: "previewPortada",
            simbolo: "✳"
        },
        {
            input: "imagenContraportada",
            preview: "previewContraportada",
            simbolo: "✦"
        },
        {
            input: "imagenCD",
            preview: "previewCD",
            simbolo: "◉"
        }
    ];

    const MAX_SIZE = 5 * 1024 * 1024;

    const tiposPermitidos = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    const botonPrincipal = formulario.querySelector(
        'button[type="submit"]'
    );

    const urlsPrevias = new Map();

    function limpiarVista(campo) {
        const preview = document.getElementById(campo.preview);

        if (urlsPrevias.has(campo.input)) {
            URL.revokeObjectURL(urlsPrevias.get(campo.input));
            urlsPrevias.delete(campo.input);
        }

        preview.replaceChildren();

        const simbolo = document.createElement("span");
        simbolo.textContent = campo.simbolo;

        preview.appendChild(simbolo);
    }

    function validarImagen(archivo) {

        if (!archivo) {
            return "Selecciona una imagen.";
        }

        if (!tiposPermitidos.includes(archivo.type)) {
            return "Solo se permiten imágenes JPG, PNG o WEBP.";
        }

        if (archivo.size > MAX_SIZE) {
            return "Cada imagen debe pesar máximo 5 MB.";
        }

        return null;
    }

    campos.forEach(campo => {

        const input = document.getElementById(campo.input);
        const preview = document.getElementById(campo.preview);

        input.addEventListener("change", () => {

            limpiarVista(campo);

            const archivo = input.files[0];

            if (!archivo) return;

            const error = validarImagen(archivo);

            if (error) {
                alert(error);
                input.value = "";
                return;
            }

            const url = URL.createObjectURL(archivo);

            urlsPrevias.set(campo.input, url);

            const imagen = document.createElement("img");

            imagen.src = url;
            imagen.alt = "Vista previa de " + campo.input;

            imagen.style.width = "100%";
            imagen.style.height = "100%";
            imagen.style.objectFit = "cover";

            preview.replaceChildren(imagen);
        });
    });


    // VENTANA DE CONFIRMACIÓN

    const modal = document.createElement("div");

    modal.id = "modalPersonaliza";
    modal.className = "modal-personaliza";
    modal.hidden = true;

    modal.innerHTML = `
        <div class="modal-personaliza-contenido"
             role="dialog"
             aria-modal="true"
             aria-labelledby="tituloModalPersonaliza">

            <button type="button"
                    class="cerrar-modal-personaliza"
                    aria-label="Cerrar">
                ×
            </button>

            <h2 id="tituloModalPersonaliza">
                REVISA TU DISEÑO ✳
            </h2>

            <p>
                Confirma que los datos sean correctos
                antes de enviar tu solicitud.
            </p>

            <div class="resumen-personaliza">
                <p><strong>Diseño:</strong>
                    <span id="resumenNombre"></span>
                </p>

                <p><strong>Canción o playlist:</strong>
                    <span id="resumenCancion"></span>
                </p>
            </div>

            <div class="modal-imagenes-personaliza">
                <div>
                    <h3>PORTADA</h3>
                    <img id="resumenPortada" alt="Portada">
                </div>

                <div>
                    <h3>CONTRAPORTADA</h3>
                    <img id="resumenContraportada"
                         alt="Contraportada">
                </div>

                <div>
                    <h3>CD</h3>
                    <img id="resumenCD" alt="Diseño del CD">
                </div>
            </div>

            <p id="mensajePersonaliza"
               role="status"
               aria-live="polite"></p>

            <div class="modal-acciones-personaliza">
                <button type="button"
                        id="editarPersonaliza">
                    VOLVER A EDITAR
                </button>

                <button type="button"
                        id="confirmarPersonaliza">
                    CONFIRMAR SOLICITUD ↗
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    const cerrar = modal.querySelector(
        ".cerrar-modal-personaliza"
    );

    const editar = document.getElementById(
        "editarPersonaliza"
    );

    const confirmar = document.getElementById(
        "confirmarPersonaliza"
    );

    const mensaje = document.getElementById(
        "mensajePersonaliza"
    );

    let enviando = false;

    function cerrarModal() {
        if (enviando) return;

        modal.hidden = true;
        botonPrincipal.focus();
    }

    cerrar.addEventListener("click", cerrarModal);
    editar.addEventListener("click", cerrarModal);

    modal.addEventListener("click", evento => {
        if (evento.target === modal) {
            cerrarModal();
        }
    });

    document.addEventListener("keydown", evento => {
        if (evento.key === "Escape" && !modal.hidden) {
            cerrarModal();
        }
    });


    // REVISAR FORMULARIO

    formulario.addEventListener("submit", evento => {

        evento.preventDefault();

        if (!formulario.reportValidity()) return;

        for (const campo of campos) {

            const input = document.getElementById(campo.input);
            const error = validarImagen(input.files[0]);

            if (error) {
                alert(error);
                input.focus();
                return;
            }
        }

        document.getElementById("resumenNombre").textContent =
            document.getElementById("nombrePersonaliza").value.trim();

        document.getElementById("resumenCancion").textContent =
            document.getElementById("cancionPersonaliza").value.trim()
            || "No especificada";

        const resumenes = [
            "resumenPortada",
            "resumenContraportada",
            "resumenCD"
        ];

        campos.forEach((campo, indice) => {
            document.getElementById(resumenes[indice]).src =
                urlsPrevias.get(campo.input);
        });

        mensaje.textContent = "";
        modal.hidden = false;
        cerrar.focus();
    });


    // ENVIAR SOLICITUD

    confirmar.addEventListener("click", async () => {

        if (enviando) return;

        enviando = true;
        confirmar.disabled = true;
        editar.disabled = true;
        cerrar.disabled = true;

        confirmar.textContent = "ENVIANDO...";
        mensaje.textContent = "Guardando tu solicitud...";

        try {

            const datos = new FormData(formulario);

            const respuesta = await fetch(formulario.action, {
                method: "POST",
                body: datos,
                credentials: "same-origin",
                headers: {
                    "Accept": "application/json"
                }
            });

            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(
                    resultado.mensaje ||
                    "No se pudo guardar la solicitud."
                );
            }

            mensaje.textContent =
                resultado.mensaje ||
                "¡Tu solicitud se envió correctamente!";

            formulario.reset();

            campos.forEach(limpiarVista);

            if (resultado.csrf_token) {
                formulario.elements["csrf_token"].value =
                    resultado.csrf_token;
            }

            confirmar.textContent = "SOLICITUD ENVIADA";
            editar.textContent = "CERRAR";

            confirmar.hidden = true;

        } catch (error) {

            mensaje.textContent = error.message;
            confirmar.textContent = "INTENTAR DE NUEVO";

        } finally {

            enviando = false;
            confirmar.disabled = false;
            editar.disabled = false;
            cerrar.disabled = false;
        }
    });

});
