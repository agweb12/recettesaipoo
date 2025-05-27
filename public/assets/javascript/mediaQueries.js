// Si la taille de mon écran est inférieure à 768px ou égale, je cache les span au sein des <a> de la <nav>
const mediaQuery = window.matchMedia('(max-width: 768px)');
function handleMediaQueryChange(e) {
  const navLinks = document.querySelectorAll('a span');
  const navButtons = document.querySelectorAll('nav a.cta span');
  if (e.matches) {
    // Si la condition est vraie, je cache les span
    navLinks.forEach(link => {
      link.style.display = 'none';
    });
    navButtons.forEach(link => {
      link.style.display = 'none';
    });
  } else {
    // Sinon, je les affiche
    navLinks.forEach(link => {
      link.style.display = 'inline';
    });
    navButtons.forEach(link => {
      link.style.display = 'inline';
    });
  }
}
// J'appelle la fonction une première fois pour appliquer le style au chargement de la page
handleMediaQueryChange(mediaQuery);
// J'ajoute un écouteur d'événement pour surveiller les changements de la media query
mediaQuery.addEventListener('change', handleMediaQueryChange);