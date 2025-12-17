document.addEventListener('DOMContentLoaded', function() {
    const nivelSelect = document.getElementById('nivelAcademico');
    const gradoSelect = document.getElementById('gradoSeccion');
    const formRegistro = document.getElementById('registroForm');
    const formActividades = document.getElementById('formActividades');
    let estudiantes = [];
    let actividades = [];
    const asignaturaInstructor = "<?php echo addslashes($asignatura_instructor); ?>";
    // Menú móvil
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('sideMenu').classList.toggle('active');
    });
    // Generar grados
    function generarGrados(inicio, fin) {
        gradoSelect.innerHTML = '<option>Seleccionar Grado/Sección</option>';
        for (let grado = inicio; grado <= fin; grado++) {
            ['A', 'B', 'C'].forEach(seccion => {
                const option = document.createElement('option');
                option.value = `${grado}-${seccion}`;
                option.textContent = `${grado}° ${seccion}`;
                gradoSelect.appendChild(option);
            });
        }
    }
    // Cargar estudiantes
    async function cargarEstudiantes(grado, seccion) {
        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `gradoSeccion=${encodeURIComponent(grado + '-' + seccion)}`
            });
            
            const data = await response.json();
            
            estudiantes = data.map(est => ({
                id: est.id_estud,
                nombre: `${est.nombres} ${est.apellido1} ${est.apellido2}`,
                asignatura: asignaturaInstructor,
                notas: { cognitivo: 0, procedimental: 0, actitudinal: 0, prueba: 0 },
                grado: `${grado}-${seccion}`
            }));
            
            actualizarSelectsEstudiantes();
            actualizarTablas();
            
        } catch (error) {
            console.error('Error:', error);
        }
    }
    function actualizarSelectsEstudiantes() {
        const selects = document.querySelectorAll('#nombreEstudiante, #estudianteActividad');
        selects.forEach(select => {
            select.innerHTML = '<option value="">Seleccionar Estudiante</option>';
            estudiantes.forEach(est => {
                const option = document.createElement('option');
                option.value = est.id;
                option.textContent = est.nombre;
                select.appendChild(option);
            });
        });
    }
    function actualizarTablas() {
        actualizarTablaCalificaciones();
        actualizarTablaActividades();
    }
    function actualizarTablaCalificaciones() {
        const tbody = document.getElementById('tablaCalificaciones');
        tbody.innerHTML = '';
        estudiantes.forEach(est => {
            const promedio = 
                (est.notas.cognitivo * 6) + 
                (est.notas.procedimental * 6) + 
                (est.notas.actitudinal * 4) + 
                (est.notas.prueba * 4);
                
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${est.nombre}</td>
                <td>${est.asignatura}</td>
                <td>${est.notas.cognitivo}</td>
                <td>${est.notas.procedimental}</td>
                <td>${est.notas.actitudinal}</td>
                <td>${est.notas.prueba}</td>
                <td>${promedio.toFixed(1)}</td>
                <td><span class="badge ${promedio >= 60 ? 'bg-success' : 'bg-danger'}">${promedio >= 60 ? 'Aprobado' : 'Reprobado'}</span></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarEstudiante(${est.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarEstudiante(${est.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    function actualizarTablaActividades() {
        const tbody = document.getElementById('tablaActividades');
        tbody.innerHTML = '';
        actividades.forEach(act => {
            const estudiante = estudiantes.find(est => est.id == act.estudianteId);
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${estudiante?.nombre || 'Desconocido'}</td>
                <td>${act.tipo}</td>
                <td>${new Date(act.fecha).toLocaleDateString()}</td>
                <td>${act.puntaje}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarActividad(${act.id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarActividad(${act.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    // Event listeners
    nivelSelect.addEventListener('change', function() {
        const nivel = this.value;
        if (nivel === 'Primaria') generarGrados(1, 5);
        else if (nivel === 'Secundaria') generarGrados(6, 11);
    });
    gradoSelect.addEventListener('change', function() {
        const [grado, seccion] = this.value.split('-');
        if (grado && seccion) cargarEstudiantes(grado, seccion);
    });
    formRegistro.addEventListener('submit', function(e) {
        e.preventDefault();
        const newStudent = {
            id: Date.now(),
            nombre: document.getElementById('nombreEstudiante').value,
            asignatura: asignaturaInstructor,
            notas: {
                cognitivo: parseInt(document.getElementById('cognitivo').value),
                procedimental: parseInt(document.getElementById('procedimental').value),
                actitudinal: parseInt(document.getElementById('actitudinal').value),
                prueba: parseInt(document.getElementById('prueba').value)
            },
            grado: gradoSelect.value
        };
        
        estudiantes.push(newStudent);
        actualizarTablas();
        this.reset();
    });
    formActividades.addEventListener('submit', function(e) {
        e.preventDefault();
        const newActividad = {
            id: Date.now(),
            estudianteId: document.getElementById('estudianteActividad').value,
            tipo: document.getElementById('tipoActividad').value,
            fecha: document.getElementById('fechaActividad').value,
            puntaje: parseInt(document.getElementById('puntajeActividad').value)
        };
        
        actividades.push(newActividad);
        actualizarTablas();
        this.reset();
    });
    // Funciones globales
    window.editarEstudiante = function(id) {
        const estudiante = estudiantes.find(est => est.id === id);
        if (estudiante) {
            document.getElementById('nombreEstudiante').value = estudiante.id;
            document.getElementById('cognitivo').value = estudiante.notas.cognitivo;
            document.getElementById('procedimental').value = estudiante.notas.procedimental;
            document.getElementById('actitudinal').value = estudiante.notas.actitudinal;
            document.getElementById('prueba').value = estudiante.notas.prueba;
            estudiantes = estudiantes.filter(est => est.id !== id);
            actualizarTablas();
        }
    };
    window.eliminarEstudiante = function(id) {
        estudiantes = estudiantes.filter(est => est.id !== id);
        actualizarTablas();
    };
    window.editarActividad = function(id) {
        const actividad = actividades.find(act => act.id === id);
        if (actividad) {
            document.getElementById('estudianteActividad').value = actividad.estudianteId;
            document.getElementById('tipoActividad').value = actividad.tipo;
            document.getElementById('fechaActividad').value = actividad.fecha;
            document.getElementById('puntajeActividad').value = actividad.puntaje;
            actividades = actividades.filter(act => act.id !== id);
            actualizarTablas();
        }
    };
    window.eliminarActividad = function(id) {
        actividades = actividades.filter(act => act.id !== id);
        actualizarTablas();
    };
});