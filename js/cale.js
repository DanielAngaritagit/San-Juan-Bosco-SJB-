// Variables globales
let currentDate = new Date();
let events = [];
let editingIndex = -1;
let currentView = 'month';

// --- Función robusta para cargar eventos ---
async function loadEvents() {
    try {
        const response = await fetch('../php/eventos.php');
        if (!response.ok) throw new Error(`Error HTTP! estado: ${response.status}`);
        const data = await response.json();

        if (data.success === false && data.error) {
             console.error('Error cargando eventos desde el servidor: ' + data.error);
             events = [];
        } else if (data.success && Array.isArray(data.data)) {
            events = data.data.map(event => ({
                id: event.id,
                name: event.nombre,
                description: event.descripcion,
                startDate: event.fecha_inicio,
                endDate: event.fecha_fin,
                startTime: event.hora_inicio,
                endTime: event.hora_fin,
                color: event.color,
                creado_por: event.usuario_id,
                // Aseguramos que target_roles sea un array (si viene como string "rol1,rol2")
                target_roles: event.target_roles ? event.target_roles.split(',') : [],
                // Aseguramos que target_ids sea un array (si viene como string "id1,id2")
                target_ids: event.target_ids ? event.target_ids.split(',').map(id => id.trim()) : []
            }));
        } else {
            events = [];
        }
        generateCalendar();
    } catch (error) {
        console.error('Error fatal cargando eventos: ' + error.message);
        events = [];
        generateCalendar();
    }
}

// --- Funciones del calendario ---
function parseLocalDate(dateString) {
    if (!dateString || typeof dateString !== 'string' || !dateString.includes('-')) {
        return new Date('Invalid');
    }
    const [year, month, day] = dateString.split('-');
    return new Date(year, month - 1, day);
}

function switchView(view) {
    currentView = view;
    document.getElementById('monthView').style.display = view === 'month' ? 'table' : 'none';
    document.getElementById('weekView').style.display = view === 'week' ? 'table' : 'none';
    document.querySelectorAll('.view-toggle button').forEach(btn => {
        btn.classList.remove('btn-view-active');
        if ((view === 'month' && btn.textContent === 'Mes') || (view === 'week' && btn.textContent === 'Semana')) {
            btn.classList.add('btn-view-active');
        }
    });
    generateCalendar();
}

function generateCalendar() {
    if (currentView === 'month') {
        generateMonthView();
    } else {
        generateWeekView();
    }
}

function generateMonthView() {
    const calendarBody = document.getElementById('calendar-body');
    const monthYear = document.getElementById('monthYear');
    if (!calendarBody || !monthYear) return;
    calendarBody.innerHTML = '';
    const monthNames = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];
    monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let date = new Date(firstDay);
    date.setDate(date.getDate() - date.getDay());
    for (let i = 0; i < 6; i++) {
        const row = document.createElement('tr');
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            cell.className = 'calendar-day';
            const cellDate = new Date(date);
            cellDate.setHours(0, 0, 0, 0);
            const dayNumber = document.createElement('span');
            dayNumber.className = 'day-number';
            dayNumber.textContent = cellDate.getDate();
            if (cellDate.getMonth() !== currentDate.getMonth()) {
                cell.classList.add('other-month');
            }
            if (cellDate.getTime() === today.getTime()) {
                cell.classList.add('current-day');
            }
            cell.appendChild(dayNumber);
            const eventsContainer = document.createElement('div');
            eventsContainer.className = 'events-container';
            const cellEvents = events.filter(event => {
                if (!event.startDate || !event.endDate) return false;
                const eventStart = parseLocalDate(event.startDate);
                const eventEnd = parseLocalDate(event.endDate);
                return cellDate >= eventStart && cellDate <= eventEnd;
            });
            cellEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'event';
                eventDiv.style.backgroundColor = event.color;
                eventDiv.textContent = event.name;
                eventDiv.onclick = (e) => {
                    e.stopPropagation();
                    showEventForm(event);
                };
                eventsContainer.appendChild(eventDiv);
            });
            cell.appendChild(eventsContainer);
            row.appendChild(cell);
            date.setDate(date.getDate() + 1);
        }
        calendarBody.appendChild(row);
    }
}

function generateWeekView() {
    const weekBody = document.getElementById('week-body');
    if (!weekBody) return;
    weekBody.innerHTML = '';
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    for(let hour = 6; hour <= 14; hour++) {
        const row = document.createElement('tr');
        const timeCell = document.createElement('td');
        timeCell.className = 'time-slot time-column';
        timeCell.textContent = `${hour.toString().padStart(2, '0')}:00`;
        row.appendChild(timeCell);
        const weekStart = new Date(currentDate);
        weekStart.setDate(currentDate.getDate() - currentDate.getDay());
        for(let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            cell.className = 'week-day-cell';
            const cellDate = new Date(weekStart);
            cellDate.setDate(weekStart.getDate() + day);
            cellDate.setHours(hour, 0, 0, 0);
            if (cellDate.toDateString() === today.toDateString()) {
                cell.style.backgroundColor = 'rgba(67, 97, 238, 0.05)';
            }
            const eventsInCell = events.filter(event => {
                if (!event.startDate || !event.endDate || !event.startTime || !event.endTime) return false;
                const eventStart = new Date(event.startDate + 'T' + event.startTime);
                const eventEnd = new Date(event.endDate + 'T' + event.endTime);
                return cellDate >= eventStart && cellDate <= eventEnd;
            });
            eventsInCell.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'week-event';
                eventDiv.style.backgroundColor = event.color;
                eventDiv.textContent = event.name;
                eventDiv.onclick = (e) => {
                    e.stopPropagation();
                    showEventForm(event);
                };
                cell.appendChild(eventDiv);
            });
            row.appendChild(cell);
        }
        weekBody.appendChild(row);
    }
}


function goToToday() {
    currentDate = new Date();
    generateCalendar();
}

function previous() {
    currentView === 'month' 
        ? currentDate.setMonth(currentDate.getMonth() - 1)
        : currentDate.setDate(currentDate.getDate() - 7);
    generateCalendar();
}

function next() {
    currentView === 'month' 
        ? currentDate.setMonth(currentDate.getMonth() + 1)
        : currentDate.setDate(currentDate.getDate() + 7);
    generateCalendar();
}

// --- Función para mostrar el formulario ---
function showEventForm(event = null) {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const modalTitle = document.getElementById('modalTitle');
    const deleteBtn = document.getElementById('deleteBtn');
    const destinatariosOptionsDiv = document.querySelector('.destinatarios-options'); // Contenedor de checkboxes de roles

    form.reset(); // Limpia todos los campos

    // Ocultar todas las listas de destinatarios al inicio
    document.querySelectorAll('[id^="lista-"]').forEach(container => {
        container.innerHTML = '';
        container.style.display = 'none';
    });

    if (event) { // Modo edición
        editingIndex = events.findIndex(e => e.id === event.id);
        modalTitle.textContent = 'Editar Evento';
        form.eventName.value = event.name;
        form.eventDescription.value = event.description || ''; // Cargar descripción
        form.startDate.value = event.startDate;
        form.endDate.value = event.endDate;
        form.startTime.value = event.startTime;
        form.endTime.value = event.endTime;
        
        // Seleccionar color
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.dataset.color === event.color) {
                opt.classList.add('selected');
            }
        });
        form.selectedColor.value = event.color;

        // Mostrar botón de eliminar
        deleteBtn.style.display = 'inline-block';

        // Manejar destinatarios (solo si el div existe en la página)
        if (destinatariosOptionsDiv) {
            // Desmarcar todos los checkboxes de destinatarios primero
            document.querySelectorAll('input[name="destinatarios"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Marcar los roles de destinatario del evento
            event.target_roles.forEach(role => {
                const checkbox = document.getElementById(`dest_${role}`);
                if (checkbox) {
                    checkbox.checked = true;
                    // Cargar la lista de usuarios/cursos para el rol si está marcado
                    if (role === 'profesor') {
                        cargarListaUsuarios('profesor', 'lista-profesores-container', event.target_ids);
                        document.getElementById('lista-profesores-container').style.display = 'block';
                    } else if (role === 'estudiante' || role === 'padre') { // Asumimos que padre/estudiante usan cargarListaCursos
                        cargarListaCursos(`lista-${role}s-container`, event.target_ids);
                        document.getElementById(`lista-${role}s-container`).style.display = 'block';
                    }
                }
            });
        }

    } else { // Modo creación
        editingIndex = -1;
        modalTitle.textContent = 'Nuevo Evento';
        const today = new Date().toISOString().split('T')[0];
        form.startDate.value = today;
        form.endDate.value = today;
        form.startTime.value = '08:00'; // Hora por defecto
        form.endTime.value = '09:00';   // Hora por defecto
        deleteBtn.style.display = 'none';
        
        // Seleccionar primer color por defecto
        const firstColorOption = document.querySelector('.color-option');
        if (firstColorOption) {
           selectColor(firstColorOption);
        }

        // Desmarcar todos los checkboxes de destinatarios
        document.querySelectorAll('input[name="destinatarios"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
    
    modal.classList.add('active');
}

function hideEventForm() {
    document.getElementById('eventModal').classList.remove('active');
    editingIndex = -1;
}

function selectColor(element) {
    if (!element) return;
    document.querySelectorAll('.color-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    element.classList.add('selected');
    document.getElementById('selectedColor').value = element.dataset.color;
}

function deleteEvent() {
    if (editingIndex >= 0 && events[editingIndex]?.id) {
        if (confirm('¿Estás seguro de eliminar este evento?')) {
            deleteEventFromServer(events[editingIndex].id);
        }
    }
    hideEventForm();
}

// --- Función para guardar el evento ---
async function saveEvent(e) {
    e.preventDefault();
    const form = e.target;
    
    const selectedDestinatarios = [];
    // Solo intentar recoger destinatarios si los checkboxes existen en la página
    document.querySelectorAll('input[name="destinatarios"]:checked').forEach(checkbox => {
        selectedDestinatarios.push(checkbox.value);
    });

    const selectedIds = [];
    // Solo intentar recoger IDs si los checkboxes existen en la página
    document.querySelectorAll('input[name="destinatario_id"]:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });

    const eventData = {
        nombre: form.eventName.value,
        descripcion: form.eventDescription.value,
        fecha_inicio: form.startDate.value,
        fecha_fin: form.endDate.value,
        hora_inicio: form.startTime.value,
        hora_fin: form.endTime.value,
        color: form.selectedColor.value,
        target_roles: selectedDestinatarios,
        target_ids: selectedIds
    };

    const startDateTime = new Date(`${eventData.fecha_inicio}T${eventData.hora_inicio}`);
    const endDateTime = new Date(`${eventData.fecha_fin}T${eventData.hora_fin}`);

    if (startDateTime > endDateTime) {
        alert('La fecha y hora de fin no pueden ser anteriores a la fecha y hora de inicio.');
        return;
    }

    const payload = {
        ...eventData
    };

    if (editingIndex >= 0 && events[editingIndex]?.id) {
        payload.id = events[editingIndex].id;
        await updateEvent(payload);
    } else {
        await createEvent(payload);
    }
    hideEventForm();
}

// --- Funciones de comunicación con el servidor ---
async function createEvent(eventData) {
    try {
        const response = await fetch('../php/eventos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'create', ...eventData})
        });
        const result = await response.json();
        if (result.success) {
            await loadEvents();
        } else {
             console.error('Error del servidor al crear:', result.error);
        }
    } catch (error) {
        console.error('Error guardando evento:', error.message);
    }
}

async function updateEvent(eventData) {
    try {
        const response = await fetch('../php/eventos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'update', ...eventData})
        });
        const result = await response.json();
        if (result.success) {
            await loadEvents();
        } else {
             console.error('Error del servidor al actualizar:', result.error);
        }
    } catch (error) {
        console.error('Error actualizando evento:', error.message);
    }
}

async function deleteEventFromServer(eventId) {
    try {
        const response = await fetch('../php/eventos.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'delete', id: eventId})
        });
        const result = await response.json();
        if (result.success) {
            await loadEvents();
        } else {
            console.error('Error del servidor al eliminar:', result.error);
        }
    } catch (error) {
        console.error('Error eliminando evento:', error.message);
    }
}

// --- Función para cargar la lista de usuarios por rol ---
async function cargarListaUsuarios(rol, containerId, preSelectedIds = []) {
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Limpiar antes de cargar

    try {
        const response = await fetch(`../php/get_usuarios.php?rol=${rol}`);
        const usuarios = await response.json();

        if (usuarios.length > 0) {
            const selectAllCheckbox = document.createElement('div');
            selectAllCheckbox.className = 'select-all-container';
            selectAllCheckbox.innerHTML = `
                <input type="checkbox" id="select_all_${rol}" onchange="toggleAll(this, '${rol}')">
                <label for="select_all_${rol}">Seleccionar Todos</label>
            `;
            container.appendChild(selectAllCheckbox);

            usuarios.forEach(usuario => {
                const userDiv = document.createElement('div');
                userDiv.className = 'user-checkbox';
                const isChecked = preSelectedIds.includes(String(usuario.id_log)); // Convertir a string para comparación
                userDiv.innerHTML = `
                    <input type="checkbox" name="destinatario_id" value="${usuario.id_log}" data-rol="${rol}" ${isChecked ? 'checked' : ''}>
                    <label>${usuario.nombre} ${usuario.apellido}</label>
                `;
                container.appendChild(userDiv);
            });
        }
    } catch (error) {
        console.error(`Error cargando la lista de ${rol}:`, error);
        container.innerHTML = '<p>Error cargando los profesores</p>';
    }
}

function toggleAll(source, rol) {
    const checkboxes = document.querySelectorAll(`input[name="destinatario_id"][data-rol="${rol}"]`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = source.checked;
    });
}

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

// --- Inicialización ---
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener("DOMContentLoaded", async function() {
    

    await loadEvents();
    switchView('month');
    loadUserProfile(); // Cargar el perfil del usuario

    // Manejo de eventos de click unificado para los colores
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('color-option')) {
            selectColor(e.target);
        }
    });

    // Listener para la casilla de profesores
    const destProfesorCheckbox = document.getElementById('dest_profesor');
    if (destProfesorCheckbox) {
        destProfesorCheckbox.addEventListener('change', function() {
            const container = document.getElementById('lista-profesores-container');
            if (this.checked) {
                container.style.display = 'block';
                cargarListaUsuarios('profesor', 'lista-profesores-container');
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
            }
        });
    }

    // Listener para la casilla de estudiantes
    const destEstudianteCheckbox = document.getElementById('dest_estudiante');
    if (destEstudianteCheckbox) {
        destEstudianteCheckbox.addEventListener('change', function() {
            const container = document.getElementById('lista-estudiantes-container');
            if (this.checked) {
                container.style.display = 'block';
                cargarListaCursos('lista-estudiantes-container');
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
            }
        });
    }

    // Listener para la casilla de padres
    const destPadreCheckbox = document.getElementById('dest_padre');
    if (destPadreCheckbox) {
        destPadreCheckbox.addEventListener('change', function() {
            const container = document.getElementById('lista-padres-container');
            if (this.checked) {
                container.style.display = 'block';
                cargarListaCursos('lista-padres-container');
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
            }
        });
    }

    
});

// --- Función para cargar la lista de cursos (grados) ---
async function cargarListaCursos(containerId, preSelectedIds = []) {
    const container = document.getElementById(containerId);
    container.innerHTML = ''; // Limpiar antes de cargar

    try {
        const response = await fetch(`../php/get_cursos.php`);
        const data = await response.json();

        if (data.success && data.cursos.length > 0) {
            const selectAllCheckbox = document.createElement('div');
            selectAllCheckbox.className = 'select-all-container';
            selectAllCheckbox.innerHTML = `
                <input type="checkbox" id="select_all_cursos" onchange="toggleAll(this, 'curso')">
                <label for="select_all_cursos">Seleccionar Todos los Cursos</label>
            `;
            container.appendChild(selectAllCheckbox);

            data.cursos.forEach(curso => {
                const cursoDiv = document.createElement('div');
                cursoDiv.className = 'curso-checkbox';
                const isChecked = preSelectedIds.includes(String(curso.id_seccion)); // Usar id_seccion
                cursoDiv.innerHTML = `
                    <input type="checkbox" name="destinatario_id" value="${curso.id_seccion}" data-rol="curso" ${isChecked ? 'checked' : ''}>
                    <label>${curso.grado_numero}-${curso.letra_seccion}</label>
                `;
                container.appendChild(cursoDiv);
            });
        } else {
            container.innerHTML = '<p>No se encontraron cursos.</p>';
        }
    } catch (error) {
        console.error(`Error cargando la lista de cursos:`, error);
        container.innerHTML = '<p>Error cargando los cursos.</p>';
    }
}

