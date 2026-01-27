/* =========================================
   LOGIKA ONBOARDING (CLIENT SIDE VALIDATION)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {

    // --- STEP 2: CREATE PROFILE ---
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            // Kita HAPUS e.preventDefault() supaya form tetap terkirim ke PHP
            
            // Validasi Sederhana (Opsional)
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();

            if (!firstName || !lastName) {
                e.preventDefault(); // Cegah kirim kalau kosong
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Data',
                    text: 'Please fill in all fields correctly.'
                });
            }
            // Jika valid, biarkan browser mengirim data ke create-profile.php
        });
    }

    // --- STEP 3: COMPLETE PROFILE ---
    const completeProfileForm = document.getElementById('completeProfileForm');
    if (completeProfileForm) {
        completeProfileForm.addEventListener('submit', (e) => {
            // Kita HAPUS e.preventDefault()
            
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;

            if (weight <= 0 || height <= 0) {
                e.preventDefault(); // Cegah kalau data tidak masuk akal
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Data',
                    text: 'Please enter valid weight and height.'
                });
            }
            // Jika valid, biarkan browser mengirim data ke complete-profile.php
        });
    }
});