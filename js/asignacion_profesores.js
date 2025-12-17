function initializeAsignacionProfesores() {
    const profesorSelect = document.getElementById('profesorSelect');
    const cursoSelect = document.getElementById('cursoSelect'); // Renamed to gradoSelect conceptually
    const assignmentForm = document.getElementById('assignmentForm');
    const assignmentsTableBody = document.getElementById('assignmentsTableBody');

    let allProfesores = [];
    let allGrados = []; // Renamed from allCursos

    // Helper function to format grade display
    function formatGradoDisplay(grado) {
        if (grado.grado_numero === 0) {
            return `Transición${grado.letra_seccion}`;
        } else {
            return `Grado ${grado.grado_numero}${grado.letra_seccion}`;
        }
    }

    // Function to fetch data from APIs
    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Error en la API');
            }
            return data.data;
        } catch (error) {
            console.error('Error fetching data:', error);
            alert(`Error al cargar datos: ${error.message}`);
            return [];
        }
    }

    // Populate dropdowns
    async function populateDropdowns() {
        allProfesores = await fetchData('../api/get_profesores_list.php');
        allGrados = await fetchData('../api/get_grados_list.php'); // Changed API call

        profesorSelect.innerHTML = '<option value="">Seleccione un profesor</option>';
        allProfesores.forEach(profesor => {
            const option = document.createElement('option');
            option.value = profesor.id_profesor;
            option.textContent = `${profesor.nombres} ${profesor.apellidos} (${profesor.especialidad})`;
            profesorSelect.appendChild(option);
        });

        cursoSelect.innerHTML = '<option value="">Seleccione un grado/sección</option>'; // Changed label
        allGrados.forEach(grado => {
            const option = document.createElement('option');
            option.value = grado.id_seccion;
            option.textContent = formatGradoDisplay(grado); // Changed text content
            cursoSelect.appendChild(option);
        });
    }

    // Load assignments into the table
    async function loadAssignments() {
        const assignments = await fetchData('../api/get_teacher_assignments.php');
        // Clear existing rows
        while (assignmentsTableBody.firstChild) {
            assignmentsTableBody.removeChild(assignmentsTableBody.firstChild);
        }

        if (assignments.length === 0) {
            const row = assignmentsTableBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 6; // Span all columns
            cell.textContent = 'No hay asignaciones registradas.';
            return;
        }

        assignments.forEach(assignment => {
            const row = assignmentsTableBody.insertRow();

            const profesorCell = row.insertCell();
            profesorCell.textContent = `${assignment.profesor_nombres} ${assignment.profesor_apellidos}`;

            const especialidadCell = row.insertCell();
            especialidadCell.textContent = assignment.materia_nombre;

            const gradoCell = row.insertCell();
            const tempGrado = { grado_numero: assignment.grado_numero, letra_seccion: assignment.letra_seccion };
            gradoCell.textContent = formatGradoDisplay(tempGrado);

            const seccionCell = row.insertCell();
            seccionCell.textContent = assignment.letra_seccion;

            const materiaCell = row.insertCell();
            materiaCell.textContent = assignment.materia_nombre;

            const accionesCell = row.insertCell();
            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-danger btn-sm delete-btn';
            deleteButton.textContent = 'Eliminar';
            deleteButton.dataset.profesorId = assignment.id_profesor;
            deleteButton.dataset.seccionId = assignment.id_seccion;
            accionesCell.appendChild(deleteButton);
        });

        // Add event listeners for delete buttons
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', handleDeleteAssignment);
        });
    }

    // Handle form submission
    assignmentForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const id_profesor = profesorSelect.value;
        const id_seccion = cursoSelect.value; // Changed to id_seccion

        if (!id_profesor || !id_seccion) {
            alert('Por favor, seleccione un profesor y un grado/sección.'); // Changed message
            return;
        }

        try {
            const response = await fetch('../api/save_teacher_assignment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_profesor, id_seccion }), // Changed payload
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
                assignmentForm.reset();
                loadAssignments(); // Reload assignments after saving
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error saving assignment:', error);
            alert('Error al guardar la asignación.');
        }
    });

    // Handle delete assignment
    async function handleDeleteAssignment(event) {
        const id_profesor = event.target.dataset.profesorId;
        const id_seccion = event.target.dataset.seccionId; // Changed to id_seccion

        if (!confirm('¿Está seguro de que desea eliminar esta asignación?')) {
            return;
        }

        try {
            const response = await fetch('../api/delete_teacher_assignment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_profesor, id_seccion }), // Changed payload
            });
            const result = await response.json();

            if (result.success) {
                alert(result.message);
                loadAssignments(); // Reload assignments after deleting
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error deleting assignment:', error);
            alert('Error al eliminar la asignación.');
        }
    }

    // Basic Recommendation Logic
    // This function will suggest grades/sections based on the selected teacher's specialty
    profesorSelect.addEventListener('change', () => {
        const selectedProfesorId = profesorSelect.value;
        if (!selectedProfesorId) {
            cursoSelect.innerHTML = '<option value="">Seleccione un grado/sección</option>'; // Changed label
            allGrados.forEach(grado => {
                const option = document.createElement('option');
                option.value = grado.id_seccion;
                option.textContent = formatGradoDisplay(grado); // Changed text content
                cursoSelect.appendChild(option);
            });
            return;
        }

        const selectedProfesor = allProfesores.find(p => p.id_profesor == selectedProfesorId);
        if (!selectedProfesor) return;

        const specialty = selectedProfesor.especialidad.toLowerCase();
        const recommendedGrados = allGrados.filter(grado => {
            const gradoText = formatGradoDisplay(grado).toLowerCase(); // Use formatted display for matching
            // Simple keyword matching for recommendation based on specialty
            // This logic might need refinement based on actual subject-grade mapping
            return specialty.includes('matemáticas') && gradoText.includes('grado') ||
                   specialty.includes('física') && gradoText.includes('grado') ||
                   specialty.includes('química') && gradoText.includes('grado') ||
                   specialty.includes('biología') && gradoText.includes('grado') ||
                   specialty.includes('literatura') && gradoText.includes('grado') ||
                   specialty.includes('historia') && gradoText.includes('grado') ||
                   specialty.includes('inglés') && gradoText.includes('grado') ||
                   specialty.includes('arte') && gradoText.includes('grado') ||
                   specialty.includes('filosofía') && gradoText.includes('grado') ||
                   specialty.includes('programación') && gradoText.includes('grado') ||
                   specialty.includes('transición') && gradoText.includes('transición'); // Added transition matching
        });

        cursoSelect.innerHTML = '<option value="">Seleccione un grado/sección</option>'; // Changed label
        if (recommendedGrados.length > 0) {
            const optgroup = document.createElement('optgroup');
            optgroup.label = 'Grados Recomendados'; // Changed label
            recommendedGrados.forEach(grado => {
                const option = document.createElement('option');
                option.value = grado.id_seccion;
                option.textContent = formatGradoDisplay(grado); // Changed text content
                optgroup.appendChild(option);
            });
            cursoSelect.appendChild(optgroup);
        }

        const otherGrados = allGrados.filter(grado => !recommendedGrados.some(rg => rg.id_seccion === grado.id_seccion));
        if (otherGrados.length > 0) {
            const optgroup = document.createElement('optgroup');
            optgroup.label = 'Otros Grados'; // Changed label
            otherGrados.forEach(grado => {
                const option = document.createElement('option');
                option.value = grado.id_seccion;
                option.textContent = formatGradoDisplay(grado); // Changed text content
                optgroup.appendChild(option);
            });
            cursoSelect.appendChild(optgroup);
        }
    });

    // Initial load
    populateDropdowns();
    loadAssignments();
}

/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js';
        script.onload = initializeAsignacionProfesores;
        document.head.appendChild(script);
    } else {
        initializeAsignacionProfesores();
    }
});