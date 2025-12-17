


document.addEventListener('DOMContentLoaded', function() {
    // ...existing code...

    // =====================================
    // MANEJO DE MENÚ PRINCIPAL Y SUBMENÚS
    // =====================================
    const menuToggle = document.getElementById('boton-menu');
    const menuContainer = document.getElementById('menu-movil');
    const cerrarMenuBtn = document.getElementById('cerrar-menu');
    const menusDesktop = document.querySelectorAll('.menu-desplegable');
    const submenusMovil = document.querySelectorAll('.con-submenu');

    // Validación inicial de elementos
    if (!menuToggle || !menuContainer) {
        console.error('Elementos principales del menú no encontrados');
        return;
    }

    // ====================
    // Funcionalidad Desktop
    // ====================
    menusDesktop.forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.preventDefault();
            // Cierra otros menús desplegables que puedan estar abiertos
            menusDesktop.forEach(m => m !== this && m.classList.remove('active'));
            // Alterna el estado activo del menú clicado
            this.classList.toggle('active');
        });
    });

    // Cerrar menús desktop al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.menu-desplegable')) {
            menusDesktop.forEach(menu => menu.classList.remove('active'));
        }
    });

    // ====================
    // Funcionalidad Móvil
    // ====================
    // Abrir menú principal móvil
    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menuContainer.classList.add('active');
    });

    // Cerrar menú móvil con el botón de cierre
    if (cerrarMenuBtn) {
        cerrarMenuBtn.addEventListener('click', (e) => {
            e.preventDefault();
            menuContainer.classList.remove('active');
        });
    }

    // Alternar submenús móviles
    submenusMovil.forEach(item => {
        const link = item.querySelector('a');
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isActive = item.classList.contains('active');
            
            // Cerrar otros submenús
            submenusMovil.forEach(menu => {
                if (menu !== item) menu.classList.remove('active');
            });
            
            // Alternar estado activo del submenú actual
            item.classList.toggle('active', !isActive);
        });
    });

    // Cerrar menú y submenús al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#menu-movil') && !e.target.closest('#boton-menu')) {
            menuContainer.classList.remove('active');
            submenusMovil.forEach(menu => menu.classList.remove('active'));
        }
    });

    // Resetear menú en cambio de tamaño de ventana a desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            menuContainer.classList.remove('active');
            submenusMovil.forEach(menu => menu.classList.remove('active'));
        }
    });

    // =====================================
    // CARRUSEL INTERACTIVO
    // =====================================
    const carruselContenedor = document.querySelector('.carrusel-contenedor');
    
    if (carruselContenedor) {
        const carruselImagenes = document.querySelectorAll('.carrusel-contenedor img');
        const btnAnterior = document.querySelector('.carrusel-anterior');
        const btnSiguiente = document.querySelector('.carrusel-siguiente');
        
        if (carruselImagenes.length > 0) {
            let contador = 0;
            let touchStartX = 0;
            let touchEndX = 0;
            let intervalo;

            const actualizarCarrusel = () => {
                carruselContenedor.style.transform = `translateX(-${contador * 100}%)`;
            };

            const siguienteImagen = () => {
                contador = (contador + 1) % carruselImagenes.length;
                actualizarCarrusel();
                reiniciarIntervalo();
            };

            const imagenAnterior = () => {
                contador = (contador - 1 + carruselImagenes.length) % carruselImagenes.length;
                actualizarCarrusel();
                reiniciarIntervalo();
            };

            // Control táctil (swipe)
            carruselContenedor.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            });

            carruselContenedor.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) siguienteImagen();
                if (touchStartX - touchEndX < -50) imagenAnterior();
            });

            // Controles de botones
            if(btnSiguiente) btnSiguiente.addEventListener('click', siguienteImagen);
            if(btnAnterior) btnAnterior.addEventListener('click', imagenAnterior);

            // Auto-play
            const iniciarIntervalo = () => {
                intervalo = setInterval(siguienteImagen, 5000);
            };

            const reiniciarIntervalo = () => {
                clearInterval(intervalo);
                iniciarIntervalo();
            };

            // Pausar en hover
            carruselContenedor.addEventListener('mouseenter', () => clearInterval(intervalo));
            carruselContenedor.addEventListener('mouseleave', iniciarIntervalo);

            iniciarIntervalo();
        }
    }

    // =====================================
    // CARGA Y ANIMACIÓN DE ESTADÍSTICAS
    // =====================================
    const cargarEstadisticas = async () => {
        try {
            const response = await fetch('php/estadisticas.php');
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();

            if (data.success && data.data) {
                animarContador(document.getElementById('studentCount'), parseInt(data.data.estudiantes, 10));
                animarContador(document.getElementById('teacherCount'), parseInt(data.data.profesores, 10));
                animarContador(document.getElementById('parentCount'), parseInt(data.data.padres, 10));
            } else {
                establecerValoresPorDefecto();
            }
        } catch (error) {
            console.error("Error al cargar estadísticas:", error);
            establecerValoresPorDefecto();
        }
    };

    const establecerValoresPorDefecto = () => {
        document.getElementById('studentCount').textContent = '0';
        document.getElementById('teacherCount').textContent = '0';
        document.getElementById('parentCount').textContent = '0';
    };
    
    // Función de animación de contadores solicitada
    const animarContador = (element, end) => {
        if (!element) return;
        
        let start = 0;
        let duration = 2000;

        if (end <= start) {
            element.textContent = end;
            return;
        }

        // Prevenir división por cero y asegurar que stepTime sea al menos 1
        let stepTime = Math.max(1, Math.floor(duration / (end - start)));
        
        let current = start;
        let timer = setInterval(() => {
            current++;
            element.textContent = current;
            if (current >= end) {
                clearInterval(timer);
                element.textContent = end; // Asegura que el valor final sea exacto
            }
        }, stepTime);
    };

    // Llama a cargarEstadisticas() solo si existe la sección de estadísticas
    if (document.querySelector('.stats')) {
        cargarEstadisticas();
    }


    // =====================================
    // MANEJO DE SCROLL SUAVE PARA ANCLAS
    // =====================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            try {
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            } catch (error) {
                console.error(`Selector de ancla inválido: ${targetId}`);
            }
        });
    });

    // =====================================
    // OTRAS INTERACCIONES
    // =====================================
    // Cerrar menú móvil al hacer clic en enlaces que no tienen submenú
    document.querySelectorAll('.lista-menu-movil > li > a').forEach(enlace => {
        if (!enlace.parentElement.classList.contains('con-submenu')) {
            enlace.addEventListener('click', () => {
                menuContainer.classList.remove('active');
            });
        }
    });
});
