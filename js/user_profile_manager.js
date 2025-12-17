/**
 * user_profile_manager.js
 * 
 * Este script gestiona de forma centralizada la carga, almacenamiento y visualización
 * de los datos del perfil del usuario (nombre, rol, foto) utilizando localStorage
 * para asegurar consistencia en toda la aplicación.
 */

/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    try {
        loadAndDisplayUserProfile();
    } catch (e) {
        alert("Error crítico al cargar el perfil de usuario: " + e.message);
        console.error("Error crítico en user_profile_manager: ", e);
    }

    
});

async function loadAndDisplayUserProfile() {
    // FORZAR LA CARGA DESDE LA API PARA DEPURACIÓN
    let userProfile;
    try {
        const response = await fetch('../api/get_user_profile.php?t=' + new Date().getTime()); // Prevenir caché del navegador
        const result = await response.json();

        if (result.success) {
            userProfile = result.data;
            localStorage.setItem('userProfile', JSON.stringify(userProfile)); // Actualizar caché
        } else {
            const sessionData = result.session_data ? JSON.stringify(result.session_data) : 'No hay datos de sesión.';
            alert(`Error al obtener el perfil:\n- Mensaje: ${result.message}\n- Datos de Sesión: ${sessionData}`);
            console.error('Error al obtener el perfil del usuario:', result.message);
            userProfile = getDefaultProfile();
        }
    } catch (error) {
        alert("Error de red o JSON al obtener el perfil: " + error.message);
        console.error('Error de red al obtener el perfil:', error);
        userProfile = getDefaultProfile();
    }

    displayProfileData(userProfile);

    const event = new CustomEvent('userProfileLoaded', { detail: userProfile });
    document.dispatchEvent(event);
}

function displayProfileData(profile) {
    const userNameElements = document.querySelectorAll('.user-name');
    const userRoleElements = document.querySelectorAll('.user-role');
    const profilePicElements = document.querySelectorAll('.profile-pic');

    // Fallback for different name structures
    const firstName = profile.nombres || profile.nombre || '';
    const lastName = profile.apellidos || profile.apellido || '';
    const fullName = `${firstName} ${lastName}`.trim();

    if (userNameElements.length > 0) {
        userNameElements.forEach(el => el.textContent = fullName || 'Usuario Invitado');
    }
    if (userRoleElements.length > 0) {
        userRoleElements.forEach(el => el.textContent = profile.rol || 'Rol no disponible');
    }
    if (profilePicElements.length > 0) {
        const base_url = window.location.origin + '/SJB/';
        // Asegura que la URL de la foto de perfil se resuelve correctamente
        const imageUrl = profile.foto_url ? (profile.foto_url.startsWith('http') ? profile.foto_url : `${base_url}${profile.foto_url}`) : `${base_url}multimedia/pagina_principal/usuario.png`;
        profilePicElements.forEach(el => el.src = `${imageUrl}?t=${new Date().getTime()}`);
    }
}

function updateUserProfilePic(newPicUrl) {
    let userProfile = JSON.parse(localStorage.getItem('userProfile'));
    if (userProfile) {
        userProfile.foto_url = newPicUrl;
        localStorage.setItem('userProfile', JSON.stringify(userProfile));
        displayProfileData(userProfile); // Actualiza la UI
    }
}

function updateUserProfileName(firstName, lastName) {
    let userProfile = JSON.parse(localStorage.getItem('userProfile'));
    if (userProfile) {
        userProfile.nombres = firstName;
        userProfile.apellidos = lastName;
        localStorage.setItem('userProfile', JSON.stringify(userProfile));
        displayProfileData(userProfile); // Actualiza la UI
    }
}

function getDefaultProfile() {
    return {
        nombres: 'Usuario',
        apellidos: 'Invitado',
        rol: 'Desconocido',
        foto_url: 'multimedia/pagina_principal/usuario.png'
    };
}