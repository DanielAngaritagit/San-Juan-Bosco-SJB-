// Mobile menu toggle
document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
    document.querySelector('nav ul').classList.toggle('show');
});

// News filtering functionality
const filterButtons = document.querySelectorAll('.filter-btn');
const newsCards = document.querySelectorAll('.news-card');
const searchInput = document.getElementById('news-search');

// Filter by category
filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        const filter = button.getAttribute('data-filter');
        
        // Update active button
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        
        // Filter news cards
        newsCards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-category') === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Search functionality
searchInput.addEventListener('input', () => {
    const searchTerm = searchInput.value.toLowerCase();
    
    newsCards.forEach(card => {
        const title = card.querySelector('.news-title').textContent.toLowerCase();
        const excerpt = card.querySelector('.news-excerpt').textContent.toLowerCase();
        const category = card.getAttribute('data-category');
        
        if (title.includes(searchTerm) || excerpt.includes(searchTerm) || category.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Pagination functionality
const paginationButtons = document.querySelectorAll('.pagination-btn');
        
paginationButtons.forEach(button => {
    button.addEventListener('click', () => {
        paginationButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
        // In a real implementation, this would load the next page of news
    });
});

// Newsletter form validation
const newsletterForm = document.querySelector('.newsletter-form');
        
newsletterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const emailInput = newsletterForm.querySelector('input[type="email"]');
    const email = emailInput.value;
    
    if (validateEmail(email)) {
        // In a real implementation, this would send the email to a server
        alert('¡Gracias por suscribirte a nuestro boletín!');
        emailInput.value = '';
    } else {
        alert('Por favor, introduce una dirección de correo electrónico válida.');
    }
});
        
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}