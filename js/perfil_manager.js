/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
function populateProfileForm(profile) {
    if (!profile) return;

    const safelySetValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.value = value || '';
        }
    };

    const fullName = `${profile.nombres || ''} ${profile.apellidos || ''}`.trim();
    document.getElementById('profile-name-main').textContent = fullName || 'Nombre no disponible';
    document.getElementById('profile-role-main').textContent = profile.rol || 'Rol no definido';
    
    const mainProfileImg = document.getElementById('profile-img-main');
    if (mainProfileImg) {
        const base_url = window.location.origin + '/SJB/';
        const imageUrl = profile.foto_url ? `${base_url}${profile.foto_url}` : `${base_url}multimedia/pagina_principal/usuario.png`;
        mainProfileImg.src = `${imageUrl}?t=${new Date().getTime()}`;
    }

    const nombres = (profile.nombres || '').split(' ');
    safelySetValue('primer_nombre', nombres[0] || '');
    safelySetValue('segundo_nombre', nombres.slice(1).join(' ') || '');

    const apellidos = (profile.apellidos || '').split(' ');
    safelySetValue('primer_apellido', apellidos[0] || '');
    safelySetValue('segundo_apellido', apellidos.slice(1).join(' ') || '');

    safelySetValue('tipo_documento', profile.tipo_documento);
    safelySetValue('no_documento', profile.no_documento);
    safelySetValue('fecha_expedicion', profile.fecha_expedicion);
    safelySetValue('fecha_nacimiento', profile.fecha_nacimiento);
    safelySetValue('telefono', profile.telefono);
    safelySetValue('sexo', profile.sexo);
    safelySetValue('estado_civil', profile.estado_civil);
    safelySetValue('direccion', profile.direccion);
    safelySetValue('email', profile.email);
    safelySetValue('rh', profile.rh);
    safelySetValue('alergias', profile.alergias);
}

document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(profileForm);
            const button = profileForm.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Guardando...';

            try {
                const response = await fetch('../api/update_user_profile.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('¡Perfil actualizado con éxito!');
                    // Clear the localStorage to force a reload from the server
                    localStorage.removeItem('userProfile');
                    // Reload the profile data to reflect changes instantly
                    await loadAndDisplayUserProfile(); // Use await to ensure it completes
                } else {
                    alert('Error al actualizar el perfil: ' + result.message);
                }
            } catch (error) {
                alert('Error de red al actualizar el perfil: ' + error.message);
            } finally {
                button.disabled = false;
                button.textContent = 'Guardar Cambios';
            }
        });
    }

    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(passwordForm);
            const newPassword = formData.get('new-password');
            const confirmPassword = formData.get('confirm-password');

            if (newPassword !== confirmPassword) {
                alert('Las nuevas contraseñas no coinciden.');
                return;
            }
            
            const button = passwordForm.querySelector('button[type="submit"]');
            button.disabled = true;
            button.textContent = 'Cambiando...';

            try {
                const response = await fetch('../api/change_password.php', {
                    method: 'POST',
                    body: new FormData(passwordForm)
                });
                const result = await response.json();

                if (result.success) {
                    alert('¡Contraseña cambiada con éxito!');
                    passwordForm.reset();
                } else {
                    alert('Error al cambiar la contraseña: ' + result.message);
                }
            } catch (error) {
                alert('Error de red al cambiar la contraseña: ' + error.message);
            } finally {
                button.disabled = false;
                button.textContent = 'Cambiar Contraseña';
            }
        });
    }

    const changePicBtn = document.getElementById('change-pic-btn');
    const profilePicInput = document.getElementById('profile-pic-input');

    if (changePicBtn && profilePicInput) {
        changePicBtn.addEventListener('click', () => {
            profilePicInput.click();
        });

        profilePicInput.addEventListener('change', async () => {
            const file = profilePicInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_pic', file);

            changePicBtn.textContent = 'Subiendo...';
            changePicBtn.disabled = true;

            try {
                const response = await fetch('../api/update_profile_pic.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert('Foto de perfil actualizada con éxito.');
                    updateUserProfilePic(result.new_pic_url);
                } else {
                    alert('Error al actualizar la foto: ' + result.message);
                }
            } catch (error) {
                alert('Error de red al actualizar la foto: ' + error.message);
            } finally {
                changePicBtn.textContent = 'Cambiar Foto';
                changePicBtn.disabled = false;
            }
        });
    }
});