// listado.js - Código completo con validación de grados por nivel
document.addEventListener('DOMContentLoaded', function() {
    // ==================== INICIALIZACIÓN ====================
    initMenu();
    initModals();
    initNotifications();
    initAttendanceSystem();
});

// ==================== MÓDULO DE MENÚ ====================
function initMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');

    if (!menuToggle || !menuContainer) return;

    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menuContainer.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (menuContainer.classList.contains('active') && 
            !e.target.closest('#menu-container') && 
            !e.target.closest('#menu-toggle')) {
            menuContainer.classList.remove('active');
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 991) menuContainer.classList.remove('active');
    });
}

// ==================== MÓDULO DE MODALES ====================
function initModals() {
    const modal = document.getElementById('credentialsModal');
    const closeModalBtn = document.querySelector('.modal-close');
    
    if (!modal || !closeModalBtn) return;

    const closeModal = () => {
        modal.style.display = 'none';
        document.getElementById('credentialForm').reset();
    };

    closeModalBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeModal());
}

// ==================== MÓDULO DE NOTIFICACIONES ====================
function initNotifications() {
    const correoButton = document.getElementById('Correo');
    const notificationsButton = document.getElementById('notifications-button');

    if (!correoButton || !notificationsButton) return;

    const correoPanel = document.getElementById('correo-panel');
    const notificationsPanel = document.getElementById('notifications-panel');

    const togglePanel = (mainPanel, secondaryPanel) => {
        mainPanel.style.display = mainPanel.style.display === 'block' ? 'none' : 'block';
        secondaryPanel.style.display = 'none';
    };

    correoButton.addEventListener('click', (e) => {
        e.stopPropagation();
        togglePanel(correoPanel, notificationsPanel);
    });

    notificationsButton.addEventListener('click', (e) => {
        e.stopPropagation();
        togglePanel(notificationsPanel, correoPanel);
    });

    document.addEventListener('click', () => {
        correoPanel.style.display = 'none';
        notificationsPanel.style.display = 'none';
    });
}

// ==================== SISTEMA DE ASISTENCIA ====================
let estudiantes = [
    { nombre: "María González", nivel: "primaria", grado: "transicion" },
    { nombre: "Juan Pérez", nivel: "primaria", grado: "5a" },
    { nombre: "Ana Rodríguez", nivel: "secundaria", grado: "6b" },
    { nombre: "Carlos Sánchez", nivel: "secundaria", grado: "11c" },
    { nombre: "Laura Martínez", nivel: "primaria", grado: "3b" }
];

function initAttendanceSystem() {
    document.getElementById('class-date').valueAsDate = new Date();
    initFilters();
    updateStudents();
}

function initFilters() {
    const nivelSelect = document.getElementById('nivel');
    const gradoSelect = document.getElementById('grado');
    
    // Event listener para cambios en el nivel
    nivelSelect.addEventListener('change', function() {
        updateGradoOptions(this.value);
        updateStudents();
    });

    // Inicializar opciones de grado
    updateGradoOptions(nivelSelect.value);
}

function updateGradoOptions(nivel) {
    const gradoSelect = document.getElementById('grado');
    gradoSelect.innerHTML = '<option value="todos">Todos</option>';
    
    let grados = [];
    const secciones = ['a', 'b', 'c'];
    
    if (nivel === 'primaria') {
        // Transición + grados 1-5 con secciones
        grados.push('transicion');
        for (let i = 1; i <= 5; i++) {
            secciones.forEach(sec => grados.push(`${i}${sec}`));
        }
    } else if (nivel === 'secundaria') {
        // Grados 6-11 con secciones
        for (let i = 6; i <= 11; i++) {
            secciones.forEach(sec => grados.push(`${i}${sec}`));
        }
    } else { // Todos los niveles
        grados.push('transicion');
        for (let i = 1; i <= 11; i++) {
            secciones.forEach(sec => grados.push(`${i}${sec}`));
        }
    }

    // Crear opciones de grado
    grados.forEach(grado => {
        const option = document.createElement('option');
        option.value = grado;
        option.textContent = grado.toUpperCase();
        gradoSelect.appendChild(option);
    });
}

function updateStudents() {
    const nivel = document.getElementById('nivel').value;
    const grado = document.getElementById('grado').value;
    
    const filtrados = estudiantes.filter(e => {
        const cumpleNivel = nivel === 'todos' || e.nivel === nivel;
        const cumpleGrado = grado === 'todos' || e.grado === grado;
        return cumpleNivel && cumpleGrado;
    });

    renderStudents(filtrados);
}

function renderStudents(listaEstudiantes) {
    const tbody = document.getElementById('students-body');
    tbody.innerHTML = '';

    listaEstudiantes.forEach((estudiante, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="status-indicator presente"></span>${estudiante.nombre}</td>
            <td>${estudiante.nivel.toUpperCase()}</td>
            <td>${estudiante.grado.toUpperCase()}</td>
            <td class="attendance-options">
                <label data-status="presente">
                    <input type="radio" name="attendance-${index}" value="presente" checked> Presente
                </label>
                <label data-status="tarde">
                    <input type="radio" name="attendance-${index}" value="tarde"> Tarde
                </label>
                <label data-status="ausente">
                    <input type="radio" name="attendance-${index}" value="ausente"> Ausente
                </label>
            </td>
            <td>
                <div class="excuse-upload" id="excuse-${index}">
                    <input type="file" accept="image/*" class="excuse-file">
                    <div class="image-preview">
                        <img class="preview-image" id="preview-${index}">
                        <span class="remove-excuse">✕</span>
                    </div>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });

    // Reasignar eventos
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', handleStatusChange);
    });

    document.querySelectorAll('.excuse-file').forEach(input => {
        input.addEventListener('change', handleImageUpload);
    });

    document.querySelectorAll('.remove-excuse').forEach(btn => {
        btn.addEventListener('click', removeImage);
    });

    updateSummary();
}

function handleStatusChange(e) {
    const radio = e.target;
    const row = radio.closest('tr');
    const statusIndicator = row.querySelector('.status-indicator');
    
    statusIndicator.className = `status-indicator ${radio.value}`;
    void statusIndicator.offsetWidth; // Forzar repintado CSS
    
    updateSummary();
}

function handleImageUpload(e) {
    const input = e.target;
    const index = Array.from(document.querySelectorAll('.excuse-file')).indexOf(input);
    const preview = document.getElementById(`preview-${index}`);
    const removeBtn = preview.nextElementSibling;

    if (input.files[0]) {
        const reader = new FileReader();
        reader.onload = (event) => {
            preview.src = event.target.result;
            preview.style.display = 'inline';
            removeBtn.style.display = 'inline';
            updateSummary();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage(e) {
    const btn = e.target;
    const index = Array.from(document.querySelectorAll('.remove-excuse')).indexOf(btn);
    const preview = document.getElementById(`preview-${index}`);
    const fileInput = preview.closest('.excuse-upload').querySelector('input');
    
    preview.src = '';
    preview.style.display = 'none';
    btn.style.display = 'none';
    fileInput.value = '';
    updateSummary();
}

function updateSummary() {
    let present = 0, late = 0, absent = 0, excuses = 0;
    
    document.querySelectorAll('input[type="radio"]:checked').forEach((radio, index) => {
        const preview = document.getElementById(`preview-${index}`);
        
        switch(radio.value) {
            case 'presente': present++; break;
            case 'tarde': late++; break;
            case 'ausente': 
                absent++; 
                if (preview && preview.src) excuses++;
                break;
        }
    });

    document.getElementById('present').textContent = present;
    document.getElementById('late').textContent = late;
    document.getElementById('absent').textContent = absent;
    document.getElementById('excuses').textContent = excuses;
    document.getElementById('total').textContent = document.querySelectorAll('#students-body tr').length;
}

// ==================== ENVÍO DE DATOS ====================
function submitAttendance() {
    const attendanceData = [];
    
    document.querySelectorAll('#students-body tr').forEach((row, index) => {
        const celdas = row.cells;
        const student = {
            nombre: celdas[0].textContent.replace(/^[^a-zA-Z]+/, ''),
            nivel: celdas[1].textContent.toLowerCase(),
            grado: celdas[2].textContent.toLowerCase(),
            estado: row.querySelector('input[type="radio"]:checked').value,
            excusa: document.getElementById(`preview-${index}`)?.src || null,
            fecha: document.getElementById('class-date').value
        };
        attendanceData.push(student);
    });

    console.log('Datos enviados:', attendanceData);
    alert('Asistencia guardada para:\n' + 
          attendanceData.map(a => `${a.nombre} (${a.grado.toUpperCase()})`).join('\n'));
}