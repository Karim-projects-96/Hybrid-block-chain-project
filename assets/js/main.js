document.addEventListener('DOMContentLoaded', () => {
    // Basic frontend interactions could go here
    console.log("LuxBlock frontend loaded.");

    // Flash messages dismissal
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
