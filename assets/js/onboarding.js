/* =========================================
   LOGIKA ONBOARDING (STEP 2 & 3)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {

    // --- LOGIKA STEP 2 (create-profile.html) ---
    const profileForm = document.getElementById('profileForm');

    if (profileForm) {
        // 1. Cek validasi akses: Apakah user ini beneran habis dari register?
        // Kita ambil email "sementara" yang disimpan script.js di Step 1
        const currentEmail = localStorage.getItem('registeringEmail');

        if (!currentEmail) {
            // Kalau user nyasar langsung buka file ini tanpa register dulu, tendang balik
            alert("Akses Ditolak! Harap Sign Up terlebih dahulu.");
            window.location.href = 'register.html';
            return;
        }

        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // 2. Ambil data dari input Step 2
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const username = document.getElementById('username').value;
            
            // Gabung jadi Full Name
            const fullName = `${firstName} ${lastName}`; 

            // 3. Ambil database users dari localStorage
            let users = JSON.parse(localStorage.getItem('users')) || [];

            // 4. Cari user yang sedang mendaftar berdasarkan Email
            const userIndex = users.findIndex(u => u.email === currentEmail);

            if (userIndex !== -1) {
                // 5. UPDATE data user tersebut (masukkan nama)
                users[userIndex].name = fullName; 
                users[userIndex].username = username; 
                
                // 6. Simpan perubahan ke LocalStorage
                localStorage.setItem('users', JSON.stringify(users));

                // 7. Lanjut ke STEP 3
                // Redirect ke halaman data fisik
                window.location.href = 'complete-profile.html';
            } else {
                alert("Error: User tidak ditemukan. Silakan register ulang.");
                window.location.href = 'register.html';
            }
        });
    }


    // --- LOGIKA STEP 3 (complete-profile.html) ---
    const completeProfileForm = document.getElementById('completeProfileForm');

    if (completeProfileForm) {
        // 1. Cek validasi akses (sama kayak step 2, user harus punya sesi register)
        const currentEmail = localStorage.getItem('registeringEmail');
        
        if (!currentEmail) {
            alert("Akses Ditolak! Silakan login/register dulu.");
            window.location.href = 'register.html';
            return;
        }

        completeProfileForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // 2. Ambil data fisik dari input Step 3
            const birthDate = document.getElementById('birthDate').value;
            const gender = document.getElementById('gender').value;
            const bloodType = document.getElementById('bloodType').value;
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;

            // 3. Update Database User
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

                // 4. Redirect ke Dashboard (AKHIRNYA!)
                alert("Profil Sukses Dibuat! Selamat Datang di HydraFit.");
                
                // Pastikan file dashboard.html nanti ada ya
                window.location.href = 'dashboard.html'; 
            } else {
                alert("Error: User data corrupt. Silakan ulang.");
                window.location.href = 'register.html';
            }
        });
    }

});