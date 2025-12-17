/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// Este script contiene la lógica para el dashboard principal del administrador y el temporizador de inactividad global.

$(document).ready(function() {

    // --- LÓGICA DEL DASHBOARD (SOLO SE EJECUTA SI LOS ELEMENTOS EXISTEN) ---

    // Carga las estadísticas generales (estudiantes, padres, profesores)
    function fetchDashboardStats() {
        if ($('#studentCount').length) { // Verifica si el elemento existe
            $.ajax({
                url: '../php/estadisticas.php?type=general',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const stats = response.data;
                        $('#studentCount').text(stats.estudiantes || 0);
                        $('#parentCount').text(stats.padres || 0);
                        $('#teacherCount').text(stats.profesores || 0);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX error fetching statistics: " + textStatus, errorThrown);
                }
            });
        }
    }

    // Llama a la función al cargar la página
    fetchDashboardStats();

    // Refresca las estadísticas cada 10 segundos
    setInterval(fetchDashboardStats, 10000);

    

    // Lógica de la Agenda de Eventos
    const agendaContainer = document.getElementById('events-container');
    if (agendaContainer) { // Verifica si el contenedor de la agenda existe
        const addEventBtn = document.getElementById('add-event-btn');
        const addEventForm = document.getElementById('add-event-form');
        const eventDateInput = document.getElementById('event-date');
        const saveEventBtn = document.getElementById('save-event-btn');
        const cancelEventBtn = document.getElementById('cancel-event-btn');
        const eventTitleInput = document.getElementById('event-title');
        const eventTimeInput = document.getElementById('event-time');
        const eventDetailsInput = document.getElementById('event-details');
        let events = JSON.parse(localStorage.getItem('agendaEvents')) || [];
        let editIndex = null;

        const renderEvents = () => {
            agendaContainer.innerHTML = '';
            events.sort((a, b) => new Date(a.date + 'T' + a.time) - new Date(b.date + 'T' + b.time));
            events.forEach((event, index) => {
                const eventElement = document.createElement('div');
                eventElement.classList.add('evento');
                eventElement.innerHTML = `
                    <div class="event-time">
                        <span class="time">${event.time}</span>
                        <span class="date">${new Date(event.date).toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })}</span>
                    </div>
                    <div class="event-info">
                        <h4>${event.title}</h4>
                        <p>${event.details}</p>
                    </div>
                    <div class="event-actions">
                        <button class="edit-event-btn" data-index="${index}"><img src="../multimedia/administrador/edit.png" alt="Editar"></button>
                        <button class="delete-event-btn" data-index="${index}"><img src="../multimedia/administrador/borrar.png" alt="Eliminar"></button>
                    </div>
                `;
                agendaContainer.appendChild(eventElement);
            });
        };

        const showForm = () => {
            addEventForm.style.display = 'grid';
            addEventBtn.style.display = 'none';
        };

        const hideForm = () => {
            addEventForm.style.display = 'none';
            addEventBtn.style.display = 'block';
            eventTitleInput.value = '';
            eventDateInput.value = '';
            eventTimeInput.value = '';
            eventDetailsInput.value = '';
            editIndex = null;
        };

        if (addEventBtn) {
            addEventBtn.addEventListener('click', showForm);
        }
        if (cancelEventBtn) {
            cancelEventBtn.addEventListener('click', hideForm);
        }
        if (saveEventBtn) {
            saveEventBtn.addEventListener('click', () => {
                const newEvent = {
                    title: eventTitleInput.value,
                    date: eventDateInput.value,
                    time: eventTimeInput.value,
                    details: eventDetailsInput.value
                };

                if (editIndex !== null) {
                    events[editIndex] = newEvent;
                } else {
                    events.push(newEvent);
                }

                localStorage.setItem('agendaEvents', JSON.stringify(events));
                renderEvents();
                hideForm();
            });
        }

        agendaContainer.addEventListener('click', (e) => {
            if (e.target.closest('.delete-event-btn')) {
                const index = e.target.closest('.delete-event-btn').dataset.index;
                events.splice(index, 1);
                localStorage.setItem('agendaEvents', JSON.stringify(events));
                renderEvents();
            }

            if (e.target.closest('.edit-event-btn')) {
                const index = e.target.closest('.edit-event-btn').dataset.index;
                const event = events[index];
                eventTitleInput.value = event.title;
                eventDateInput.value = event.date;
                eventTimeInput.value = event.time;
                eventDetailsInput.value = event.details;
                editIndex = index;
                showForm();
            }
        });

        if(eventDateInput) {
            const today = new Date().toISOString().split('T')[0];
            eventDateInput.setAttribute('min', today);
        }

        renderEvents();
    }

    // Lógica de Periodos Académicos
    const periodosContainer = document.getElementById('periodoForm');
    if (periodosContainer) { // Verifica si el formulario de periodos existe
        // ... (toda la lógica de gestión de periodos va aquí adentro)
    }
});

// --- TEMPORIZADOR DE INACTIVIDAD (GLOBAL) ---

let inactivityTimer;

function resetTimer() {
    clearTimeout(inactivityTimer);
    // 30 minutos en milisegundos
    inactivityTimer = setTimeout(logoutUser, 30 * 60 * 1000); 
}

function logoutUser() {
    // Redirige al usuario a la página de logout por inactividad.
    window.location.href = '../php/logout.php?reason=inactivity';
}

// Se añaden listeners a eventos comunes para reiniciar el temporizador con cualquier actividad.
window.addEventListener('load', resetTimer);
document.addEventListener('mousemove', resetTimer);
document.addEventListener('mousedown', resetTimer);
document.addEventListener('touchstart', resetTimer);
document.addEventListener('click', resetTimer);
document.addEventListener('keypress', resetTimer);
document.addEventListener('scroll', resetTimer, true);

// Llamada inicial para empezar a contar.
resetTimer();