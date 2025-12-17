$(document).ready(function() {
    // --- CONFIGURACIÓN INICIAL ---
    const idProfesor = $('#idProfesorLogueado').val();
    const apiEndpoints = {
        getGrados: '../api/get_grados_list.php',
        getProfesorCursos: `../api/get_profesor_cursos.php?id_profesor=${idProfesor}`,
        getEstudiantes: '../api/get_estudiantes_por_grado.php',
        saveGrade: '../api/save_grade.php',
        getRecentGrades: `../api/get_calificaciones_recientes.php`
    };

    let allGrades = [];
    let profesorCursos = [];

    // --- ELEMENTOS DEL DOM ---
    const gradoSelect = $('#gradoSelect');
    const materiaSelect = $('#materiaSelect');
    const estudianteSelect = $('#estudianteSelect');
    const calificacionForm = $('#calificacionForm');
    const recientesBody = $('#calificacionesRecientesBody');
    const fechaCalificacionInput = document.getElementById('fechaCalificacion');
    const idCursoRealInput = $('#idCursoReal');

    // --- FUNCIONES ---

    function showFeedback(message, type) {
        $('.feedback-alert').remove();
        const alertHtml = `
            <div class="alert alert-${type} feedback-alert alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        calificacionForm.prepend(alertHtml);
    }

    function cargarListasIniciales() {
        const gradosPromise = $.ajax({
            url: apiEndpoints.getGrados,
            type: 'GET',
            dataType: 'json'
        });

        const profesorCursosPromise = $.ajax({
            url: apiEndpoints.getProfesorCursos,
            type: 'GET',
            dataType: 'json'
        });

        $.when(gradosPromise, profesorCursosPromise).done(function(gradosResponse, profesorCursosResponse) {
            // Cargar Grados
            if (gradosResponse[0].success && gradosResponse[0].data.length > 0) {
                allGrades = gradosResponse[0].data;
                gradoSelect.html('<option value="" selected disabled>Seleccione un grado...</option>');
                allGrades.forEach(grado => {
                    gradoSelect.append(`<option value="${grado.id_seccion}">${grado.grado_numero}° ${grado.letra_seccion}</option>`);
                });
            } else {
                gradoSelect.html('<option value="" selected disabled>No hay grados disponibles</option>');
            }

            // Almacenar Cursos del Profesor
            if (profesorCursosResponse[0].success && profesorCursosResponse[0].data.length > 0) {
                profesorCursos = profesorCursosResponse[0].data;
            } else {
                console.warn('El profesor no tiene cursos asignados.');
            }
        }).fail(function() {
            showFeedback('Error de conexión al cargar datos iniciales.', 'danger');
        });
    }

    gradoSelect.change(function() {
        const idSeccion = $(this).val();
        
        materiaSelect.html('<option value="" selected disabled>Seleccione un grado primero</option>').prop('disabled', true);
        estudianteSelect.html('<option value="" selected disabled>Seleccione una materia primero</option>').prop('disabled', true);
        idCursoRealInput.val('');

        if (idSeccion) {
            const cursosAsignadosParaGrado = profesorCursos.filter(c => c.id_seccion == idSeccion);

            if (cursosAsignadosParaGrado.length > 0) {
                materiaSelect.html('<option value="" selected disabled>Seleccione una materia...</option>');
                cursosAsignadosParaGrado.forEach(curso => {
                    materiaSelect.append(`<option value="${curso.id_curso}">${curso.nombre_curso}</option>`);
                });
                materiaSelect.prop('disabled', false);
            } else {
                materiaSelect.html('<option value="" selected disabled>No tiene materias asignadas para este grado</option>');
            }
        }
    });

    materiaSelect.change(function() {
        const idCurso = $(this).val();
        const idSeccion = gradoSelect.val();

        estudianteSelect.html('<option value="" selected disabled>Seleccione una materia primero</option>').prop('disabled', true);
        
        if (idCurso && idSeccion) {
            idCursoRealInput.val(idCurso);
            cargarEstudiantes(idSeccion);
        }
    });

    function cargarEstudiantes(idSeccion) {
        estudianteSelect.html('<option value="" selected disabled>Cargando...</option>').prop('disabled', false);
        
        $.ajax({
            url: `${apiEndpoints.getEstudiantes}?id_seccion=${idSeccion}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                estudianteSelect.html('<option value="" selected disabled>Seleccione un estudiante...</option>');
                if (response.success && response.estudiantes.length > 0) {
                    response.estudiantes.forEach(est => {
                        const fullName = `${est.apellido1} ${est.apellido2 || ''} ${est.nombres}`.trim();
                        estudianteSelect.append(`<option value="${est.id_ficha}">${fullName}</option>`);
                    });
                    estudianteSelect.prop('disabled', false);
                } else {
                     estudianteSelect.html('<option value="" selected disabled>No hay estudiantes en esta sección</option>');
                }
            },
            error: function() {
                showFeedback('Error de conexión al cargar estudiantes.', 'danger');
            }
        });
    }

    // --- LÓGICA DE CALIFICACIONES RECIENTES Y FORMULARIO (sin cambios) ---

    function cargarCalificacionesRecientes() {
        $.ajax({
            url: apiEndpoints.getRecentGrades,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                recientesBody.empty();
                if (response.success && response.data.length > 0) {
                    const calificacionesAgrupadas = response.data.reduce((acc, cal) => {
                        const gradeName = cal.nombre_grado ? "Grado " + cal.nombre_grado.trim() : 'Sin Grado';
                        const studentName = cal.nombre_estudiante ? cal.nombre_estudiante.trim() : 'Sin Estudiante';

                        if (!acc[gradeName]) {
                            acc[gradeName] = {};
                        }
                        if (!acc[gradeName][studentName]) {
                            acc[gradeName][studentName] = [];
                        }
                        acc[gradeName][studentName].push(cal);
                        return acc;
                    }, {});

                    for (const gradeName in calificacionesAgrupadas) {
                        const estudiantes = calificacionesAgrupadas[gradeName];
                        const gradeRowHtml = `
                            <tr class="grade-row" data-grade="${gradeName}">
                                <td colspan="7" class="font-weight-bold grade-name-cell text-center">
                                    ${gradeName}
                                    <i class="fas fa-chevron-down float-right"></i>
                                </td>
                            </tr>
                        `;
                        recientesBody.append(gradeRowHtml);

                        for (const studentName in estudiantes) {
                            const calificaciones = estudiantes[studentName];
                            const promedio = calificaciones.reduce((sum, cal) => sum + parseFloat(cal.calificacion), 0) / calificaciones.length;
                            
                            const studentRowHtml = `
                                <tr class="student-row" data-grade-details="${gradeName}" data-student="${studentName}" style="display: none;">
                                    <td></td>
                                    <td class="font-weight-bold">${studentName}</td>
                                    <td colspan="5">
                                        <span class="badge badge-info calificaciones-badge">(${calificaciones.length} calificaciones)</span>
                                        <span class="badge badge-primary promedio-badge">Promedio: ${promedio.toFixed(2)}</span>
                                        <i class="fas fa-chevron-down float-right"></i>
                                    </td>
                                </tr>
                            `;
                            recientesBody.append(studentRowHtml);

                            const detailsRowHtml = `
                                <tr class="student-details-row" data-student-details="${studentName}" style="display: none;">
                                    <td colspan="7">
                                        <div style="padding-left: 40px;">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr><th>Fecha</th><th>Materia</th><th>Tipo</th><th>Nota</th><th>Comentario</th><th>Periodo</th></tr>
                                                </thead>
                                                <tbody>
                                                    ${calificaciones.map(cal => `
                                                        <tr>
                                                            <td>${cal.fecha}</td>
                                                            <td>${cal.nombre_materia}</td>
                                                            <td>${cal.tipo_evaluacion}</td>
                                                            <td>${parseFloat(cal.calificacion).toFixed(1)}</td>
                                                            <td>${cal.comentario || 'N/A'}</td>
                                                            <td>${cal.periodo || 'N/A'}</td>
                                                        </tr>
                                                    `).join('')}
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            recientesBody.append(detailsRowHtml);
                        }
                    }

                } else {
                    recientesBody.append('<tr><td colspan="7" class="text-center">No hay calificaciones para mostrar.</td></tr>');
                }
            },
            error: function() {
                recientesBody.empty().append('<tr><td colspan="7" class="text-center text-danger">Error al cargar las calificaciones.</td></tr>');
            }
        });
    }

    recientesBody.on('click', '.grade-row', function() {
        console.log('Grado header visibility:', $('#header-grado').is(':visible'));
        const gradeName = $(this).data('grade');
        const studentRows = $(`.student-row[data-grade-details="${gradeName.trim()}"]`);
        const gradeCell = $(this).find('.grade-name-cell');
        const isExpanding = !$(this).hasClass('expanded');

        if (isExpanding) {
            // EXPANDING
            $(this).addClass('expanded');
            gradeCell.html(`${gradeName} <i class="fas fa-chevron-up float-right"></i>`).attr('colspan', 1).removeClass('text-center');
            $(this).append('<td colspan="6"></td>');

            // Show headers if this is the first one being expanded
            if ($('.grade-row.expanded').length === 1) {
                $('#header-estudiante').show();
                $('#header-detalles').show();
                $('#header-grado').removeClass('text-center');
            }
        } else {
            // COLLAPSING
            $(this).removeClass('expanded');
            gradeCell.html(`${gradeName} <i class="fas fa-chevron-down float-right"></i>`).attr('colspan', 7).addClass('text-center');
            $(this).find('td:not(.grade-name-cell)').remove();

            // Hide headers if this is the last one being collapsed
            if ($('.grade-row.expanded').length === 0) {
                $('#header-estudiante').hide();
                $('#header-detalles').hide();
                $('#header-grado').addClass('text-center');
            }
        }

        studentRows.toggle();
        // Hide the details rows that belong to the students of this grade
        studentRows.each(function() {
            const studentName = $(this).data('student');
            $(`.student-details-row[data-student-details="${studentName}"]`).hide();
            $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        });
    });

    recientesBody.on('click', '.student-row', function(e) {
        e.stopPropagation();
        const studentName = $(this).data('student');
        $(this).next('.student-details-row').toggle();
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });

    const calificacionInput = document.getElementById('calificacionInput');

    calificacionInput.addEventListener('input', function() {
        let value = this.value;
        value = value.replace(',', '.').replace(/[^0-9.]/g, '');
        if (value.length === 2 && !value.includes('.')) {
            value = value.substring(0, 1) + '.' + value.substring(1, 2);
        }
        else if (value.length > 2 && !value.includes('.')) {
            value = value.substring(0, 1) + '.' + value.substring(1, 2);
        }
        else if (value.includes('.')) {
            let parts = value.split('.');
            if (parts[1] && parts[1].length > 1) {
                value = parts[0] + '.' + parts[1].substring(0, 1);
            }
        }
        let numValue = parseFloat(value);
        if (isNaN(numValue)) {
            this.value = value;
        } else if (numValue < parseFloat(this.min)) {
            this.value = parseFloat(this.min);
        } else if (numValue > parseFloat(this.max)) {
            this.value = parseFloat(this.max);
        } else {
            this.value = value;
        }
    });

    calificacionForm.submit(function(e) {
        e.preventDefault();
        const calificacionData = {
            id_curso: idCursoRealInput.val(),
            id_estud: estudianteSelect.val(),
            id_profesor: idProfesor,
            tipo_evaluacion: $('#tipoEvaluacionSelect').val(),
            calificacion: $('#calificacionInput').val(),
            fecha: fechaCalificacionInput.value,
            comentario: $('#comentarioText').val().trim()
        };

        $.ajax({
            url: apiEndpoints.saveGrade,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(calificacionData),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showFeedback('¡Calificación guardada exitosamente!', 'success');
                    calificacionForm[0].reset();
                    gradoSelect.val('');
                    materiaSelect.html('<option value="" selected disabled>Seleccione un grado primero</option>').prop('disabled', true);
                    estudianteSelect.html('<option value="" selected disabled>Seleccione una materia primero</option>').prop('disabled', true);
                    idCursoRealInput.val('');
                    fechaCalificacionInput.valueAsDate = new Date();
                    cargarCalificacionesRecientes();
                } else {
                    showFeedback(`Error al guardar: ${response.message}`, 'danger');
                }
            },
            error: function() {
                showFeedback('Error de conexión. No se pudo guardar la calificación.', 'danger');
            }
        });
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const todayFormatted = `${year}-${month}-${day}`;

    fechaCalificacionInput.value = todayFormatted;
    fechaCalificacionInput.setAttribute('min', todayFormatted);
    
    // --- INICIALIZACIÓN ---
    cargarListasIniciales();
    cargarCalificacionesRecientes();
});
