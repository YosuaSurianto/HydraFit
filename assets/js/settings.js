/* LOGIC HALAMAN SETTINGS */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. LOGIC PREVIEW AVATAR
    // Agar saat user pilih file, gambarnya langsung muncul tanpa refresh
    const avatarInput = document.getElementById('avatarInput');
    const imagePreview = document.getElementById('imagePreview');
    const initialAvatar = document.getElementById('initialAvatar');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Cek ukuran file (Max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Please upload an image smaller than 2MB.'
                    });
                    this.value = ''; // Reset input
                    return;
                }

                // Tampilkan Preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    if (initialAvatar) initialAvatar.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    }

});