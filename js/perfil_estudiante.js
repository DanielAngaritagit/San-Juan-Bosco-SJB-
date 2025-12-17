/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
function populateProfileForm(user) {
    document.getElementById('nombres').value = user.nombres;
    document.getElementById('apellidos').value = user.apellidos;
    document.getElementById('tipo_documento').value = user.tipo_documento;
    document.getElementById('no_documento').value = user.no_documento;
    document.getElementById('fecha_expedicion').value = user.fecha_expedicion;
    document.getElementById('fecha_nacimiento').value = user.fecha_nacimiento;
    document.getElementById('sexo').value = user.sexo;
    document.getElementById('rh').value = user.rh;
    document.getElementById('direccion').value = user.direccion;
    document.getElementById('email').value = user.email;
    document.getElementById('telefono').value = user.telefono;
    document.getElementById('alergias').value = user.alergias;

    // Actualizar la imagen de perfil si existe
    const profileImgMain = document.getElementById('profile-img-main');
    if (profileImgMain && user.foto_url) {
        profileImgMain.src = user.foto_url;
    }
    const profileNameMain = document.getElementById('profile-name-main');
    if (profileNameMain) {
        profileNameMain.textContent = `${user.nombres} ${user.apellidos}`;
    }
    const profileRoleMain = document.getElementById('profile-role-main');
    if (profileRoleMain) {
        profileRoleMain.textContent = user.rol;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Event listener para el botón de cambio de foto
    const changePicBtn = document.getElementById('change-pic-btn');
    if (changePicBtn) {
        changePicBtn.addEventListener('click', function() {
            document.getElementById('profile-pic-input').click();
        });
    }

    const profilePicInput = document.getElementById('profile-pic-input');
    if (profilePicInput) {
        profilePicInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('profile_pic', file);

                fetch('../api/update_profile_pic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Foto de perfil actualizada con éxito.');
                        // Actualizar la imagen en la interfaz
                        const newPicUrl = data.new_pic_url + '?t=' + new Date().getTime(); // Añadir timestamp para evitar caché
                        document.getElementById('profile-img-main').src = newPicUrl;
                        // También actualizar la imagen en el menú lateral si existe
                        const profileImgAside = document.getElementById('profile-img');
                        if (profileImgAside) {
                            profileImgAside.src = newPicUrl;
                        }
                        // Actualizar en localStorage y en user_profile_manager
                        if (typeof updateUserProfilePic === 'function') {
                            updateUserProfilePic(newPicUrl);
                        }
                    } else {
                        alert('Error al actualizar la foto de perfil: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión al actualizar la foto de perfil.');
                });
            }
        });
    }

    // Manejo del formulario de perfil
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const userData = Object.fromEntries(formData.entries());

            fetch('../api/update_user_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Perfil actualizado con éxito.');
                    // Actualizar el nombre en la interfaz y en user_profile_manager
                    if (typeof updateUserProfileName === 'function') {
                        updateUserProfileName(userData.nombres, userData.apellidos);
                    }
                } else {
                    alert('Error al actualizar el perfil: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al actualizar el perfil.');
            });
        });
    }

    // Manejo del formulario de cambio de contraseña
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const currentPassword = document.getElementById('current-password').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                alert('La nueva contraseña y la confirmación no coinciden.');
                return;
            }

            fetch('../api/change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ current_password: currentPassword, new_password: newPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Contraseña actualizada con éxito.');
                    document.getElementById('password-form').reset();
                } else {
                    alert('Error al cambiar la contraseña: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión al cambiar la contraseña.');
            });
        });
    }

    // Listen for the custom event dispatched by user_profile_manager.js
    document.addEventListener('userProfileLoaded', function(event) {
        populateProfileForm(event.detail);
    });
});

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