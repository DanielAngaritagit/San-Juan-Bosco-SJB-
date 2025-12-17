document.addEventListener('DOMContentLoaded', function () {
    const correoButton = document.getElementById('Correo');
    const correoPanel = document.getElementById('correo-panel');
    const notificationsButton = document.getElementById('notifications-button');
    const notificationsPanel = document.getElementById('notifications-panel');

    // Mostrar/ocultar panel de correo
    correoButton.addEventListener('click', function(event) {
        event.stopPropagation();
        correoPanel.style.display = correoPanel.style.display === 'block' ? 'none' : 'block';
        notificationsPanel.style.display = 'none';
    });

    // Mostrar/ocultar panel de notificaciones
    notificationsButton.addEventListener('click', function(event) {
        event.stopPropagation();
        notificationsPanel.style.display = notificationsPanel.style.display === 'block' ? 'none' : 'block';
        correoPanel.style.display = 'none';
    });

    // Ocultar paneles al hacer clic fuera de ellos
    document.addEventListener('click', function() {
        correoPanel.style.display = 'none';
        notificationsPanel.style.display = 'none';
    });

    // Evitar que los paneles se cierren al hacer clic dentro de ellos
    correoPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    notificationsPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Cargar notificaciones
    function loadNotifications() {
        fetch('../api/get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationsPanel.innerHTML = data.data.map(notif => `
                        <div class="panel-item">
                            <p>${notif.mensaje}</p>
                            <small>${new Date(notif.fecha).toLocaleString()}</small>
                        </div>
                    `).join('');

                    document.getElementById('notification-count').textContent = data.data.length;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar mensajes de correo
    function loadCorreo() {
        fetch('../api/get_correo.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    correoPanel.innerHTML = data.data.map(msg => `
                        <div class="panel-item">
                            <p>${msg.mensaje}</p>
                            <small>${new Date(msg.fecha).toLocaleString()}</small>
                        </div>
                    `).join('');

                    document.getElementById('correo-count').textContent = data.data.length;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar notificaciones y mensajes al abrir los paneles
    notificationsButton.addEventListener('click', loadNotifications);
    correoButton.addEventListener('click', loadCorreo);

    // Cargar notificaciones y mensajes al cargar la página
    loadNotifications();
    loadCorreo();
});

document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const contentContainer = document.querySelector('.content-container');

    // Validación inicial
    if (!menuToggle || !menuContainer) {
        console.error('Error: Elementos del menú no encontrados');
        return;
    }

    // Abrir/cerrar menú móvil
    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menuContainer.classList.toggle('active');
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (menuContainer.classList.contains('active') && 
            !e.target.closest('#menu-container') && 
            !e.target.closest('#menu-toggle')) {
            menuContainer.classList.remove('active');
        }
    });

    // Resetear menú en pantallas grandes
    window.addEventListener('resize', () => {
        if (window.innerWidth > 991) {
            menuContainer.classList.remove('active');
        }
    });

    // Manejo del modal
    const modal = document.getElementById('estudiantesModal');
    const closeModal = document.querySelector('.close-modal');
    const gradoSeleccionadoSpan = document.getElementById('gradoSeleccionado');
    const listaEstudiantesTbody = document.getElementById('listaEstudiantes');
    const modalTitulo = document.getElementById('modalTitulo');
    let currentStudentsData = []; // Para almacenar los datos de los estudiantes cargados

    // Configurar eventos para las cards
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('click', function() {
            const grado = this.getAttribute('data-grado');
            mostrarEstudiantes(grado);
        });
        
        card.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                const grado = this.getAttribute('data-grado');
                mostrarEstudiantes(grado);
            }
        });
    });

    // Función para mostrar estudiantes en el modal
    async function mostrarEstudiantes(grado) {
        if(!['preescolar', 'primaria', 'secundaria'].includes(grado)) {
            console.error('Grado no válido');
            return;
        }
        
        const titulo = grado.charAt(0).toUpperCase() + grado.slice(1);
        gradoSeleccionadoSpan.textContent = titulo;
        modalTitulo.textContent = `Estudiantes de ${titulo}`;
        
        // Mostrar loading
        listaEstudiantesTbody.innerHTML = '<tr><td colspan="4" class="loading">Cargando estudiantes...</td></tr>';
        
        // Mostrar modal
        modal.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        
        try {
            const response = await fetch(`../api/get_students_by_grade.php?grade_type=${grado}`);
            const data = await response.json();
            
            if(data.success) {
                currentStudentsData = data.data; // Guardar los datos para la exportación
                listaEstudiantesTbody.innerHTML = ''; // Limpiar lista anterior
                
                if(currentStudentsData.length === 0) {
                    listaEstudiantesTbody.innerHTML = '<tr><td colspan="4">No hay estudiantes registrados en este grado</td></tr>';
                    return;
                }
                
                // Agregar estudiantes a la tabla
                currentStudentsData.forEach(estudiante => {
                    const estudianteRow = document.createElement('tr');
                    estudianteRow.innerHTML = `
                        <td>${estudiante.id_estud}</td>
                        <td>${estudiante.grado_numero || 'N/A'}${estudiante.letra_seccion || ''}</td>
                        <td>${estudiante.nombres} ${estudiante.apellido1} ${estudiante.apellido2 || ''}</td>
                        <td class="action-buttons">
                            <button class="export-student" data-id="${estudiante.id_estud}" title="Exportar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293V6.5z"/>
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                </svg>
                            </button>
                            <button class="view-more" data-id="${estudiante.id_estud}" title="Ver más">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8a13 13 0 0 1-1.66 2.043C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                            </button>
                        </td>
                    `;
                    listaEstudiantesTbody.appendChild(estudianteRow);
                });
                
                // Configurar eventos para los botones
                setupExportButtons();
                
                // Mover el foco al modal
                modal.focus();
            } else {
                console.error('Error al cargar estudiantes:', data.message);
                listaEstudiantesTbody.innerHTML = `<tr><td colspan="4" class="error">Error al cargar los estudiantes: ${data.message}</td></tr>`;
            }
        } catch(error) {
            console.error('Error de red al cargar estudiantes:', error);
            listaEstudiantesTbody.innerHTML = `<tr><td colspan="4" class="error">Error de red al cargar los estudiantes: ${error.message}</td></tr>`;
        }
    }

    function setupExportButtons() {
        // Exportar estudiante individual
        document.querySelectorAll('.export-student').forEach(btn => {
            btn.addEventListener('click', async function() {
                const studentId = this.getAttribute('data-id');
                if(!studentId) return;
                
                this.disabled = true;
                const originalHTML = this.innerHTML;
                this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><rect x="0" y="0" width="16" height="16"/></svg> Exportando...`;
                
                try {
                    // Obtener datos del estudiante específico para exportar
                    const studentToExport = currentStudentsData.find(s => s.id_estud == studentId);
                    if (!studentToExport) {
                        alert('Estudiante no encontrado para exportar.');
                        return;
                    }

                    // Simular exportación individual (puedes extender esto para elegir formato)
                    const response = await fetch(`../api/export_students.php?format=pdf`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `data=${encodeURIComponent(JSON.stringify([studentToExport]))}&grade=${encodeURIComponent(gradoSeleccionadoSpan.textContent)}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert(`Estudiante ${studentId} exportado correctamente (${result.filename})`);
                        // Aquí podrías redirigir a result.download_url si el backend genera un archivo real
                    } else {
                        alert(`Error al exportar estudiante: ${result.message}`);
                    }

                } catch (error) {
                    console.error('Error al exportar estudiante:', error);
                    alert(`Error de red al exportar estudiante: ${error.message}`);
                } finally {
                    this.disabled = false;
                    this.innerHTML = originalHTML;
                }
            });
        });
        
        // Exportar todo el listado
        const exportAllBtn = document.getElementById('exportAll');
        const exportOptions = document.querySelector('.export-options');
        
        exportOptions.style.display = 'none';
        
        exportAllBtn.addEventListener('click', function() {
            exportOptions.style.display = exportOptions.style.display === 'none' ? 'flex' : 'none';
        });
        
        // Opciones de exportación
        document.querySelectorAll('.export-options button').forEach(btn => {
            btn.addEventListener('click', async function() {
                const format = this.getAttribute('data-format');
                if(!format) return;
                
                exportAllBtn.disabled = true;
                const originalHTML = exportAllBtn.innerHTML;
                exportAllBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><rect x="0" y="0" width="16" height="16"/></svg> Exportando...`;
                
                try {
                    const response = await fetch(`../api/export_students.php?format=${format}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `data=${encodeURIComponent(JSON.stringify(currentStudentsData))}&grade=${encodeURIComponent(gradoSeleccionadoSpan.textContent)}`
                    });
                    const result = await response.json();

                    if (result.success) {
                        alert(`Listado exportado en formato ${format.toUpperCase()} (${result.filename})`);
                        // Aquí podrías redirigir a result.download_url si el backend genera un archivo real
                    } else {
                        alert(`Error al exportar listado: ${result.message}`);
                    }

                } catch (error) {
                    console.error('Error al exportar listado:', error);
                    alert(`Error de red al exportar listado: ${error.message}`);
                } finally {
                    exportAllBtn.disabled = false;
                    exportAllBtn.innerHTML = originalHTML;
                }
            });
        });
        
        // Ver más información del estudiante
        document.querySelectorAll('.view-more').forEach(btn => {
            btn.addEventListener('click', async function() {
                const studentId = this.getAttribute('data-id');
                if(!studentId) return;
                
                this.disabled = true;
                try {
                    const response = await fetch(`../api/get_student_grades.php?student_id=${studentId}`);
                    const data = await response.json();

                    if (data.success && data.data.student_info) {
                        const studentInfo = data.data.student_info;
                        const grades = data.data.grades;

                        // Crear y mostrar modal de detalles
                        const detailsModal = document.createElement('div');
                        detailsModal.className = 'details-modal';
                        detailsModal.setAttribute('aria-hidden', 'false');
                        
                        let gradesHtml = '';
                        if (grades.length > 0) {
                            gradesHtml = '<h4>Calificaciones:</h4><ul>';
                            grades.forEach(grade => {
                                gradesHtml += `<li>${grade.materia}: ${grade.calificacion.toFixed(2)} (${grade.desempeno})</li>`;
                            });
                            gradesHtml += '</ul>';
                        } else {
                            gradesHtml = '<p>No hay calificaciones disponibles.</p>';
                        }

                        detailsModal.innerHTML = `
                            <div class="details-content">
                                <h3>Detalles del Estudiante</h3>
                                <p><strong>ID:</strong> ${studentInfo.id_ficha}</p>
                                <p><strong>Nombre:</strong> ${studentInfo.nombres} ${studentInfo.apellidos}</p>
                                <p><strong>Grado:</strong> ${studentInfo.grado_numero || 'N/A'}${studentInfo.letra_seccion || ''}</p>
                                <p><strong>Documento:</strong> ${studentInfo.codigo || 'N/A'}</p>
                                <p><strong>Fecha Nacimiento:</strong> ${studentInfo.fecha_nacimiento || 'N/A'}</p>
                                ${gradesHtml}
                                <button class="close-details">Cerrar</button>
                            </div>
                        `;
                        document.body.appendChild(detailsModal);
                        
                        // Configurar evento para cerrar el modal
                        detailsModal.querySelector('.close-details').addEventListener('click', () => {
                            detailsModal.remove();
                        });
                        
                        // Cerrar al hacer clic fuera del contenido
                        detailsModal.addEventListener('click', (e) => {
                            if(e.target === detailsModal) {
                                detailsModal.remove();
                            }
                        });
                    } else {
                        alert(data.message || 'Estudiante no encontrado para ver detalles.');
                    }
                } catch (error) {
                    console.error('Error al obtener detalles del estudiante:', error);
                    alert(`Error de red al obtener detalles del estudiante: ${error.message}`);
                } finally {
                    this.disabled = false;
                }
            });
        });
    }

    // Cerrar modal principal
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = 'auto';
    });

    // Cerrar modal al hacer clic fuera del contenido
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }
    });

    // Cerrar con tecla Escape
    document.addEventListener('keydown', function(event) {
        if(event.key === 'Escape' && modal.style.display === 'flex') {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = 'auto';
        }
    });
});