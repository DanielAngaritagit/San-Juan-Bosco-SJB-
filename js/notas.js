document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const nivelSelect = document.getElementById('nivelAcademico');
    const gradoSelect = document.getElementById('gradoSeccion');
    const nombreEstudianteSelect = document.getElementById('nombreEstudiante');
    const asignaturaInstructorInput = document.getElementById('asignaturaInstructor');
    const cognitivoInput = document.getElementById('cognitivo');
    const procedimentalInput = document.getElementById('procedimental');
    const actitudinalInput = document.getElementById('actitudinal');
    const pruebaInput = document.getElementById('prueba');
    const comentarioInput = document.getElementById('comentario');
    const registroForm = document.getElementById('registroForm');
    const tablaCalificacionesBody = document.getElementById('tablaCalificaciones');

    const estudianteActividadSelect = document.getElementById('estudianteActividad');
    const tipoActividadSelect = document.getElementById('tipoActividad');
    const fechaActividadInput = document.getElementById('fechaActividad');
    const puntajeActividadInput = document.getElementById('puntajeActividad');
    const formActividades = document.getElementById('formActividades');
    const tablaActividadesBody = document.getElementById('tablaActividades');

    // Asumir un ID de profesor fijo por ahora. En un sistema real, vendría de la sesión.
    const profesorId = 1; 
    let asignaturaInstructor = '';

    // Configuración de grados
    const gradosConfig = {
        Primaria: { inicio: 1, fin: 5 },
        Secundaria: { inicio: 6, fin: 11 }
    };

    // Funcionalidad del menú
    function setupMenuToggle() {
        const menuToggle = document.getElementById('menuToggle');
        const sideMenu = document.getElementById('sideMenu');

        if (menuToggle && sideMenu) {
            menuToggle.addEventListener('click', () => {
                sideMenu.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!sideMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    sideMenu.classList.remove('active');
                }
            });
        }
    }

    setupMenuToggle();

    // Cargar estudiantes y datos iniciales
    async function loadUserProfile() {
        try {
            const response = await fetch('../php/get_user_profile.php');
            if (!response.ok) throw new Error(`Error HTTP! estado: ${response.status}`);
            const data = await response.json();

            if (data.success) {
                const profileName = document.getElementById('profile-name');
                const profileRole = document.getElementById('profile-role');
                const profileImg = document.getElementById('profile-img');

                if (profileName) profileName.textContent = data.name;
                if (profileRole) profileRole.textContent = data.role.charAt(0).toUpperCase() + data.role.slice(1); // Capitalizar rol
                if (profileImg) profileImg.src = data.profile_pic;
            } else {
                console.error('Error al cargar el perfil del usuario:', data.error);
                const profileName = document.getElementById('profile-name');
                const profileRole = document.getElementById('profile-role');
                if (profileName) profileName.textContent = 'Usuario';
                if (profileRole) profileRole.textContent = 'Desconocido';
            }
        } catch (error) {
            console.error('Error de comunicación al cargar el perfil:', error);
            const profileName = document.getElementById('profile-name');
            const profileRole = document.getElementById('profile-role');
            if (profileName) profileName.textContent = 'Usuario';
            if (profileRole) profileRole.textContent = 'Desconocido';
        }
    }

    async function loadInitialData() {
        try {
            const response = await fetch(`../api/get_grades_data.php?profesor_id=${profesorId}`);
            const data = await response.json();

            if (data.success) {
                asignaturaInstructor = data.data.asignatura_instructor;
                asignaturaInstructorInput.value = asignaturaInstructor;

                // Llenar selects de estudiantes
                const estudiantes = data.data.estudiantes;
                nombreEstudianteSelect.innerHTML = '<option value="">Seleccionar Estudiante</option>';
                estudianteActividadSelect.innerHTML = '<option value="">Seleccionar Estudiante</option>';
                estudiantes.forEach(estudiante => {
                    const nombreCompleto = `${estudiante.nombres} ${estudiante.apellido1} ${estudiante.apellido2 || ''}`;
                    const option1 = new Option(nombreCompleto, estudiante.id_estud);
                    const option2 = new Option(nombreCompleto, estudiante.id_estud);
                    nombreEstudianteSelect.appendChild(option1);
                    estudianteActividadSelect.appendChild(option2);
                });

                // Cargar historial de calificaciones y actividades
                renderizarTablaCalificaciones(data.data.calificaciones);
                renderizarTablaActividades(data.data.actividades);

            } else {
                console.error('Error al cargar datos iniciales:', data.message);
            }
        } catch (error) {
            console.error('Error de red al cargar datos iniciales:', error);
        }
    }

    // Actualizar grados según el nivel académico
    function actualizarGrados() {
        const nivel = nivelSelect.value;
        gradoSelect.innerHTML = '<option value="">Seleccionar Grado/Sección</option>';
        
        if (nivel && gradosConfig[nivel]) {
            const secciones = ['A', 'B', 'C'];
            for (let grado = gradosConfig[nivel].inicio; grado <= gradosConfig[nivel].fin; grado++) {
                secciones.forEach(seccion => {
                    const option = document.createElement('option');
                    option.value = `${grado}-${seccion}`;
                    option.textContent = `${grado}° ${seccion}`;
                    gradoSelect.appendChild(option);
                });
            }
        }
    }

    // Evento para nivel académico
    nivelSelect.addEventListener('change', actualizarGrados);

    // Evento para grado/sección (para recargar estudiantes filtrados)
    gradoSelect.addEventListener('change', async () => {
        const gradoSeccion = gradoSelect.value;
        if (gradoSeccion) {
            const [grado, seccion] = gradoSeccion.split('-');
            try {
                const response = await fetch(`../api/get_grades_data.php?profesor_id=${profesorId}&grado=${grado}&seccion=${seccion}`);
                const data = await response.json();
                if (data.success) {
                    const estudiantes = data.data.estudiantes;
                    nombreEstudianteSelect.innerHTML = '<option value="">Seleccionar Estudiante</option>';
                    estudianteActividadSelect.innerHTML = '<option value="">Seleccionar Estudiante</option>';
                    estudiantes.forEach(estudiante => {
                        const nombreCompleto = `${estudiante.nombres} ${estudiante.apellido1} ${estudiante.apellido2 || ''}`;
                        const option1 = new Option(nombreCompleto, estudiante.id_estud);
                        const option2 = new Option(nombreCompleto, estudiante.id_estud);
                        nombreEstudianteSelect.appendChild(option1);
                        estudianteActividadSelect.appendChild(option2);
                    });
                } else {
                    console.error('Error al filtrar estudiantes:', data.message);
                }
            } catch (error) {
                console.error('Error de red al filtrar estudiantes:', error);
            }
        }
    });

    // Enviar calificaciones
    registroForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id_estud = nombreEstudianteSelect.value;
        const cognitivo = parseFloat(cognitivoInput.value);
        const procedimental = parseFloat(procedimentalInput.value);
        const actitudinal = parseFloat(actitudinalInput.value);
        const prueba = parseFloat(pruebaInput.value);
        const comentario = comentarioInput.value.trim();

        // Validar campos
        if (!id_estud || isNaN(cognitivo) || isNaN(procedimental) || isNaN(actitudinal) || isNaN(prueba)) {
            alert('Por favor, complete todos los campos de calificación y seleccione un estudiante.');
            return;
        }

        // Calcular calificación final (promedio ponderado)
        const calificacionFinal = (
            (cognitivo * 0.30) +
            (procedimental * 0.30) +
            (actitudinal * 0.20) +
            (prueba * 0.20)
        ).toFixed(2);

        // Obtener id_curso de la asignatura del instructor
        let id_curso = null;
        try {
            const response = await fetch(`../api/get_course_id.php?course_name=${encodeURIComponent(asignaturaInstructor)}`);
            const data = await response.json();
            if (data.success && data.data.id_curso) {
                id_curso = data.data.id_curso;
            } else {
                alert('No se pudo obtener el ID del curso para la asignatura del instructor.');
                return;
            }
        } catch (error) {
            console.error('Error al obtener ID del curso:', error);
            alert('Error de red al obtener el ID del curso.');
            return;
        }

        const gradeData = {
            id_estud: id_estud,
            id_profesor: profesorId,
            id_curso: id_curso,
            calificacion: calificacionFinal,
            comentario: comentario
        };

        try {
            const response = await fetch('../api/save_grade.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(gradeData)
            });
            const data = await response.json();

            if (data.success) {
                alert('Calificación guardada exitosamente.');
                registroForm.reset();
                loadInitialData(); // Recargar datos para actualizar tablas
            } else {
                alert('Error al guardar calificación: ' + data.message);
            }
        } catch (error) {
            console.error('Error de red al guardar calificación:', error);
            alert('Error de red al guardar calificación.');
        }
    });

    // Enviar actividades
    formActividades.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id_estud = estudianteActividadSelect.value;
        const tipoActividad = tipoActividadSelect.value;
        const fechaActividad = fechaActividadInput.value;
        const puntajeActividad = parseFloat(puntajeActividadInput.value);

        if (!id_estud || !tipoActividad || !fechaActividad || isNaN(puntajeActividad)) {
            alert('Por favor, complete todos los campos de actividad y seleccione un estudiante.');
            return;
        }

        const activityData = {
            id_estud: id_estud,
            id_profesor: profesorId,
            nombre: tipoActividad,
            fecha: fechaActividad,
            puntaje: puntajeActividad
        };

        try {
            const response = await fetch('../api/save_activity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(activityData)
            });
            const data = await response.json();

            if (data.success) {
                alert('Actividad guardada exitosamente.');
                formActividades.reset();
                loadInitialData(); // Recargar datos para actualizar tablas
            } else {
                alert('Error al guardar actividad: ' + data.message);
            }
        } catch (error) {
            console.error('Error de red al guardar actividad:', error);
            alert('Error de red al guardar actividad.');
        }
    });

    // Renderizar tabla de calificaciones
    function renderizarTablaCalificaciones(calificaciones) {
        tablaCalificacionesBody.innerHTML = '';
        if (calificaciones.length === 0) {
            tablaCalificacionesBody.innerHTML = '<tr><td colspan="10">No hay calificaciones registradas.</td></tr>';
            return;
        }
        calificaciones.forEach(cal => {
            const promedio = cal.calificacion;
            const estado = promedio >= 3.0 ? 'aprobado' : 'reprobado';
            const row = `
                <tr>
                    <td>${cal.nombres} ${cal.apellido1} ${cal.apellido2 || ''}</td>
                    <td>${cal.asignatura}</td>
                    <td>N/A</td> <!-- No hay desglose de cognitivo en la BD actual -->
                    <td>N/A</td> <!-- No hay desglose de procedimental en la BD actual -->
                    <td>N/A</td> <!-- No hay desglose de actitudinal en la BD actual -->
                    <td>N/A</td> <!-- No hay desglose de prueba en la BD actual -->
                    <td>${promedio}</td>
                    <td><span class="status-indicator ${estado}">${estado.toUpperCase()}</span></td>
                    <td>${cal.comentario || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tablaCalificacionesBody.innerHTML += row;
        });
    }

    // Renderizar tabla de actividades
    function renderizarTablaActividades(actividades) {
        tablaActividadesBody.innerHTML = '';
        if (actividades.length === 0) {
            tablaActividadesBody.innerHTML = '<tr><td colspan="5">No hay actividades registradas.</td></tr>';
            return;
        }
        actividades.forEach(act => {
            const row = `
                <tr>
                    <td>${act.nombres} ${act.apellido1} ${act.apellido2 || ''}</td>
                    <td>${act.tipo_actividad}</td>
                    <td>${new Date(act.fecha).toLocaleDateString()}</td>
                    <td>${act.puntaje}</td>
                    <td>
                        <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tablaActividadesBody.innerHTML += row;
        });
    }

    // Cargar datos al iniciar la página
    loadInitialData();
});