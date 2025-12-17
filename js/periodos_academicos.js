document.addEventListener('DOMContentLoaded', () => {
    try {
        const periodoForm = document.getElementById('periodoForm');
        const periodoIdInput = document.getElementById('periodoId');
        const nombrePeriodoSelect = document.getElementById('nombrePeriodo');
        const fechaInicioInput = document.getElementById('fechaInicio');
        const fechaFinInput = document.getElementById('fechaFin');

        // Restrict dates to the current academic year (Jan 20 to Dec 5)
        const currentYear = new Date().getFullYear();
        fechaInicioInput.min = `${currentYear}-01-20`;
        fechaInicioInput.max = `${currentYear}-12-05`;
        fechaFinInput.min = `${currentYear}-01-20`;
        fechaFinInput.max = `${currentYear}-12-05`;

        const periodosTableBody = document.getElementById('periodosTableBody');
        const cancelEditButton = document.getElementById('cancelEdit');
        const periodoFechasDiv = document.getElementById('periodoFechas');

        const dateMappings = {
            // Academic year starts in February (month index 1)
            'Periodo Bimestre 1': { startMonth: 1, endMonth: 2 },   // Feb - Mar
            'Periodo Bimestre 2': { startMonth: 3, endMonth: 4 },   // Apr - May
            'Periodo Bimestre 3': { startMonth: 5, endMonth: 7 },   // Jun - Aug (includes mid-year break)
            'Periodo Bimestre 4': { startMonth: 8, endMonth: 10 },  // Sep - Nov
            'Periodo Trimestre 1': { startMonth: 1, endMonth: 3 },  // Feb - Apr
            'Periodo Trimestre 2': { startMonth: 4, endMonth: 7 },  // May - Aug
            'Periodo Trimestre 3': { startMonth: 8, endMonth: 10 }, // Sep - Nov
        };

        // Auto-fill dates when a period is selected
        nombrePeriodoSelect.addEventListener('change', () => {
            // Auto-fill always when period is selected
            // Removed condition to allow auto-fill even when editing

            const selectedPeriod = nombrePeriodoSelect.value;
            const mapping = dateMappings[selectedPeriod];

            if (!mapping) return;

            const year = new Date().getFullYear();
            let startDate;

            if (selectedPeriod === 'Bimestre 1' || selectedPeriod === 'Trimestre 1' || selectedPeriod === 'Semestre 1') {
                startDate = new Date(year, 0, 20); // January 20th
            } else {
                startDate = new Date(year, mapping.startMonth, 1);
            }

            let endDate;

            if (selectedPeriod === 'Bimestre 4' || selectedPeriod === 'Trimestre 3' || selectedPeriod === 'Semestre 2') {
                endDate = new Date(year, 11, 5); // December 5th
            } else {
                endDate = new Date(year, mapping.endMonth + 1, 0);
            }

            // Define the overall academic year boundaries
            const academicYearStart = new Date(year, 0, 20); // January 20th
            const academicYearEnd = new Date(year, 11, 5); // December 5th

            // Clamp startDate to be no earlier than academicYearStart
            if (startDate < academicYearStart) {
                startDate = academicYearStart;
            }

            // Clamp endDate to be no later than academicYearEnd
            if (endDate > academicYearEnd) {
                endDate = academicYearEnd;
            }

            // Only auto-fill if the fields are empty, allowing manual override
            if (!fechaInicioInput.value) {
                fechaInicioInput.value = startDate.toISOString().split('T')[0];
            }
            if (!fechaFinInput.value) {
                fechaFinInput.value = endDate.toISOString().split('T')[0];
            }

            const options = { day: 'numeric', month: 'long' };
            const formattedStartDate = startDate.toLocaleDateString('es-ES', options);
            const formattedEndDate = endDate.toLocaleDateString('es-ES', options);

            if (periodoFechasDiv) {
                periodoFechasDiv.innerHTML = `Va de ${formattedStartDate} al ${formattedEndDate}`;
            }
        });

        async function fetchData(url, options = {}) {
            try {
                const response = await fetch(url, options);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.success === false) {
                    throw new Error(data.message || 'Error en la API');
                }
                return data.data || [];
            } catch (error) {
                console.error('Error fetching data:', error);
                alert(`Error al cargar datos: ${error.message}`);
                return [];
            }
        }

        async function loadPeriodos() {
            const periodos = await fetchData('../api/get_periodos_academicos.php');
            periodosTableBody.innerHTML = '';
            if (periodos.length === 0) {
                                        periodosTableBody.innerHTML = '<tr><td colspan="4">No hay periodos académicos registrados.</td></tr>';
                return;
            }

            periodos.forEach(periodo => {
                const row = periodosTableBody.insertRow();
                row.innerHTML = `
                    <td>${periodo.nombre_periodo}</td>
                    <td>${periodo.fecha_inicio}</td>
                    <td>${periodo.fecha_fin}</td>
                    <td>
                        <button class="btn btn-info btn-sm edit-btn" data-id="${periodo.id_periodo}" data-tipo='${periodo.nombre_periodo}' data-inicio="${periodo.fecha_inicio}" data-fin="${periodo.fecha_fin}">Editar</button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${periodo.id_periodo}">Eliminar</button>
                    </td>
                `;
            });

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', handleEditPeriodo);
            });
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', handleDeletePeriodo);
            });
        }

        periodoForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const id_periodo = periodoIdInput.value || null;
            const nombre_periodo = nombrePeriodoSelect.value;
            const fecha_inicio = fechaInicioInput.value;
            const fecha_fin = fechaFinInput.value;

            if (!nombre_periodo || !fecha_inicio || !fecha_fin) {
                alert('Por favor, complete todos los campos.');
                return;
            }

            try {
                const response = await fetch('../api/save_periodo_academico.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_periodo, nombre_periodo, fecha_inicio, fecha_fin }),
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    resetForm();
                    loadPeriodos();
                } else {
                    alert(`Error: ${result.message}`);
                }
            } catch (error) {
                console.error('Error saving periodo:', error);
                alert('Error al guardar el periodo académico.');
            }
        });

        function handleEditPeriodo(event) {
            const id = event.target.dataset.id;
            const nombre_periodo = event.target.dataset.tipo;
            const inicio = event.target.dataset.inicio;
            const fin = event.target.dataset.fin;

            periodoIdInput.value = id;
            nombrePeriodoSelect.value = "Periodo " + nombre_periodo;
            fechaInicioInput.value = inicio;
            fechaFinInput.value = fin;

            cancelEditButton.style.display = 'inline-block';
            window.scrollTo(0, 0);
        }

        function resetForm() {
            periodoForm.reset();
            periodoIdInput.value = '';
            cancelEditButton.style.display = 'none';
        }

        cancelEditButton.addEventListener('click', resetForm);

        async function handleDeletePeriodo(event) {
            const id_periodo = event.target.dataset.id;
            if (!confirm('¿Está seguro de que desea eliminar este periodo académico?')) return;

            try {
                const response = await fetch('../api/delete_periodo_academico.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_periodo }),
                });
                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    loadPeriodos();
                } else {
                    alert(`Error: ${result.message}`);
                }
            } catch (error) {
                console.error('Error deleting periodo:', error);
                alert('Error al eliminar el periodo académico.');
            }
        }

        loadPeriodos();
    } catch (e) {
        alert('Ocurrió un error al inicializar la página: ' + e.message);
    }
});