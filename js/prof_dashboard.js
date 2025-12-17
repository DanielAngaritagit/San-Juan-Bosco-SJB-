$(document).ready(function() {
    // --- CONFIGURACIÓN Y ENDPOINTS ---
    const idProfesor = $('#idProfesorLogueado').val(); // Obtener el ID del profesor logueado
    const displayLimit = 5; // Número de estudiantes a mostrar inicialmente
    let allStudents = []; // Almacenará la lista completa de estudiantes
    let isDirector = false; // Flag para saber si el profesor es director de grupo

    const apiEndpoints = {
        getDirectorInfo: `../api/get_profesor_director_info.php?id_profesor=${idProfesor}`,
        getStudentsByGrade: `../api/get_students_by_director_grade.php`,
        // Endpoints para estadísticas y gráficos (aún no implementados en PHP)
        getStats: `../api/get_profesor_stats.php?id_profesor=${idProfesor}`,
        getChartData: `../api/get_rendimiento_cursos.php?id_profesor=${idProfesor}`,
        getTasks: `../api/get_profesor_tareas.php?id_profesor=${idProfesor}`
    };

    // --- ELEMENTOS DEL DOM ---
    const directorInfoDiv = $('#director-info');
    const gradoCargoSpan = $('#grado-cargo');
    const estudiantesGradoBody = $('#estudiantes-grado-body');
    const showAllStudentsBtn = $('#show-all-students');

    // --- FUNCIONES DE CARGA DE DATOS ---

    /**
     * Carga la información del director de grupo y los estudiantes de su grado.
     */
    function cargarInfoDirector() {
        $.ajax({
            url: apiEndpoints.getDirectorInfo,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.is_director) {
                    isDirector = true;
                    directorInfoDiv.show();
                    gradoCargoSpan.text(`Director del Grado ${response.grado}-${response.seccion}`);
                    cargarEstudiantesGrado(response.grado, response.seccion);

                    // Actualizar la tarjeta de "Cursos" para mostrar que es Director de Grado
                    const cursosCard = $('#total-cursos').closest('.stat-card');
                    cursosCard.find('i').removeClass('fa-book-open').addClass('fa-chalkboard-teacher');
                    cursosCard.find('.card-title').text('Director de Grado');
                    cursosCard.find('.card-text').text(`${response.grado}-${response.seccion}`);

                } else {
                    directorInfoDiv.hide(); // Ocultar si no es director de grupo
                }
            },
            error: function() {
                console.error("Error al cargar información del director.");
                directorInfoDiv.hide();
            }
        });
    }

    /**
     * Renderiza la tabla de estudiantes.
     * @param {Array} studentsToDisplay - Array de estudiantes a mostrar.
     */
    function renderStudentsTable(studentsToDisplay) {
        estudiantesGradoBody.empty();
        if (studentsToDisplay.length > 0) {
            studentsToDisplay.forEach(estudiante => {
                estudiantesGradoBody.append(`
                    <tr>
                        <td>${estudiante.nombres}</td>
                        <td>${estudiante.apellido1} ${estudiante.apellido2 || ''}</td>
                        <td>${estudiante.no_documento}</td>
                        <td>${estudiante.email}</td>
                    </tr>
                `);
            });
        } else {
            estudiantesGradoBody.append('<tr><td colspan="4" class="text-center">No hay estudiantes en este grado.</td></tr>');
        }
    }

    /**
     * Carga los estudiantes de un grado específico.
     * @param {string} grado_numero
     * @param {string} letra_seccion
     */
    function cargarEstudiantesGrado(grado_numero, letra_seccion) {
        estudiantesGradoBody.html('<tr><td colspan="4" class="text-center">Cargando estudiantes...</td></tr>');
        showAllStudentsBtn.hide(); // Ocultar el botón mientras se cargan

        $.ajax({
            url: `${apiEndpoints.getStudentsByGrade}?grado_numero=${grado_numero}&letra_seccion=${letra_seccion}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    allStudents = response.data; // Guardar todos los estudiantes
                    renderStudentsTable(allStudents.slice(0, displayLimit)); // Mostrar solo los primeros 5

                    if (allStudents.length > displayLimit) {
                        showAllStudentsBtn.show(); // Mostrar botón si hay más de 5
                    } else {
                        showAllStudentsBtn.hide();
                    }
                } else {
                    estudiantesGradoBody.append('<tr><td colspan="4" class="text-center">No hay estudiantes en este grado.</td></tr>');
                    showAllStudentsBtn.hide();
                }
            },
            error: function() {
                console.error("Error al cargar estudiantes del grado.");
                estudiantesGradoBody.html('<tr><td colspan="4" class="text-center text-danger">Error al cargar estudiantes.</td></tr>');
                showAllStudentsBtn.hide();
            }
        });
    }

    /**
     * Carga las estadísticas principales del profesor.
     */
    function cargarEstadisticas() {
        $.ajax({
            url: apiEndpoints.getStats,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#total-estudiantes').text(response.data.total_estudiantes || '0');
                    if (!isDirector) {
                        $('#total-cursos').text(response.data.total_materias || '0'); // Renamed to total_materias in API
                    }
                    $('#promedio-general').text(response.data.promedio_general || 'N/A');
                } else {
                    $('#total-estudiantes').text('--');
                    $('#total-cursos').text('--');
                    $('#promedio-general').text('--');
                    console.warn("No se encontraron datos de estadísticas para el profesor.");
                }
            },
            error: function() {
                $('#total-estudiantes').text('--');
                $('#total-cursos').text('--');
                $('#promedio-general').text('--');
                console.error("Error al cargar las estadísticas del profesor.");
            }
        });
    }

    /**
     * Carga y renderiza el gráfico de rendimiento por estudiante.
     */
    async function cargarGraficoRendimiento() {
        const ctx = document.getElementById('rendimientoCursosChart').getContext('2d');
        
        let labels = [];
        let data = [];

        try {
            const response = await $.ajax({
                url: apiEndpoints.getChartData,
                type: 'GET',
                dataType: 'json'
            });

            if (response.success && response.data && response.data.length > 0) {
                labels = response.data.map(item => item.nombre_estudiante);
                data = response.data.map(item => parseFloat(item.promedio_general).toFixed(2));
            } else {
                console.warn("No se encontraron datos de rendimiento para el gráfico.");
            }
        } catch (error) {
            console.error("Error al cargar los datos del gráfico de rendimiento:", error);
        }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Promedio General',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });
    }

    // --- MANEJADORES DE EVENTOS ---
    showAllStudentsBtn.on('click', function() {
        renderStudentsTable(allStudents); // Mostrar todos los estudiantes
        $(this).hide(); // Ocultar el botón después de mostrar todos
    });

    // --- INICIALIZACIÓN ---
    cargarInfoDirector(); // Cargar información del director al inicio
    cargarEstadisticas();
    cargarGraficoRendimiento();
});
