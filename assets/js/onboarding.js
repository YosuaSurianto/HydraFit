/* LOGIKA ONBOARDING (CLIENT SIDE VALIDATION) */

document.addEventListener('DOMContentLoaded', () => {

    // --- STEP 2: CREATE PROFILE ---
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            // Validasi Sederhana: Cek input kosong
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
        });
    }

    // --- STEP 3: COMPLETE PROFILE ---
    const completeProfileForm = document.getElementById('completeProfileForm');
    if (completeProfileForm) {
        completeProfileForm.addEventListener('submit', (e) => {
            
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;

            // Validasi Sederhana: Angka harus masuk akal
            if (weight <= 0 || height <= 0) {
                e.preventDefault(); 
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Data',
                    text: 'Please enter valid weight and height.'
                });
            }
        });
    }
});