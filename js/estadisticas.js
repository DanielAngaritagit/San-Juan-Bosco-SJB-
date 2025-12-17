document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    fetch('estadisticas.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar los contadores en la página
                document.getElementById('studentCount').textContent = data.data.estudiantes;
                document.getElementById('teacherCount').textContent = data.data.profesores;
                document.getElementById('parentCount').textContent = data.data.padres;
                document.getElementById('staffCount').textContent = data.data.administrativos;
                
                // Opcional: Animación de conteo
                animarContadores();
            }
        })
        .catch(error => {
            console.error('Error al cargar estadísticas:', error);
        });
}

function animarContadores() {
    const elementos = [
        { id: 'studentCount', final: parseInt(document.getElementById('studentCount').textContent) },
        { id: 'teacherCount', final: parseInt(document.getElementById('teacherCount').textContent) },
        { id: 'parentCount', final: parseInt(document.getElementById('parentCount').textContent) },
        { id: 'staffCount', final: parseInt(document.getElementById('staffCount').textContent) }
    ];

    elementos.forEach(item => {
        if (item.final > 0) {
            animateValue(item.id, 0, item.final, 2000);
        }
    });
}

function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}