/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/

document.addEventListener('DOMContentLoaded', function() {
    // Lógica existente para notificaciones
    const updateCounts = () => {
        const notificationCountElement = document.getElementById('notification-count');
        if (notificationCountElement) {
            notificationCountElement.textContent = '0';
        }
        const correoCountElement = document.getElementById('correo-count');
        if (correoCountElement) {
            correoCountElement.textContent = '0';
        }
    };
    updateCounts();
    setInterval(updateCounts, 60000);

    // =====================================
    // CARGA DE PERFIL DE USUARIO
    // =====================================
    async function loadUserProfile() {
        try {
            const response = await fetch('../php/get_user_profile.php');
            if (!response.ok) throw new Error(`Error HTTP! estado: ${response.status}`);
            const data = await response.json();

            if (data.success) {
                const profileName = document.querySelector('.user-name');
                const profileRole = document.querySelector('.user-role');
                const profileImg = document.querySelector('.profile-pic');

                if (profileName) profileName.textContent = data.name;
                if (profileRole) profileRole.textContent = data.role.charAt(0).toUpperCase() + data.role.slice(1);
                if (profileImg && data.profile_pic) {
                    profileImg.src = data.profile_pic;
                }
            } else {
                console.error('Error al cargar el perfil del usuario:', data.error);
            }
        } catch (error) {
            console.error('Error de comunicación al cargar el perfil:', error);
        }
    }

    loadUserProfile();

    // =====================================
    // LÓGICA PARA CARGAR NOTAS DE HIJOS (NUEVA VERSIÓN CON ACORDEÓN DE UN NIVEL)
    // =====================================
    const studentNameElement = document.getElementById('student-name');
    const studentGradeElement = document.getElementById('student-grade');
    const studentAgeElement = document.getElementById('student-age');
    const overallAverageElement = document.getElementById('overall-average');
    const bestSubjectElement = document.getElementById('best-subject');
    const overallPerformanceElement = document.getElementById('overall-performance');
    const areaToImproveElement = document.getElementById('area-to-improve');
    const studentPhotoImg = document.getElementById('student-photo-img');
    const childSelector = document.getElementById('child-selector');

    async function loadChildData(studentId) {
        const accordionContainer = document.getElementById('grades-accordion-container');
        if (!studentId) {
            accordionContainer.innerHTML = '<p>Por favor, seleccione un estudiante.</p>';
            return;
        }

        try {
            const gradesResponse = await fetch(`../api/get_student_grades.php?student_id=${studentId}&t=${new Date().getTime()}`);
            
            const gradesData = await gradesResponse.json();
            

            if (gradesData.success) {
                const { student_info, grades_by_materia, summary } = gradesData.data;

                // Rellenar información del estudiante
                studentNameElement.textContent = `${student_info.nombres} ${student_info.apellidos}`;
                studentGradeElement.textContent = `Grado: ${student_info.grado_numero}${student_info.letra_seccion}`;
                studentAgeElement.textContent = `Edad: ${student_info.edad} años`;
                studentPhotoImg.src = student_info.profile_pic || '../multimedia/pagina_principal/usuario.png';

                // Rellenar tarjetas de resumen
                overallAverageElement.textContent = summary.promedio_general;
                bestSubjectElement.textContent = summary.mejor_materia.nombre;
                overallPerformanceElement.textContent = summary.desempeno_general;
                overallPerformanceElement.className = `performance-tag ${summary.desempeno_general.toLowerCase()}`;
                areaToImproveElement.textContent = summary.peor_materia.nombre;

                // Lógica del Acordeón de un nivel
                accordionContainer.innerHTML = '';
                if (Object.keys(grades_by_materia).length === 0) {
                    accordionContainer.innerHTML = '<p>No hay calificaciones registradas para este estudiante.</p>';
                    return;
                }

                for (const materia in grades_by_materia) {
                    const subjectData = grades_by_materia[materia];
                    const subjectId = `subject-${materia.replace(/\s+/g, '-')}`;
                    const subjectGrades = subjectData.grades;

                    const subjectElement = document.createElement('div');
                    subjectElement.className = 'accordion-item'; // Reutilizar clase existente
                    subjectElement.innerHTML = `
                        <div class="accordion-header">
                            <span class="subject-name">${materia}</span>
                            <span class="professor-name">Prof: ${subjectData.profesor}</span>
                            <span class="badge badge-primary promedio-badge">Promedio: ${((subjectGrades.reduce((sum, g) => sum + parseFloat(g.calificacion), 0) / subjectGrades.length) || 0).toFixed(2)}</span>
                            <span class="accordion-icon">&#9660;</span>
                        </div>
                        <div id="${subjectId}" class="accordion-content" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered grades-detail-table">
                                    <thead class="thead-light">
                                        <tr><th>Fecha</th><th>Tipo</th><th>Nota</th><th>Comentario</th></tr>
                                    </thead>
                                    <tbody>
                                        ${subjectGrades.map(grade => `
                                            <tr>
                                                <td>${new Date(grade.fecha).toLocaleDateString('es-ES')}</td>
                                                <td>${grade.tipo_evaluacion}</td>
                                                <td>${parseFloat(grade.calificacion)}</td>
                                                <td>${grade.comentario || 'N/A'}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    accordionContainer.appendChild(subjectElement);
                }

                // Añadir event listeners para el acordeón
                document.querySelectorAll('.accordion-header').forEach(header => {
                    header.addEventListener('click', () => {
                        const content = header.nextElementSibling; // El contenido es el siguiente hermano
                        const icon = header.querySelector('.accordion-icon');
                        if (content.style.display === 'none') {
                            content.style.display = 'block';
                            icon.innerHTML = '&#9650;';
                        } else {
                            content.style.display = 'none';
                            icon.innerHTML = '&#9660;';
                        }
                    });
                });

            } else {
                console.error('Error al obtener calificaciones:', gradesData.message);
                accordionContainer.innerHTML = `<p>No se pudieron cargar las calificaciones para el estudiante seleccionado.</p>`;
            }
        } catch (error) {
            console.error('Error en loadChildData:', error);
            accordionContainer.innerHTML = '<p>Error al cargar los datos del estudiante.</p>';
        }
    }

    // Función para inicializar el selector de hijos
    async function initializeChildSelector() {
        try {
            const childrenResponse = await fetch('../api/get_children_ids.php');
            const childrenData = await childrenResponse.json();

            if (childrenData.success && childrenData.data.length > 0) {
                childSelector.innerHTML = '';
                childrenData.data.forEach(child => {
                    const option = document.createElement('option');
                    option.value = child.id_estud;
                    option.textContent = `${child.nombres} ${child.apellido1} ${child.apellido2 || ''}`.trim();
                    childSelector.appendChild(option);
                });

                loadChildData(childSelector.value);

                childSelector.addEventListener('change', (e) => {
                    loadChildData(e.target.value);
                });

            } else {
                console.warn('No se encontraron hijos para este padre:', childrenData.message);
                document.querySelector('.container').innerHTML = '<p>No tiene estudiantes asociados a su cuenta.</p>';
            }
        } catch (error) {
            console.error('Error en initializeChildSelector:', error);
            document.querySelector('.container').innerHTML = '<p>Error al cargar la lista de estudiantes.</p>';
        }
    }

    initializeChildSelector();
});