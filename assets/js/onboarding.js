/* =========================================
   LOGIKA ONBOARDING (STEP 2 & 3)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {

    // --- LOGIKA STEP 2 (create-profile.php) ---
    const profileForm = document.getElementById('profileForm');

    if (profileForm) {
        // Cek validasi akses: Apakah user ini beneran habis dari register?
        // ambil email "sementara" yang disimpan script.js di Step 1
        const currentEmail = localStorage.getItem('registeringEmail');

        if (!currentEmail) {
            // Kalau user nyasar langsung buka file ini tanpa register dulu, tendang balik
            alert("Akses Ditolak! Harap Sign Up terlebih dahulu.");
            window.location.href = 'register.php';
            return;
        }

        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Ambil data dari input Step 2
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const username = document.getElementById('username').value;
            
            // Gabung jadi Full Name
            const fullName = `${firstName} ${lastName}`; 

            // Ambil database users dari localStorage
            let users = JSON.parse(localStorage.getItem('users')) || [];

            // Cari user yang sedang mendaftar berdasarkan Email
            const userIndex = users.findIndex(u => u.email === currentEmail);

            if (userIndex !== -1) {
                // UPDATE data user tersebut (masukkan nama)
                users[userIndex].name = fullName; 
                users[userIndex].username = username; 
                
                // Simpan perubahan ke LocalStorage
                localStorage.setItem('users', JSON.stringify(users));

                // Lanjut ke STEP 3
                window.location.href = 'complete-profile.php';
            } else {
                alert("Error: User tidak ditemukan. Silakan register ulang.");
                window.location.href = 'register.php';
            }
        });
    }


    // --- LOGIKA STEP 3 (complete-profile.php) ---
    const completeProfileForm = document.getElementById('completeProfileForm');

    if (completeProfileForm) {
        // Cek validasi akses (sama kayak step 2, user harus punya sesi register)
        const currentEmail = localStorage.getItem('registeringEmail');
        
        if (!currentEmail) {
            alert("Akses Ditolak! Silakan login/register dulu.");
            window.location.href = 'register.php';
            return;
        }

        completeProfileForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Ambil data fisik dari input Step 3
            const birthDate = document.getElementById('birthDate').value;
            const gender = document.getElementById('gender').value;
            const bloodType = document.getElementById('bloodType').value;
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;

            // Update Database User
            let users = JSON.parse(localStorage.getItem('users')) || [];
            const userIndex = users.findIndex(u => u.email === currentEmail);

            if (userIndex !== -1) {
                // Masukkan Data Fisik ke objek user
                users[userIndex].birthdate = birthDate;
                users[userIndex].gender = gender;
                users[userIndex].blood = bloodType;
                users[userIndex].weight = weight;
                users[userIndex].height = height;
                
                // PENTING: Set Status Setup SELESAI (True)
                // Ini kunci agar user bisa login di masa depan
                users[userIndex].isSetupDone = true; 

                // Simpan update ke LocalStorage
                localStorage.setItem('users', JSON.stringify(users));
                
                // Auto-login user ini (simpan ke currentUser biar langsung masuk)
                localStorage.setItem('currentUser', JSON.stringify(users[userIndex]));
                
                // Hapus sesi register sementara (bersih-bersih) karena sudah selesai
                localStorage.removeItem('registeringEmail');

               // Redirect ke WELCOME PAGE 
               window.location.href = 'welcome.php';
            } else {
                alert("Error: User data corrupt. Silakan ulang.");
                window.location.href = 'register.php';
            }
        });
    }

});