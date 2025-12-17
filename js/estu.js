/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const studentName = document.getElementById('student-name');
    const studentDetails = document.getElementById('student-details');
    const overallAverage = document.getElementById('overall-average');
    const academicPerformanceContainer = document.getElementById('academic-performance-container');
    const subjectGradesChartCanvas = document.getElementById('subjectGradesChart');

    if (!menuToggle || !menuContainer || !studentName || !studentDetails || !overallAverage || !academicPerformanceContainer || !subjectGradesChartCanvas) {
        console.error('Error: Uno o más elementos del DOM no encontrados.');
        return;
    }

    function loadStudentPerformance() {
        fetch(`../api/get_student_performance.php?t=${new Date().getTime()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const studentInfo = data.data.student_info;
                    const overallAvg = data.data.overall_average;
                    const gradesByCourse = data.data.grades_by_course;

                    studentName.textContent = `${studentInfo.nombres} ${studentInfo.apellidos}`;
                    studentDetails.textContent = `Grado ${studentInfo.grado_numero || 'N/A'} • Grupo ${studentInfo.letra_seccion || 'N/A'} • Código: ${studentInfo.codigo || 'N/A'}`;
                    overallAverage.textContent = overallAvg.toFixed(2);

                    const periodStatusElement = document.getElementById('period-status');
                    const overallPerformanceSummaryElement = document.getElementById('overall-performance-summary');

                    if (overallAvg >= 3.0) {
                        periodStatusElement.textContent = 'Aprobado';
                        periodStatusElement.closest('.card').classList.add('aprobando');
                        periodStatusElement.closest('.card').classList.remove('reprobando');
                    } else {
                        periodStatusElement.textContent = 'Reprobado';
                        periodStatusElement.closest('.card').classList.add('reprobando');
                        periodStatusElement.closest('.card').classList.remove('aprobando');
                    }

                    if (overallAvg >= 4.5) {
                        overallPerformanceSummaryElement.textContent = 'Excelente rendimiento académico.';
                    } else if (overallAvg >= 3.5) {
                        overallPerformanceSummaryElement.textContent = 'Buen rendimiento académico.';
                    } else if (overallAvg >= 3.0) {
                        overallPerformanceSummaryElement.textContent = 'Rendimiento académico aceptable.';
                    } else {
                        overallPerformanceSummaryElement.textContent = 'Necesita mejorar el rendimiento académico.';
                    }

                    renderPerformanceTable(gradesByCourse);
                    renderDetailedGradesAccordion(gradesByCourse);
                    renderSubjectGradesChart(gradesByCourse);

                } else {
                    console.error('Error al cargar el rendimiento del estudiante:', data.message);
                }
            })
            .catch(error => {
                console.error('Error de red al cargar el rendimiento del estudiante:', error);
            });
    }

    function renderPerformanceTable(grades) {
        academicPerformanceContainer.innerHTML = '';
        if (grades.length > 0) {
            const materiasMap = new Map();
            grades.forEach(grade => {
                if (!materiasMap.has(grade.materia)) {
                    materiasMap.set(grade.materia, { sum: 0, count: 0, profesor: grade.profesor_nombre });
                }
                materiasMap.get(grade.materia).sum += parseFloat(grade.calificacion);
                materiasMap.get(grade.materia).count++;
            });

            let tableHtml = `
                <div class="table-responsive">
                    <table class="custom-table custom-table-hover">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Profesor</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            materiasMap.forEach((data, materiaNombre) => {
                const promedioMateria = (data.sum / data.count).toFixed(2);
                const notaClass = getNotaClass(promedioMateria);
                tableHtml += `
                    <tr>
                        <td>${materiaNombre}</td>
                        <td>${data.profesor}</td>
                        <td><span class="grade-badge ${notaClass}">${promedioMateria}</span></td>
                    </tr>
                `;
            });

            tableHtml += `</tbody></table></div>`;
            academicPerformanceContainer.innerHTML = tableHtml;
        } else {
            academicPerformanceContainer.innerHTML = '<p>No hay datos de calificaciones disponibles.</p>';
        }
    }

    function renderDetailedGradesAccordion(grades) {
        const detailedGradesContainer = document.getElementById('detailed-grades-container');
        detailedGradesContainer.innerHTML = '';
        if (grades.length > 0) {
            const groupedGrades = grades.reduce((acc, grade) => {
                if (!acc[grade.materia]) {
                    acc[grade.materia] = [];
                }
                acc[grade.materia].push(grade);
                return acc;
            }, {});

            let tableHtml = `
                <div class="table-responsive">
                    <table class="custom-table custom-table-accordion">
            `;

            let first = true;
            for (const materia in groupedGrades) {
                const isExpanded = first ? 'is-open' : '';
                const displayStyle = first ? '' : 'style="display: none;"'
                first = false;

                tableHtml += `
                    <tbody class="grade-group ${isExpanded}">
                        <tr class="group-header">
                            <td colspan="4">
                                <span class="group-title">${materia}</span>
                                <span class="group-toggle-icon">▼</span>
                            </td>
                        </tr>
                        <tr class="grade-subheader" ${displayStyle}>
                            <th>Tipo de Evaluación</th>
                            <th>Período</th>
                            <th>Profesor</th>
                            <th>Calificación</th>
                        </tr>
                `;

                groupedGrades[materia].forEach(grade => {
                    const notaClass = getNotaClass(grade.calificacion);
                    tableHtml += `
                        <tr class="grade-row" ${displayStyle}>
                            <td>${grade.tipo_evaluacion}</td>
                            <td>${grade.periodo || 'N/A'}</td>
                            <td>${grade.profesor_nombre}</td>
                            <td><span class="grade-badge ${notaClass}">${parseFloat(grade.calificacion)}</span></td>
                        </tr>
                    `;
                });
                tableHtml += `</tbody>`;
            }

            tableHtml += `</table></div>`;
            detailedGradesContainer.innerHTML = tableHtml;

            addAccordionListeners();

        } else {
            detailedGradesContainer.innerHTML = '<p>No hay notas detalladas disponibles.</p>';
        }
    }

    function addAccordionListeners() {
        const headers = document.querySelectorAll('.group-header');
        headers.forEach(header => {
            header.addEventListener('click', () => {
                const group = header.parentElement;
                const icon = header.querySelector('.group-toggle-icon');
                const isOpen = group.classList.toggle('is-open');

                icon.textContent = isOpen ? '▲' : '▼';

                const subheader = group.querySelector('.grade-subheader');
                const rows = group.querySelectorAll('.grade-row');
                
                if (subheader) subheader.style.display = isOpen ? 'table-row' : 'none';
                rows.forEach(row => {
                    row.style.display = isOpen ? 'table-row' : 'none';
                });
            });
        });
    }

    function getNotaClass(nota) {
        const calificacion = parseFloat(nota);
        if (calificacion >= 4.0) return 'nota-excelente';
        if (calificacion >= 3.0) return 'nota-buena';
        return 'nota-media';
    }

    function renderSubjectGradesChart(grades) {
        if (!subjectGradesChartCanvas) {
            console.error('Canvas para el gráfico de notas de materias no encontrado.');
            return;
        }

        const materiasMap = new Map();
        grades.forEach(grade => {
            if (!materiasMap.has(grade.materia)) {
                materiasMap.set(grade.materia, { sum: 0, count: 0 });
            }
            materiasMap.get(grade.materia).sum += parseFloat(grade.calificacion);
            materiasMap.get(grade.materia).count++;
        });

        const labels = Array.from(materiasMap.keys());
        
        const palette = [
            'rgba(54, 162, 235, 0.7)', // Blue
            'rgba(255, 99, 132, 0.7)', // Red
            'rgba(75, 192, 192, 0.7)', // Green
            'rgba(255, 206, 86, 0.7)', // Yellow
            'rgba(153, 102, 255, 0.7)',// Purple
            'rgba(255, 159, 64, 0.7)', // Orange
            'rgba(199, 199, 199, 0.7)',// Grey
            'rgba(83, 102, 255, 0.7)', // Indigo
            'rgba(255, 99, 255, 0.7)', // Pink
            'rgba(102, 255, 83, 0.7)'  // Lime
        ];

        const datasets = labels.map((label, index) => {
            const value = materiasMap.get(label);
            const average = (value.sum / value.count).toFixed(2);
            const data = new Array(labels.length).fill(null);
            data[index] = average;
            const color = palette[index % palette.length];

            return {
                label: label,
                data: data,
                backgroundColor: color,
                borderColor: color.replace('0.7', '1'),
                borderWidth: 1
            };
        });

        if (window.mySubjectGradesChart) {
            window.mySubjectGradesChart.destroy();
        }

        window.mySubjectGradesChart = new Chart(subjectGradesChartCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Calificaciones por Materia'
                    },
                    tooltip: {
                        callbacks: {
                            // Only show tooltip for the non-null value in the dataset
                            filter: function(tooltipItem) {
                                return tooltipItem.raw !== null;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        beginAtZero: true,
                        max: 5,
                        stacked: true
                    }
                }
            }
        });
    }

    loadStudentPerformance();
});
