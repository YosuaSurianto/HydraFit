document.addEventListener('DOMContentLoaded', function () {

    // --- PREVIEW FOTO  ---
    const avatarInput = document.getElementById('avatarInput');
    const imagePreview = document.getElementById('imagePreview');
    const initialAvatar = document.getElementById('initialAvatar');

    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error', 'File max 2MB!', 'error');
                    this.value = ''; return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    if (initialAvatar) initialAvatar.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // --- LOGIC SAVE PROFILE (AJAX) ---
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Ambil Tombol buat efek loading
            const btn = profileForm.querySelector('.btn-save');
            const originalText = btn.innerText;
            btn.innerText = 'Saving...';
            btn.disabled = true;

            // Siapkan Data
            const formData = new FormData();
            formData.append('action', 'update_profile'); // Penanda untuk PHP
            formData.append('username', document.getElementById('username').value);
            formData.append('first_name', document.getElementById('firstName').value);
            formData.append('last_name', document.getElementById('lastName').value);
            formData.append('height', document.getElementById('height').value);
            formData.append('target_weight', document.getElementById('targetWeight').value);

            // Cek apakah user upload foto
            if (avatarInput.files.length > 0) {
                formData.append('avatar', avatarInput.files[0]);
            }

            try {
                const req = await fetch('api_settings.php', { method: 'POST', body: formData });
                const res = await req.json();
                // SweetAlert success or error based on response
                if (res.status === 'success') {
                    Swal.fire('Saved!', res.message, 'success').then(() => {
                        location.reload(); // Refresh biar foto profil di pojok kanan update
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Something went wrong!', 'error');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });
    }

    // --- LOGIC GANTI PASSWORD ---
    const securityForm = document.getElementById('securityForm');
    if (securityForm) {
        securityForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const current = document.getElementById('currentPass').value;
            const newP = document.getElementById('newPass').value;
            const confirmP = document.getElementById('confirmPass').value;

            if (newP !== confirmP) {
                Swal.fire('Error', 'New passwords do not match!', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('current_password', current);
            formData.append('new_password', newP);

            try {
                const req = await fetch('api_settings.php', { method: 'POST', body: formData });
                const res = await req.json();

                if (res.status === 'success') {
                    Swal.fire('Success', res.message, 'success');
                    securityForm.reset();
                } else {
                    Swal.fire('Failed', res.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'System Error', 'error');
            }
        });
    }

    // --- LOGIC DELETE ACCOUNT ---
    const btnDelete = document.getElementById('btnDeleteAccount');
    if (btnDelete) {
        btnDelete.addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request delete
                    const formData = new FormData();
                    formData.append('action', 'delete_account');

                    fetch('api_settings.php', { method: 'POST', body: formData })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                window.location.href = 'login.php'; // Tendang ke login
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        });
                }
            });
        });
    }
});