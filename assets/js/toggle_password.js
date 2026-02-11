// Toggle Password Visibility Script

document.addEventListener("DOMContentLoaded", function() {
    
    const toggleBtn = document.getElementById("togglePasswordBtn");
    const passwordInput = document.getElementById("passwordInput");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    // Cek apakah elemen ada agar tidak error
    if (toggleBtn && passwordInput) {
        
        toggleBtn.addEventListener("click", function() {
            
            //Cek tipe input saat ini (password atau text?)
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            
            // Ubah tipe input
            passwordInput.setAttribute("type", type);
            
            // Ganti Icon
            if (type === "text") {
                // Mode Text (Kelihatan) -> Tampilkan Mata Terbuka
                eyeOpen.classList.remove("hidden");
                eyeClosed.classList.add("hidden");
            } else {
                // Mode Password (***) -> Tampilkan Mata Dicoret
                eyeOpen.classList.add("hidden");
                eyeClosed.classList.remove("hidden");
            }
        });
    }
});