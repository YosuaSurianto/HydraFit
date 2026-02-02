/* LOGIKA WELCOME PAGE */

document.addEventListener('DOMContentLoaded', () => {
    // Tombol GET STARTED
    const btnGetStarted = document.getElementById('btnGetStarted');
    
    if (btnGetStarted) {
        btnGetStarted.addEventListener('click', () => {
            // Redirect FINAL ke Dashboard
            window.location.href = 'dashboard.php';
        });
    }

});