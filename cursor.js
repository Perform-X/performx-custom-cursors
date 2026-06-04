document.addEventListener('DOMContentLoaded', () => {
    const dot = document.getElementById('pccDot');
    const outline = document.getElementById('pccOutline');

    if (!dot || !outline) return;

    // Mouse positions
    let mouseX = 0;
    let mouseY = 0;
    
    // Outline positions (delayed for trailing effect)
    let outlineX = 0;
    let outlineY = 0;

    // Track real mouse position
    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        // Immediate update for the solid center dot
        dot.style.left = mouseX + 'px';
        dot.style.top = mouseY + 'px';
    });

    // Smooth physics loop for the trailing outer outline
    function animateOutline() {
        // Simple linear interpolation calculation for damping/smoothing speed
        outlineX += (mouseX - outlineX) * 0.15;
        outlineY += (mouseY - outlineY) * 0.15;

        outline.style.left = outlineX + 'px';
        outline.style.top = outlineY + 'px';

        requestAnimationFrame(animateOutline);
    }
    requestAnimationFrame(animateOutline);

    // Add scale-up effect when hovering over interactive elements
    const interactiveElements = document.querySelectorAll('a, button, .btn, input[type="submit"]');
    interactiveElements.forEach((el) => {
        el.addEventListener('mouseenter', () => {
            outline.classList.add('pcc-cursor-hover');
        });
        el.addEventListener('mouseleave', () => {
            outline.classList.add('pcc-cursor-hover');
        });
    });
});
