document.addEventListener('DOMContentLoaded', () => {

    // Mapeo de elementos del DOM (initial mapping, some might be null if not present at load)
    const elementos = {
        modalPQRSF: document.getElementById('pqrsfModal'),
        formPQRSF: document.getElementById('formPQRSF'),
        // These will be retrieved directly in functions that use them
        // nombres: document.getElementById('nombres'),
        // tipoPQRSF: document.getElementById('tipoPQRSF'),
        // contacto_solicitante: document.getElementById('contacto_solicitante'),
        // descripcion: document.getElementById('descripcion'),
        // pqrsfAboutCategory: document.getElementById('destinatarioPQRSF'),
        pqrsfAboutIdContainer: document.getElementById('destinatario-especifico-container'),
        // pqrsfAboutId: document.getElementById('destinatarioEspecificoPQRSF'),
        archivoAdjunto: document.getElementById('archivoAdjunto'),
        nombreArchivo: document.getElementById('nombreArchivo'),
        cuerpoTabla: document.getElementById('cuerpoTabla'),
        politicaDatos: document.getElementById('politicaDatos'), // Not used in this context, but kept for completeness
        filtroTipo: document.getElementById('filtroTipo'),
        filtroEstado: document.getElementById('filtroEstado'),
        buscarInput: document.getElementById('buscarInput'),
        btnFiltrar: document.getElementById('btnFiltrar'),
        modalVerPQRSF: document.getElementById('modalVerPQRSF'),
        verId: document.getElementById('verId'),
        verTipo: document.getElementById('verTipo'),
        verDescripcion: document.getElementById('verDescripcion'),
        verPqrsfAboutCategory: document.getElementById('verDestinatario'),
        verFecha: document.getElementById('verFecha'),
        verEstado: document.getElementById('verEstado'),
        verArchivoAdjunto: document.getElementById('verArchivoAdjunto'),
        pqrsfIdEditar: document.getElementById('pqrsfIdEditar'), // This should be present now
        aceptarTerminos: document.getElementById('aceptarTerminos') // This should be present now
    };

    let datosPQRSF = [];

    // Función para cargar usuarios por rol y rellenar el select de destinatario específico
    async function cargarDestinatariosEspecificos(rol) {
        const pqrsfAboutId = document.getElementById('destinatarioEspecificoPQRSF');
        if (!pqrsfAboutId) {
            console.error('elementos.pqrsfAboutId no encontrado en cargarDestinatariosEspecificos.');
            return;
        }
        pqrsfAboutId.innerHTML = '<option value="">Cargando...</option>';
        try {
            const response = await fetch(`../php/get_usuarios.php?rol=${rol.toLowerCase()}`);
            const usuarios = await response.json();

            pqrsfAboutId.innerHTML = '<option value="">Seleccione...</option>';
            if (usuarios.length > 0) {
                usuarios.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id_log;
                    option.textContent = `${user.nombre} ${user.apellido}`.trim();
                    pqrsfAboutId.appendChild(option);
                });
            } else {
                pqrsfAboutId.innerHTML = '<option value="">No se encontraron usuarios</option>';
            }
        } catch (error) {
            console.error('Error al cargar destinatarios específicos:', error);
            pqrsfAboutId.innerHTML = '<option value="">Error al cargar</option>';
        }
    }

    // Evento para el cambio en el select de destinatario principal
    const pqrsfAboutCategory = document.getElementById('destinatarioPQRSF');
    if (pqrsfAboutCategory) {
        pqrsfAboutCategory.addEventListener('change', function() {
            const selectedValue = this.value;
            const pqrsfAboutIdContainer = document.getElementById('destinatario-especifico-container');
            if (selectedValue === 'Profesor' || selectedValue === 'Estudiante') {
                if (pqrsfAboutIdContainer) pqrsfAboutIdContainer.style.display = 'block';
                cargarDestinatariosEspecificos(selectedValue);
            } else {
                if (pqrsfAboutIdContainer) pqrsfAboutIdContainer.style.display = 'none';
                const pqrsfAboutId = document.getElementById('destinatarioEspecificoPQRSF');
                if (pqrsfAboutId) pqrsfAboutId.innerHTML = '<option value=""></option>';
            }
        });
    }

    // Cargar PQRSF desde la base de datos con filtros
    function cargarPQRSF(filtros = {}) {
        const params = new URLSearchParams();
        if (filtros.tipo) {
            params.append('tipo', filtros.tipo);
        }
        if (filtros.estado) {
            params.append('estado', filtros.estado);
        }
        if (filtros.search) {
            params.append('search', filtros.search);
        }

        fetch(`../api/get_pqrsf.php?${params.toString()}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    datosPQRSF = data.data;
                } else {
                    alert(data.message);
                    datosPQRSF = [];
                }
                renderizarTabla();
            })
            .catch(err => {
                console.error("Error al cargar PQRSF:", err);
                alert("Hubo un error de comunicación al cargar las PQRSF.");
            });
    }

    // Guardar o actualizar PQRSF en la base de datos
    function guardarPQRSF(e) {
        e.preventDefault();

        // Re-obtener los elementos del formulario justo antes de usarlos
        const nombres = document.getElementById('nombres');
        const tipoPQRSF = document.getElementById('tipoPQRSF');
        const contacto_solicitante = document.getElementById('contacto_solicitante');
        const descripcion = document.getElementById('descripcion');
        const pqrsfAboutCategory = document.getElementById('destinatarioPQRSF');
        const pqrsfAboutIdContainer = document.getElementById('destinatario-especifico-container');
        const pqrsfAboutId = document.getElementById('destinatarioEspecificoPQRSF');
        const archivoAdjunto = document.getElementById('archivoAdjunto');
        const pqrsfIdEditar = document.getElementById('pqrsfIdEditar');
        const aceptarTerminos = document.getElementById('aceptarTerminos');

        if (!validarFormulario()) {
            return;
        }

        const formData = new FormData();
        formData.append('tipo', tipoPQRSF.value);
        formData.append('descripcion', descripcion.value.trim());
        formData.append('nombre_solicitante', nombres.value.trim());
        formData.append('contacto_solicitante', contacto_solicitante.value.trim());
        formData.append('pqrsf_about_category', pqrsfAboutCategory.value);

        // Añadir pqrsf_about_id si el campo específico está visible y tiene valor
        if (pqrsfAboutIdContainer && pqrsfAboutIdContainer.style.display !== 'none' && pqrsfAboutId && pqrsfAboutId.value) {
            formData.append('pqrsf_about_id', pqrsfAboutId.value);
        }

        if (pqrsfIdEditar && pqrsfIdEditar.value) {
            formData.append('id_pqrsf', pqrsfIdEditar.value);
        }

        // Adjuntar archivo si existe
        if (archivoAdjunto && archivoAdjunto.files.length > 0) {
            formData.append('archivo_adjunto', archivoAdjunto.files[0]);
        }

        fetch(`../api/save_pqrsf.php`, {
            method: 'POST',
            body: formData // No se establece Content-Type, el navegador lo hace automáticamente
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                cerrarModal(elementos.modalPQRSF);
                cargarPQRSF(); // Recargar sin filtros
                if (elementos.formPQRSF) elementos.formPQRSF.reset();
                if (elementos.pqrsfIdEditar) elementos.pqrsfIdEditar.value = ''; // Asegurarse de que no haya ID de edición
                const tituloModalPQRSF = document.getElementById('tituloModalPQRSF');
                if (tituloModalPQRSF) tituloModalPQRSF.textContent = 'NUEVA PQRSF'; // Restaurar título
                // Ocultar y limpiar el select de destinatario específico
                if (elementos.pqrsfAboutIdContainer) elementos.pqrsfAboutIdContainer.style.display = 'none';
                if (elementos.pqrsfAboutId) elementos.pqrsfAboutId.innerHTML = '<option value=""></option>';
                if (elementos.nombreArchivo) elementos.nombreArchivo.textContent = 'Ningún archivo seleccionado';
            } else {
                console.error('Error al guardar/actualizar la PQRSF:', resp.message);
                alert('Error al guardar/actualizar la PQRSF: ' + (resp.message || ''));
            }
        })
        .catch(err => {
            console.error("Error al guardar/actualizar PQRSF (catch):", err);
            alert("Hubo un error de comunicación al guardar/actualizar la PQRSF.");
        });
    }

    // Eliminar PQRSF de la base de datos
    function eliminarPQRSF(id) {
        if (!confirm('¿Seguro que deseas eliminar esta PQRSF?')) return;
        fetch('../api/delete_pqrsf.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_pqrsf: id })
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                cargarPQRSF(); // Recargar sin filtros
            } else {
                console.error('No se pudo eliminar la PQRSF:', resp.message);
                alert('No se pudo eliminar la PQRSF: ' + (resp.message || ''));
            }
        })
        .catch(err => {
            console.error("Error al eliminar PQRSF:", err);
            alert("Hubo un error de comunicación al eliminar la PQRSF.");
        });
    }

    // Función para abrir el modal de edición
    window.editarPQRSF = async function(id) {
        try {
            const response = await fetch(`../api/get_pqrsf.php?id_pqrsf=${id}`);
            const data = await response.json();

            if (data.success && data.data.length > 0) {
                const pqrsf = data.data[0];
                const pqrsfIdEditar = document.getElementById('pqrsfIdEditar');
                if (pqrsfIdEditar) pqrsfIdEditar.value = pqrsf.id_pqrsf;
                
                const nombres = document.getElementById('nombres');
                if (nombres) nombres.value = pqrsf.nombre_solicitante;

                const tipoPQRSF = document.getElementById('tipoPQRSF');
                if (tipoPQRSF) tipoPQRSF.value = pqrsf.tipo;

                const contacto_solicitante = document.getElementById('contacto_solicitante');
                if (contacto_solicitante) contacto_solicitante.value = pqrsf.contacto_solicitante;

                const descripcion = document.getElementById('descripcion');
                if (descripcion) descripcion.value = pqrsf.descripcion;

                const pqrsfAboutCategory = document.getElementById('destinatarioPQRSF');
                if (pqrsfAboutCategory) pqrsfAboutCategory.value = pqrsf.pqrsf_about_category;

                const pqrsfAboutIdContainer = document.getElementById('destinatario-especifico-container');
                const pqrsfAboutId = document.getElementById('destinatarioEspecificoPQRSF');

                // Si hay un destinatario específico, cargar y mostrar el select
                if (pqrsf.pqrsf_about_id && (pqrsf.pqrsf_about_category === 'Profesor' || pqrsf.pqrsf_about_category === 'Estudiante')) {
                    if (pqrsfAboutIdContainer) pqrsfAboutIdContainer.style.display = 'block';
                    await cargarDestinatariosEspecificos(pqrsf.pqrsf_about_category);
                    if (pqrsfAboutId) pqrsfAboutId.value = pqrsf.pqrsf_about_id;
                } else {
                    if (pqrsfAboutIdContainer) pqrsfAboutIdContainer.style.display = 'none';
                    if (pqrsfAboutId) pqrsfAboutId.innerHTML = '<option value=""></option>';
                }

                const tituloModalPQRSF = document.getElementById('tituloModalPQRSF');
                if (tituloModalPQRSF) tituloModalPQRSF.textContent = 'EDITAR PQRSF';
                
                if (elementos.modalPQRSF) elementos.modalPQRSF.style.display = 'block';
            } else {
                alert(data.message || 'PQRSF no encontrada para edición.');
            }
        } catch (err) {
            console.error("Error al obtener PQRSF para edición:", err);
            alert("Hubo un error de comunicación al obtener la PQRSF para ver detalles.");
        }
    }

    // Función para abrir el modal de ver detalles
    window.verDetalles = function(id) {
        fetch(`../api/get_pqrsf.php?id_pqrsf=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const pqrsf = data.data[0];
                    if (elementos.verId) elementos.verId.textContent = pqrsf.id_pqrsf;
                    if (elementos.verTipo) elementos.verTipo.textContent = pqrsf.tipo;
                    if (elementos.verDescripcion) elementos.verDescripcion.textContent = pqrsf.descripcion;
                    // Mostrar el nombre del destinatario si existe, si no, la categoría.
                    if (elementos.verPqrsfAboutCategory) elementos.verPqrsfAboutCategory.textContent = pqrsf.destinatario_nombre && pqrsf.destinatario_nombre !== 'N/A' ? `${pqrsf.pqrsf_about_category} - ${pqrsf.destinatario_nombre}` : pqrsf.pqrsf_about_category;
                    if (elementos.verFecha) elementos.verFecha.textContent = pqrsf.fecha_creacion;
                    if (elementos.verEstado) elementos.verEstado.textContent = pqrsf.estado;

                    // Mostrar el enlace del archivo adjunto si existe
                    if (pqrsf.archivo_adjunto && elementos.verArchivoAdjunto) {
                        elementos.verArchivoAdjunto.href = `../uploads/${pqrsf.archivo_adjunto}`;
                        elementos.verArchivoAdjunto.textContent = pqrsf.archivo_adjunto;
                        elementos.verArchivoAdjunto.style.display = 'block';
                    } else if (elementos.verArchivoAdjunto) {
                        elementos.verArchivoAdjunto.style.display = 'none';
                        elementos.verArchivoAdjunto.textContent = '';
                        elementos.verArchivoAdjunto.href = '#';
                    }

                    if (elementos.modalVerPQRSF) elementos.modalVerPQRSF.style.display = 'block';
                } else {
                    alert(data.message || 'PQRSF no encontrada para ver detalles.');
                }
            })
            .catch(err => {
                console.error("Error al obtener PQRSF para ver detalles:", err);
                alert("Hubo un error de comunicación al obtener la PQRSF para ver detalles.");
            });
    }

    // Renderizar la tabla de PQRSF
    function renderizarTabla() {
        if (!elementos.cuerpoTabla) return;
        elementos.cuerpoTabla.innerHTML = '';
        if (!datosPQRSF || datosPQRSF.length === 0) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 6; // Ajustado al número de columnas del HTML
            td.textContent = 'No se encontraron PQRSF con los filtros seleccionados.';
            td.style.textAlign = 'center';
            tr.appendChild(td);
            elementos.cuerpoTabla.appendChild(tr);
            return;
        }
        datosPQRSF.forEach(pqrsf => {
            const tr = document.createElement('tr');
            // Mostrar el nombre del destinatario si está disponible, si no, la categoría.
            const destinatarioTexto = pqrsf.destinatario_nombre && pqrsf.destinatario_nombre !== 'N/A' ? pqrsf.destinatario_nombre : pqrsf.pqrsf_about_category;

            tr.innerHTML = `
                <td data-label="Tipo">${pqrsf.tipo}</td>
                <td data-label="Descripción">${pqrsf.descripcion.substring(0, 50)}...</td>
                <td data-label="Sobre">${destinatarioTexto || ''}</td>
                <td data-label="Fecha Envío">${pqrsf.fecha_creacion || ''}</td>
                <td data-label="Estado">${pqrsf.estado || ''}</td>
                <td data-label="Acciones">
                    <button style="padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background-color 0.3s ease; color: #fff; margin-right: 5px; background-color: #007bff;" class="btn-ver" onclick="verDetalles(${pqrsf.id_pqrsf})">Ver</button>
                    <button style="padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background-color 0.3s ease; color: #fff; margin-right: 5px; background-color: #28a745;" class="btn-editar" onclick="editarPQRSF(${pqrsf.id_pqrsf})">Editar</button>
                    <button style="padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: background-color 0.3s ease; color: #fff; margin-right: 5px; background-color: #dc3545;" class="btn-eliminar" onclick="eliminarPQRSF(${pqrsf.id_pqrsf})">Eliminar</button>
                </td>
            `;
            elementos.cuerpoTabla.appendChild(tr);
        });
    }

    // Validación básica del formulario
    function validarFormulario() {
        // Re-obtener los elementos del formulario justo antes de usarlos
        const nombres = document.getElementById('nombres');
        const tipoPQRSF = document.getElementById('tipoPQRSF');
        const contacto_solicitante = document.getElementById('contacto_solicitante');
        const descripcion = document.getElementById('descripcion');
        const pqrsfAboutCategory = document.getElementById('destinatarioPQRSF');
        const pqrsfAboutIdContainer = document.getElementById('destinatario-especifico-container');
        const pqrsfAboutId = document.getElementById('destinatarioEspecificoPQRSF');
        const aceptarTerminos = document.getElementById('aceptarTerminos');

        if (!nombres || !nombres.value.trim() ||
            !tipoPQRSF || !tipoPQRSF.value ||
            !contacto_solicitante || !contacto_solicitante.value.trim() ||
            !descripcion || !descripcion.value.trim() ||
            !pqrsfAboutCategory || !pqrsfAboutCategory.value) {
            alert('Por favor, completa todos los campos obligatorios.');
            return false; 
        }
        // Validar destinatario específico si el campo está visible
        if (pqrsfAboutIdContainer && pqrsfAboutIdContainer.style.display !== 'none' && (!pqrsfAboutId || !pqrsfAboutId.value)) {
            alert('Por favor, selecciona un destinatario específico.');
            return false;
        }
        // Validar que se acepten los términos y condiciones
        if (!aceptarTerminos || !aceptarTerminos.checked) {
            alert('Debes aceptar los Términos y Condiciones para enviar la PQRSF.');
            return false;
        }
        return true;
    }

    // Cerrar modal
    function cerrarModal(modal) {
        if (modal) modal.style.display = 'none';
    }

    // Mostrar el modal al hacer clic en el botón "Nueva PQRSF"
    const btnAbrirModal = document.getElementById('abrirModal');
    if (btnAbrirModal && elementos.modalPQRSF) {
        btnAbrirModal.onclick = () => {
            elementos.modalPQRSF.style.display = 'block';
            if (elementos.formPQRSF) elementos.formPQRSF.reset(); // Limpiar formulario al abrir para nueva PQRSF
            if (elementos.pqrsfIdEditar) elementos.pqrsfIdEditar.value = ''; // Asegurarse de que no haya ID de edición
            const tituloModalPQRSF = document.getElementById('tituloModalPQRSF');
            if (tituloModalPQRSF) tituloModalPQRSF.textContent = 'NUEVA PQRSF'; // Restaurar título
            // Ocultar y limpiar el select de destinatario específico
            if (elementos.pqrsfAboutIdContainer) elementos.pqrsfAboutIdContainer.style.display = 'none';
            if (elementos.pqrsfAboutId) elementos.pqrsfAboutId.innerHTML = '<option value=""></option>';
            if (elementos.nombreArchivo) elementos.nombreArchivo.textContent = 'Ningún archivo seleccionado'; // Limpiar nombre de archivo
            // Restablecer el estado del checkbox y el botón de guardar
            if (elementos.aceptarTerminos) {
                elementos.aceptarTerminos.checked = false;
            }
            const submitButton = document.getElementById('btn-guardar-pqrsf');
            if (submitButton) {
                submitButton.disabled = true;
            }
        };
    }

    // Cerrar el modal al hacer clic en el botón "Cancelar"
    const btnCancelarModal = document.getElementById('cancelarModalPQRSF');
    if (btnCancelarModal && elementos.modalPQRSF) {
        btnCancelarModal.onclick = () => {
            cerrarModal(elementos.modalPQRSF);
            if (elementos.formPQRSF) elementos.formPQRSF.reset();
            if (elementos.pqrsfIdEditar) elementos.pqrsfIdEditar.value = '';
            const tituloModalPQRSF = document.getElementById('tituloModalPQRSF');
            if (tituloModalPQRSF) tituloModalPQRSF.textContent = 'NUEVA PQRSF';
            // Ocultar y limpiar el select de destinatario específico
            if (elementos.pqrsfAboutIdContainer) elementos.pqrsfAboutIdContainer.style.display = 'none';
            if (elementos.pqrsfAboutId) elementos.pqrsfAboutId.innerHTML = '<option value=""></option>';
            if (elementos.nombreArchivo) elementos.nombreArchivo.textContent = 'Ningún archivo seleccionado'; // Limpiar nombre de archivo
            // Restablecer el estado del checkbox y el botón de guardar
            if (elementos.aceptarTerminos) {
                elementos.aceptarTerminos.checked = false;
            }
            const submitButton = document.getElementById('btn-guardar-pqrsf');
            if (submitButton) {
                submitButton.disabled = true;
            }
        };
    }

    // Cerrar el modal si se hace clic fuera del contenido
    window.addEventListener('click', function(event) {
        if (event.target === elementos.modalPQRSF) {
            cerrarModal(elementos.modalPQRSF);
            if (elementos.formPQRSF) elementos.formPQRSF.reset();
            if (elementos.pqrsfIdEditar) elementos.pqrsfIdEditar.value = '';
            const tituloModalPQRSF = document.getElementById('tituloModalPQRSF');
            if (tituloModalPQRSF) tituloModalPQRSF.textContent = 'NUEVA PQRSF';
            // Ocultar y limpiar el select de destinatario específico
            if (elementos.pqrsfAboutIdContainer) elementos.pqrsfAboutIdContainer.style.display = 'none';
            if (elementos.pqrsfAboutId) elementos.pqrsfAboutId.innerHTML = '<option value=""></option>';
            if (elementos.nombreArchivo) elementos.nombreArchivo.textContent = 'Ningún archivo seleccionado'; // Limpiar nombre de archivo
            // Restablecer el estado del checkbox y el botón de guardar
            if (elementos.aceptarTerminos) {
                elementos.aceptarTerminos.checked = false;
            }
            const submitButton = document.getElementById('btn-guardar-pqrsf');
            if (submitButton) {
                submitButton.disabled = true;
            }
        }
    });

    // Cerrar modal de ver detalles
    if (elementos.modalVerPQRSF) {
        window.addEventListener('click', function(event) {
            if (event.target === elementos.modalVerPQRSF) {
                elementos.modalVerPQRSF.style.display = 'none';
            }
        });
    }

    // Configuración de eventos
    if (elementos.formPQRSF) {
        elementos.formPQRSF.onsubmit = guardarPQRSF;
    }

    // Evento para el botón de filtrar
    if (elementos.btnFiltrar) {
        elementos.btnFiltrar.addEventListener('click', () => {
            const filtros = {
                tipo: elementos.filtroTipo.value,
                estado: elementos.filtroEstado.value,
                search: elementos.buscarInput.value.trim()
            };
            cargarPQRSF(filtros);
        });
    }

    // Manejar la selección de archivo
    if (elementos.archivoAdjunto) {
        elementos.archivoAdjunto.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                elementos.nombreArchivo.textContent = this.files[0].name;
            } else {
                elementos.nombreArchivo.textContent = 'Ningún archivo seleccionado';
            }
        });
    }

    // Hacer la función eliminarPQRSF global para los botones
    window.eliminarPQRSF = eliminarPQRSF;
    window.editarPQRSF = editarPQRSF; // Hacer la función editarPQRSF global
    window.verDetalles = verDetalles; // Hacer la función verDetalles global

    // Inicializar
    cargarPQRSF();

    // Lógica del checkbox aceptarTerminos y el botón de guardar
    const submitButton = document.getElementById('btn-guardar-pqrsf');
    const aceptarTerminosCheckbox = document.getElementById('aceptarTerminos'); // Get it directly here
    if (aceptarTerminosCheckbox && submitButton) {
        aceptarTerminosCheckbox.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
        });
        // Establecer el estado inicial del botón al cargar la página
        submitButton.disabled = !aceptarTerminosCheckbox.checked;
    } else {
        console.error('Checkbox aceptarTerminos o submitButton no encontrados.');
    }

    
});