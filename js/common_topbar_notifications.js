// js/common_topbar_notifications.js

/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    loadNotificationCounts();
});

async function loadNotificationCounts() {
    try {
        // Cargar contador de correo
        const correoResponse = await fetch('../api/get_correo.php'); // Asumiendo que esta API devuelve el conteo de correos
        const correoData = await correoResponse.json();
        if (correoData.success && document.getElementById('correo-count')) {
            document.getElementById('correo-count').textContent = correoData.count;
        } else {
            console.error('Error al cargar conteo de correo:', correoData.message || 'Error desconocido');
        }

        // Cargar contador de notificaciones
        const notificationsResponse = await fetch('../api/get_notifications.php'); // Asumiendo que esta API devuelve el conteo de notificaciones
        const notificationsData = await notificationsResponse.json();
        if (notificationsData.success && document.getElementById('notification-count')) {
            document.getElementById('notification-count').textContent = notificationsData.count;
        }
    } catch (error) {
        console.error('Error de comunicación al cargar contadores de la top-bar:', error);
        // En caso de error, asegurar que los contadores muestren 0 o un valor por defecto
        if (document.getElementById('correo-count')) document.getElementById('correo-count').textContent = '0';
        if (document.getElementById('notification-count')) document.getElementById('notification-count').textContent = '0';
    }
}