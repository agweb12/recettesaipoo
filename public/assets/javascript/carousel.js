document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.features-carousel-container');
    const slides = document.querySelectorAll('.feature-slide');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    const indicators = document.querySelectorAll('.indicator');
    
    let currentIndex = 0;
    const totalSlides = slides.length;
    
    // Initialisation
    updateCarousel();
    
    // Navigation par boutons
    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateCarousel();
    });
    
    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateCarousel();
    });
    
    // Navigation par indicateurs
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            currentIndex = index;
            updateCarousel();
        });
    });
    
    // Mise à jour de l'état du carousel
    function updateCarousel() {
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
    
    // Défilement automatique (optionnel)
    const autoSlide = setInterval(() => {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateCarousel();
    }, 5000); // Change de slide toutes les 5 secondes
    
    // Arrêter le défilement automatique au survol
    document.querySelector('.features-carousel').addEventListener('mouseenter', () => {
        clearInterval(autoSlide);
    });
});