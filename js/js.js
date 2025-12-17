function toggleMenu() {
    var menu = document.getElementById("menu");
    menu.classList.toggle("active");
}

let currentSlide = 0;
const slides = document.querySelectorAll('.carrusel-item');
const buttons = document.querySelectorAll('.carrusel-button');
const totalSlides = slides.length;

function goToSlide(slideIndex) {
    currentSlide = slideIndex;
    updateCarousel();
    updateButtons();
}

function updateCarousel() {
    const offset = -currentSlide * 100;
    document.querySelector('.carrusel-inner').style.transform = `translateX(${offset}%)`;
}

function updateButtons() {
    buttons.forEach((button, index) => {
        if (index === currentSlide) {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateCarousel();
    updateButtons();
}

// Inicializar el carrusel
updateButtons();

// Configura el intervalo para cambiar de slide cada 5 segundos (5000 milisegundos)
setInterval(nextSlide, 1000);

/**/
    // Función para obtener el número de usuarios desde el backend
    async function fetchUserCount() {
        try {
            const response = await fetch('/api/user-count'); // Cambia esta URL por la ruta correcta de tu backend
            const data = await response.json();
            const userCount = data.count;

            // Animar el contador
            animateCounter(userCount);
        } catch (error) {
            console.error('Error fetching user count:', error);
        }
    }

    // Función para animar el contador
    function animateCounter(targetCount) {
        const counterElement = document.getElementById('userCount');
        const duration = 2000; // Duración de la animación en milisegundos
        const startTime = performance.now();

        function updateCounter(currentTime) {
            const elapsedTime = currentTime - startTime;
            const progress = Math.min(elapsedTime / duration, 1);
            const currentCount = Math.floor(progress * targetCount);

            counterElement.textContent = currentCount;

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            } else {
                counterElement.textContent = targetCount;
            }
        }

        requestAnimationFrame(updateCounter);
    }