document.addEventListener('DOMContentLoaded', () => {
    function setupMenuToggle() {
        const menuToggle = document.getElementById('menu-toggle');
        const menuContainer = document.getElementById('menu-container');
        const mainContainer = document.querySelector('.main-container');

        if (menuToggle && menuContainer && mainContainer) {
            menuToggle.addEventListener('click', () => {
                menuContainer.classList.toggle('active');
                mainContainer.classList.toggle('menu-open');
            });

            document.addEventListener('click', (e) => {
                if (!menuContainer.contains(e.target) && !menuToggle.contains(e.target) && menuContainer.classList.contains('active')) {
                    menuContainer.classList.remove('active');
                    mainContainer.classList.remove('menu-open');
                }
            });
        }
    }

    setupMenuToggle();
});