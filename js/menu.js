/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const mainContainer = document.querySelector('.main-container');
    const contentContainer = document.querySelector('.content-container');

    if (menuToggle && menuContainer) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            menuContainer.classList.toggle('active');
            
            // Compatibilidad con las dos estructuras de layout
            if (mainContainer) {
                mainContainer.classList.toggle('active');
            }
            if (contentContainer) {
                contentContainer.classList.toggle('full-width');
            }
        });

        // Opcional: cerrar el menú si se hace clic fuera (especialmente para móviles)
        document.addEventListener('click', function(e) {
            const isClickInsideMenu = menuContainer.contains(e.target);
            const isClickOnToggle = menuToggle.contains(e.target);

            if (!isClickInsideMenu && !isClickOnToggle && menuContainer.classList.contains('active')) {
                menuContainer.classList.remove('active');
                if (mainContainer) {
                    mainContainer.classList.remove('active');
                }
                if (contentContainer) {
                    contentContainer.classList.remove('full-width');
                }
            }
        });
    }
});
