/* =========================================
   LOGIKA WELCOME PAGE
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {

    // Ambil data user yang sedang login dari LocalStorage
    // (Data ini sudah disimpan oleh Step 3 sebelumnya)
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));

    // Tampilkan Username di Navbar
    const displayUsername = document.getElementById('displayUsername');
    
    if (currentUser && currentUser.username) {
        // Kalau ada usernya, tampilkan namanya
        displayUsername.textContent = currentUser.username;
    } else {
        // Kalau error/gak ada data (misal nyasar), tampilkan Guest
        displayUsername.textContent = "Guest";
        // Opsional: Tendang balik ke login
        // window.location.href = 'login.php';
    }

    // Tombol GET STARTED
    const btnGetStarted = document.getElementById('btnGetStarted');
    
    if (btnGetStarted) {
        btnGetStarted.addEventListener('click', () => {
            // Redirect FINAL ke Dashboard
            window.location.href = 'dashboard.php';
        });
    }

});