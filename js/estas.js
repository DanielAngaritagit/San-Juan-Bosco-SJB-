// script.js
document.addEventListener('DOMContentLoaded', function () {
    const years = [2020, 2021, 2022, 2023];

    // Datos de Matrículas
    const matriculasData = {
        labels: years,
        datasets: [{
            label: 'Matrículas',
            data: [1500, 1600, 1700, 1800], // Ejemplo de datos
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };

    // Datos de Prematrículas
    const prematriculasData = {
        labels: years,
        datasets: [{
            label: 'Prematrículas',
            data: [500, 600, 700, 800], // Ejemplo de datos
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    };

    // Datos de Egresados
    const egresadosData = {
        labels: years,
        datasets: [{
            label: 'Egresados',
            data: [200, 250, 300, 350], // Ejemplo de datos
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1
        }]
    };

    // Configuración común para los gráficos
    const config = {
        type: 'bar',
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Crear gráficos
    new Chart(document.getElementById('matriculasChart'), {
        ...config,
        data: matriculasData
    });

    new Chart(document.getElementById('prematriculasChart'), {
        ...config,
        data: prematriculasData
    });

    new Chart(document.getElementById('egresadosChart'), {
        ...config,
        data: egresadosData
    });
});