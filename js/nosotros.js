// Mobile menu toggle
document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
    document.querySelector('nav ul').classList.toggle('show');
});

// Animations on scroll
function checkScroll() {
    const elements = document.querySelectorAll('.animated');
    elements.forEach(element => {
        const position = element.getBoundingClientRect();
        if (position.top < window.innerHeight - 100) {
            element.classList.add('in-view');
        }
    });
}

window.addEventListener('scroll', checkScroll);
window.addEventListener('load', checkScroll);

// Facilities carousel
const facilitiesInner = document.querySelector('.facilities-inner');
const facilitySlides = document.querySelectorAll('.facility-slide');
const facilityPrev = document.querySelector('.facility-prev');
const facilityNext = document.querySelector('.facility-next');
let currentFacilityIndex = 0;

function updateFacilitiesCarousel() {
    facilitiesInner.style.transform = `translateX(-${currentFacilityIndex * 100}%)`;
}

facilityNext.addEventListener('click', () => {
    currentFacilityIndex = (currentFacilityIndex + 1) % facilitySlides.length;
    updateFacilitiesCarousel();
});

facilityPrev.addEventListener('click', () => {
    currentFacilityIndex = (currentFacilityIndex - 1 + facilitySlides.length) % facilitySlides.length;
    updateFacilitiesCarousel();
});

// Auto slide for facilities
let facilityInterval = setInterval(() => {
    currentFacilityIndex = (currentFacilityIndex + 1) % facilitySlides.length;
    updateFacilitiesCarousel();
}, 5000);

// Pause on hover
const facilitiesCarousel = document.querySelector('.facilities-carousel');
facilitiesCarousel.addEventListener('mouseenter', () => {
    clearInterval(facilityInterval);
});

facilitiesCarousel.addEventListener('mouseleave', () => {
    facilityInterval = setInterval(() => {
        currentFacilityIndex = (currentFacilityIndex + 1) % facilitySlides.length;
        updateFacilitiesCarousel();
    }, 5000);
});

// Animated counters
function animateCounter(elementId, finalValue, duration) {
    let startTime = null;
    const element = document.getElementById(elementId);
    
    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const value = Math.floor(progress * finalValue);
        element.textContent = value;
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            element.textContent = finalValue;
        }
    }
    
    window.requestAnimationFrame(step);
}

// Start counters when stats section is in view
const statsSection = document.querySelector('.stats-section');
let countersAnimated = false;

function checkCounters() {
    const position = statsSection.getBoundingClientRect();
    if (position.top < window.innerHeight - 100 && !countersAnimated) {
        animateCounter('years-count', 33, 2000);
        animateCounter('students-count', 1250, 2000);
        animateCounter('teachers-count', 65, 2000);
        animateCounter('programs-count', 12, 2000);
        countersAnimated = true;
    }
}

window.addEventListener('scroll', checkCounters);
window.addEventListener('load', checkCounters);