document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.features-carousel-container');
    const slides = document.querySelectorAll('.feature-slide');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    const indicators = document.querySelectorAll('.indicator');
    
    // Vérifier que les éléments nécessaires existent
    if (!carousel || slides.length === 0) {
        return;
    }

    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Initialisation
    updateCarousel();
    
    // Navigation par boutons
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateCarousel();
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateCarousel();
        });
    }
    
    // Navigation par indicateurs
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            updateCarousel();
        });
    });
    
    // Mise à jour de l'état du carousel
    function updateCarousel() {
        if (carousel) {
            carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
            
            // Mise à jour des indicateurs
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
        }
    }
    
    // Défilement automatique (optionnel)
    const autoSlide = setInterval(() => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateCarousel();
    }, 5000); // Change de slide toutes les 5 secondes
    
    // Arrêter le défilement automatique au survol
    const carouselContainer = document.querySelector('.features-carousel');
    if (carouselContainer) {
        carouselContainer.addEventListener('mouseenter', () => {
            clearInterval(autoSlide);
        });
    }
});