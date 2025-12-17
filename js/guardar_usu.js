// Funcionalidad de pestañas
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        // Remover clase active de todos los botones y contenidos
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Agregar clase active al botón clickeado
        button.classList.add('active');
        
        // Mostrar el contenido correspondiente
        const tabId = button.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
        
        // Ocultar mensajes
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';
    });
});
// Validación de formularios y simulación de envío
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        const requiredFields = this.querySelectorAll('[required]');
        
        // Resetear estilos
        requiredFields.forEach(field => {
            field.style.borderColor = '';
            field.style.backgroundColor = '';
        });
        
        // Validar campos obligatorios
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#e53935';
                field.style.backgroundColor = '#ffebee';
            }
        });
        
        if (!isValid) {
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('success-message').style.display = 'none';
            
            // Scroll al mensaje de error
            document.getElementById('error-message').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center'
            });
        } else {
            // Determinar el tipo de usuario basado en el ID del formulario
            let userType;
            if (this.id === 'form-acudiente') {
                userType = 'acudiente';
            } else if (this.id === 'form-profesor') {
                userType = 'profesor';
            } else if (this.id === 'form-estudiante') {
                userType = 'estudiante';
            }

            const formData = new FormData(this);
            formData.append('user_type', userType);

            fetch('guardar_usuario.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('success-message').style.display = 'block';
                    document.getElementById('error-message').style.display = 'none';
                    document.getElementById('success-message').textContent = data.message;
                    this.reset();
                } else {
                    document.getElementById('error-message').style.display = 'block';
                    document.getElementById('success-message').style.display = 'none';
                    document.getElementById('error-message').textContent = data.message;
                }
                // Scroll al mensaje
                (data.success ? document.getElementById('success-message') : document.getElementById('error-message')).scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('success-message').style.display = 'none';
                document.getElementById('error-message').textContent = 'Error al conectar con el servidor.';
                document.getElementById('error-message').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });
        }
    });
});
// Simular datos de prueba en el formulario de Acudiente
document.addEventListener('DOMContentLoaded', function() {
    // Datos de prueba para acudiente
    document.getElementById('nombres').value = 'María Fernanda';
    document.getElementById('apellidos').value = 'Gómez Rodríguez';
    document.getElementById('tipo_documento').value = 'CC';
    document.getElementById('no_documento').value = '1098765432';
    document.getElementById('fecha_nacimiento').value = '1985-08-22';
    document.getElementById('sexo').value = 'F';
    document.getElementById('parentesco').value = 'Madre';
    document.getElementById('celular').value = '3001234567';
    document.getElementById('email').value = 'maria.gomez@example.com';
    document.getElementById('direccionp').value = 'Calle 123 #45-67';
    document.getElementById('ciudad_expedicion').value = 'Bogotá';
    document.getElementById('lugar_recidencia').value = 'Bogotá';
    document.getElementById('ocupacion').value = 'Ingeniera de Sistemas';
});