function getProfileImageUrl(fotoUrl) {
    if (!fotoUrl) return ''; // O una imagen por defecto
    if (fotoUrl.startsWith('http') || fotoUrl.startsWith('/')) {
        return fotoUrl;
    }
    return `../${fotoUrl}`;
}

async function loadUserProfile() {
    try {
        const response = await fetch('../api/get_user_profile.php');
        if (!response.ok) throw new Error(`Error HTTP! estado: ${response.status}`);
        const data = await response.json();

        if (data.success) {
            const profileName = document.getElementById('profile-name');
            const profileRole = document.getElementById('profile-role');
            const profileImg = document.getElementById('profile-img');

            if (profileName) profileName.textContent = data.data.nombre;
            if (profileRole) profileRole.textContent = data.data.rol.charAt(0).toUpperCase() + data.data.rol.slice(1);
            if (profileImg && data.data.foto_url) {
                profileImg.src = `${getProfileImageUrl(data.data.foto_url)}?t=${new Date().getTime()}`;
            }
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

/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    

    // Cargar perfil de usuario al cargar la página
    loadUserProfile();
});