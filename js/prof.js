// Variables globales
let idProfesor = 1; // ID del profesor logueado (debería venir de la sesión)
let performanceChart = null;
let cursosDisponibles = [];
let estudiantesCursoActual = [];
// Datos iniciales
document.addEventListener('DOMContentLoaded', async () => {
    await cargarDatosProfesor();
    await cargarCursos();
    await cargarAsistencias();
    inicializarChart();
    loadUserProfile(); // Cargar el perfil del usuario
});

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

// Cargar datos del profesor desde BD
async function cargarDatosProfesor() {
    try {
        const response = await fetch(`/api/profesores/${idProfesor}`);
        const profesor = await response.json();
        
        document.getElementById('nombre-profesor').textContent = `${profesor.nombres} ${profesor.apellidos}`;
        document.getElementById('materia-profesor').textContent = profesor.especialidad;
    } catch (error) {
        console.error('Error cargando datos del profesor:', error);
    }
}
// Cargar cursos asignados desde BD
async function cargarCursos() {
    try {
        const response = await fetch(`/api/profesores/${idProfesor}/cursos`);
        cursosDisponibles = await response.json();
        
        // Actualizar selects
        const selectCursos = document.getElementById('select-cursos');
        const selectCursoAsistencia = document.getElementById('select-curso-asistencia');
        const modalSelectCurso = document.getElementById('modal-select-curso');
        
        const options = cursosDisponibles.map(curso => 
            `<option value="${curso.id_curso}">${curso.nombre_curso} - Grado ${curso.grado}</option>`
        ).join('');
        
        [selectCursos, selectCursoAsistencia, modalSelectCurso].forEach(select => {
            select.innerHTML = options;
        });
        // Actualizar tabla
        const cuerpoTabla = document.getElementById('cuerpo-tabla-periodos');
        cuerpoTabla.innerHTML = cursosDisponibles.map(curso => `
            <tr>
                <td>${curso.id_curso}</td>
                <td>${curso.nombre_curso}</td>
                <td>${curso.grado}</td>
                <td>${curso.num_estudiantes}</td>
                <td>
                    <button class="edit-btn" onclick="verDetalleCurso(${curso.id_curso})">Ver</button>
                </td>
            </tr>
        `).join('');
        
        await actualizarGrafico();
    } catch (error) {
        console.error('Error cargando cursos:', error);
    }
}
// Actualizar gráfico de rendimiento
async function actualizarGrafico() {
    const idCurso = document.getElementById('select-cursos').value;
    const periodo = document.getElementById('select-periodo').value;
    
    try {
        const response = await fetch(`/api/cursos/${idCurso}/rendimiento?periodo=${periodo}`);
        const datos = await response.json();
        
        if (performanceChart) {
            performanceChart.destroy();
        }
        
        const ctx = document.getElementById('performanceChart').getContext('2d');
        performanceChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Aprobados', 'Reprobados', 'En riesgo'],
                datasets: [{
                    data: [datos.aprobados, datos.reprobados, datos.riesgo],
                    backgroundColor: ['#4CAF50', '#e74c3c', '#f1c40f'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: `Rendimiento - ${document.getElementById('select-cursos').selectedOptions[0].text}`
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error actualizando gráfico:', error);
    }
}
// Cargar asistencias desde BD
async function cargarAsistencias() {
    const idCurso = document.getElementById('select-curso-asistencia').value;
    const fecha = document.getElementById('fecha-asistencia').value;
    
    try {
        const response = await fetch(`/api/cursos/${idCurso}/asistencias?fecha=${fecha}`);
        const asistencias = await response.json();
        
        const cuerpo = document.getElementById('cuerpo-asistencia');
        cuerpo.innerHTML = asistencias.map(asistencia => `
            <tr>
                <td>${asistencia.nombre_estudiante}</td>
                <td><span class="estado-asistencia ${asistencia.estado}">${asistencia.estado.toUpperCase()}</span></td>
                <td>${new Date(asistencia.fecha_hora).toLocaleDateString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error cargando asistencias:', error);
    }
}
// Funcionalidad de asistencia
async function cargarEstudiantesParaAsistencia() {
    const idCurso = document.getElementById('select-curso-asistencia').value;
    await cargarEstudiantesModal(idCurso);
}
async function cargarEstudiantesModal(idCurso = null) {
    const cursoId = idCurso || document.getElementById('modal-select-curso').value;
    
    try {
        const response = await fetch(`/api/cursos/${cursoId}/estudiantes`);
        estudiantesCursoActual = await response.json();
        
        const lista = document.getElementById('lista-estudiantes-modal');
        lista.innerHTML = estudiantesCursoActual.map(estudiante => `
            <div class="estudiante-asistencia">
                <span>${estudiante.nombres} ${estudiante.apellido1}</span>
                <select id="estado-${estudiante.id_estud}" class="select-asistencia">
                    <option value="presente">Presente</option>
                    <option value="ausente">Ausente</option>
                    <option value="justificado">Justificado</option>
                </select>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error cargando estudiantes:', error);
    }
}
async function guardarAsistencia() {
    const fecha = document.getElementById('modal-fecha').value;
    const idCurso = document.getElementById('modal-select-curso').value;
    const registros = estudiantesCursoActual.map(estudiante => ({
        id_estud: estudiante.id_estud,
        estado: document.getElementById(`estado-${estudiante.id_estud}`).value
    }));
    try {
        const response = await fetch('/api/asistencias', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_profesor: idProfesor,
                id_curso: idCurso,
                fecha: fecha,
                registros: registros
            })
        });
        if (response.ok) {
            Swal.fire('Éxito', 'Asistencia registrada correctamente', 'success');
            cerrarModalAsistencia();
            await cargarAsistencias();
        } else {
            Swal.fire('Error', 'Hubo un problema al guardar', 'error');
        }
    } catch (error) {
        console.error('Error guardando asistencia:', error);
        Swal.fire('Error', 'Error de conexión', 'error');
    }
}
// Funciones auxiliares
function mostrarModalAsistencia() {
    document.getElementById('modalAsistencia').classList.add('show-modal');
}
function cerrarModalAsistencia() {
    document.getElementById('modalAsistencia').classList.remove('show-modal');
}
function inicializarChart() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    performanceChart = new Chart(ctx, {
        type: 'pie',
        data: { labels: [], datasets: [] },
        options: { responsive: true }
    });
}

// Manejo del menú lateral
function setupMenuToggle() {
    const menuToggle = document.getElementById('menuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const body = document.body;

    if (menuToggle && sideMenu) {
        menuToggle.addEventListener('click', () => {
            sideMenu.classList.toggle('active');
            if (window.innerWidth <= 768) {
                body.style.overflow = sideMenu.classList.contains('active') ? 'hidden' : 'auto';
            }
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!e.target.closest('#sideMenu') && !e.target.closest('#menuToggle')) {
                    sideMenu.classList.remove('active');
                    body.style.overflow = 'auto';
                }
            }
        });
    }
}

// Event listeners
document.getElementById('fecha-asistencia').addEventListener('change', cargarAsistencias);
document.getElementById('select-curso-asistencia').addEventListener('change', cargarAsistencias);