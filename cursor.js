document.addEventListener('DOMContentLoaded', () => {
    const dot = document.getElementById('pccDot');
    const outline = document.getElementById('pccOutline');

    if (!dot || !outline) return;

    let mouseX = 0;
    let mouseY = 0;
    let outlineX = 0;
    let outlineY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        dot.style.left = mouseX + 'px';
        dot.style.top = mouseY + 'px';
    });

    function animateOutline() {
        // High-fidelity linear interpolation math for fluid rubber-band physics
        outlineX += (mouseX - outlineX) * 0.15;
        outlineY += (mouseY - outlineY) * 0.15;
        outline.style.left = outlineX + 'px';
        outline.style.top = outlineY + 'px';
        requestAnimationFrame(animateOutline);
    }
    requestAnimationFrame(animateOutline);

    const targetElements = document.querySelectorAll('a, button, .btn, input[type="submit"], select, textarea');
    targetElements.forEach((el) => {
        el.addEventListener('mouseenter', () => {
            outline.classList.add('pcc-cursor-hover');
        });
        el.addEventListener('mouseleave', () => {
            outline.classList.remove('pcc-cursor-hover');
        });
    });
});
