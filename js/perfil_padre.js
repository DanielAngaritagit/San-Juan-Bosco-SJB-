/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
function populateProfileForm(profile) {
    if (!profile) return;

    // Populate main profile card header
    document.getElementById('profile-name-main').textContent = `${profile.nombre || ''} ${profile.apellido || ''}`;
    document.getElementById('profile-role-main').textContent = profile.rol || 'Rol no definido';
    const mainProfileImg = document.getElementById('profile-img-main');
    if (mainProfileImg) {
        mainProfileImg.src = profile.foto_url ? `../${profile.foto_url}` : '../multimedia/pagina_principal/usuario.png';
    }

    // Populate the form
    document.getElementById('nombre').value = profile.nombre || '';
    document.getElementById('apellido').value = profile.apellido || '';
    document.getElementById('email').value = profile.correo || '';
    document.getElementById('telefono').value = profile.telefono || '';
    document.getElementById('rh').value = profile.rh || '';
    document.getElementById('alergias').value = profile.alergias || '';
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
                    let userProfile = JSON.parse(localStorage.getItem('userProfile')) || {};
                    formData.forEach((value, key) => {
                        if (key === 'email') {
                            userProfile['correo'] = value;
                        } else {
                            userProfile[key] = value;
                        }
                    });
                    localStorage.setItem('userProfile', JSON.stringify(userProfile));
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