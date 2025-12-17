/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function () {
    const gradosSelect = document.getElementById('grados-select');
    const estudiantesContainer = document.getElementById('lista-estudiantes-container');
    const estudiantesLista = document.getElementById('lista-estudiantes');
    const asistenciaForm = document.getElementById('asistencia-form');
    const mensajeDiv = document.getElementById('mensaje-asistencia');
    const gradoTitulo = document.getElementById('grado-seleccionado-titulo');

    // 1. Cargar los grados del profesor al iniciar la página
    async function cargarGrados() {
        try {
            const response = await fetch('../api/get_grados_profesor.php');
            const data = await response.json();

            gradosSelect.innerHTML = '<option value="">-- Seleccione un grado --</option>';

            if (data.success && data.grados.length > 0) {
                data.grados.forEach(grado => {
                    const option = document.createElement('option');
                    option.value = grado.id_seccion;
                    option.textContent = grado.nombre_grado;
                    gradosSelect.appendChild(option);
                });
            } else {
                mensajeDiv.innerHTML = `<div class="alert alert-warning">${data.message || 'No tiene grados asignados.'}</div>`;
            }
        } catch (error) {
            mensajeDiv.innerHTML = `<div class="alert alert-danger">Error de red al cargar los grados.</div>`;
        }
    }

    // 2. Cargar los estudiantes cuando se selecciona un grado
    async function cargarEstudiantes(idSeccion, nombreGrado) {
        estudiantesContainer.style.display = 'block';
        gradoTitulo.textContent = `Lista de Estudiantes - ${nombreGrado}`;
        estudiantesLista.innerHTML = '<div class="list-group-item">Cargando estudiantes...</div>';

        try {
            const response = await fetch(`../api/get_estudiantes_por_grado.php?id_seccion=${idSeccion}`);
            const data = await response.json();
            
            estudiantesLista.innerHTML = ''; // Limpiar

            if (data.success && data.estudiantes.length > 0) {
                data.estudiantes.forEach(estudiante => {
                    const fullName = `${estudiante.apellido1} ${estudiante.apellido2 || ''} ${estudiante.nombres}`.trim();
                    const studentId = estudiante.id_ficha;

                    const row = document.createElement('div');
                    row.className = 'list-group-item student-row';
                    row.dataset.studentId = studentId;

                    row.innerHTML = `
                        <span>${fullName}</span>
                        <div class="attendance-options">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asistencia[${studentId}]" id="presente-${studentId}" value="presente" required>
                                <label class="form-check-label" for="presente-${studentId}">Presente</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asistencia[${studentId}]" id="ausente-${studentId}" value="ausente">
                                <label class="form-check-label" for="ausente-${studentId}">Ausente</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="asistencia[${studentId}]" id="justificado-${studentId}" value="justificado">
                                <label class="form-check-label" for="justificado-${studentId}">Justificado</label>
                            </div>
                        </div>
                        <div class="excusa-medica-upload" id="excusa-medica-${studentId}" style="display:none;">
                            <label class="custom-file-upload" for="excusa-medica-file-${studentId}">
                                <i class="fas fa-upload"></i> Subir Excusa Médica
                            </label>
                            <input type="file" name="excusa_medica_${studentId}" id="excusa-medica-file-${studentId}" accept="image/*,application/pdf">
                            <span class="file-name" id="file-name-${studentId}"></span>
                        </div>
                    `;
                    estudiantesLista.appendChild(row);

                    // Add event listener for radio buttons to show/hide file input
                    row.querySelectorAll(`input[name="asistencia[${studentId}]"]`).forEach(radio => {
                        radio.addEventListener('change', () => {
                            const excusaUploadDiv = document.getElementById(`excusa-medica-${studentId}`);
                            if (radio.value === 'justificado') {
                                excusaUploadDiv.style.display = 'block';
                            } else {
                                excusaUploadDiv.style.display = 'none';
                                // Clear the file input if hidden
                                const fileInput = document.getElementById(`excusa-medica-file-${studentId}`);
                                const fileNameSpan = document.getElementById(`file-name-${studentId}`);
                                if (fileInput) {
                                    fileInput.value = '';
                                }
                                if (fileNameSpan) {
                                    fileNameSpan.textContent = '';
                                }
                            }
                        });
                    });

                    // Add event listener for file input change to display file name
                    const fileInput = document.getElementById(`excusa-medica-file-${studentId}`);
                    const fileNameSpan = document.getElementById(`file-name-${studentId}`);
                    if (fileInput && fileNameSpan) {
                        fileInput.addEventListener('change', () => {
                            if (fileInput.files.length > 0) {
                                fileNameSpan.textContent = fileInput.files[0].name;
                            } else {
                                fileNameSpan.textContent = '';
                            }
                        });
                    }
                });
            } else {
                estudiantesLista.innerHTML = `<div class="list-group-item">${data.message || 'No hay estudiantes en este grado.'}</div>`;
            }
        } catch (error) {
            estudiantesLista.innerHTML = `<div class="list-group-item text-danger">Error de red al cargar los estudiantes.</div>`;
        }
    }

    // 3. Event Listener para el selector de grados
    gradosSelect.addEventListener('change', () => {
        const selectedId = gradosSelect.value;
        if (selectedId) {
            const selectedText = gradosSelect.options[gradosSelect.selectedIndex].text;
            cargarEstudiantes(selectedId, selectedText);
        } else {
            estudiantesContainer.style.display = 'none';
        }
    });

    // 4. Event Listener para guardar la asistencia
    asistenciaForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const studentRows = document.querySelectorAll('.student-row');
        const asistenciaData = [];
        let isValidSubmission = true; // Flag to track overall submission validity
        let excusaFile = null; // To store the single excuse file

        // Clear previous messages
        mensajeDiv.innerHTML = '';

        studentRows.forEach(row => {
            const studentId = row.dataset.studentId;
            const checkedRadio = row.querySelector(`input[name="asistencia[${studentId}]"]:checked`);
            const fileInput = document.getElementById(`excusa-medica-file-${studentId}`);
            
            if (checkedRadio) {
                const attendanceEntry = {
                    id_estud: studentId,
                    estado: checkedRadio.value
                };

                // Client-side validation for 'justificado' status and file upload
                if (checkedRadio.value === 'justificado') {
                    if (!fileInput || fileInput.files.length === 0) {
                        mensajeDiv.innerHTML = `<div class="alert alert-danger">Debe subir una excusa médica para el estudiante ${studentId} si el estado es Justificado.</div>`;
                        isValidSubmission = false;
                        return; // Skip processing this student's data for now
                    }
                    // If file exists, validate it
                    const validationResult = validateMedicalExcuseFile(fileInput.files[0]);
                    if (!validationResult.isValid) {
                        mensajeDiv.innerHTML = `<div class="alert alert-danger">${validationResult.message}</div>`;
                        isValidSubmission = false;
                        return; // Skip processing this student's data for now
                    }
                    // If valid, assign the file to excusaFile (assuming only one justified student with file)
                    excusaFile = fileInput.files[0];
                }
                asistenciaData.push(attendanceEntry);
            } else {
                // If a student is not marked, they are simply not included in the submission.
                // No error is thrown here unless no students are marked at all.
            }
        });

        // If any validation failed during the loop, stop submission
        if (!isValidSubmission) {
            return;
        }

        // Check if at least one student's attendance was marked
        if (asistenciaData.length === 0) {
            mensajeDiv.innerHTML = '<div class="alert alert-danger">Debe marcar la asistencia para al menos un estudiante.</div>';
            return;
        }

        // Create FormData object
        const formData = new FormData();
        formData.append('attendance_data', JSON.stringify(asistenciaData));
        if (excusaFile) {
            formData.append('excusa_medica', excusaFile);
        }

        try {
            const response = await fetch('../api/guardar_asistencia.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                mensajeDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                estudiantesContainer.style.display = 'none';
                gradosSelect.value = '';
            } else {
                mensajeDiv.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
            }
        } catch (error) {
            mensajeDiv.innerHTML = `<div class="alert alert-danger">Error de red al guardar la asistencia.</div>`;
        }
    });

    function validateMedicalExcuseFile(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!file) {
            return { isValid: true, message: '' }; // No file selected, so it's valid
        }

        if (!allowedTypes.includes(file.type)) {
            return { isValid: false, message: 'Tipo de archivo no permitido. Solo se aceptan imágenes (JPG, PNG) y PDF.' };
        }

        if (file.size > maxSize) {
            return { isValid: false, message: 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.' };
        }

        return { isValid: true, message: '' };
    }

    // Carga inicial de grados
    cargarGrados();
});