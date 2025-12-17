// Gestor de Próximos Eventos
const proximosEventosManager = (() => {
    const init = () => {
        cargarProximosEventos();
    };

    const cargarProximosEventos = async () => {
        const lista = document.getElementById('proximos-eventos-lista');
        if (!lista) return;

        try {
            const response = await fetch('../api/get_proximos_eventos.php');
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const eventos = await response.json();

            lista.innerHTML = ''; // Limpiar la lista

            if (eventos.length === 0) {
                lista.innerHTML = '<li class="no-eventos">No hay eventos en los próximos 15 días.</li>';
                return;
            }

            eventos.forEach(evento => {
                const li = document.createElement('li');
                li.className = 'evento-item';

                const fecha = new Date(evento.fecha + 'T00:00:00'); // Asegurar que se interprete como fecha local
                const dia = fecha.getDate();
                const mes = fecha.toLocaleDateString('es-ES', { month: 'long' });

                li.innerHTML = `
                    <div class="evento-fecha">
                        <span class="dia">${dia}</span>
                        <span class="mes">${mes}</span>
                    </div>
                    <div class="evento-info">
                        <p class="titulo">${evento.titulo}</p>
                        <p class="detalles">${evento.detalles || 'Sin detalles adicionales.'}</p>
                    </div>
                `;
                lista.appendChild(li);
            });

        } catch (error) {
            console.error('Error al cargar próximos eventos:', error);
            if (lista) {
                lista.innerHTML = '<li class="no-eventos" style="color: red;">Error al cargar los eventos.</li>';
            }
        }
    };

    return { init };
})();