/* MANAGE USERS LOGIC */

document.addEventListener('DOMContentLoaded', function() {
    
    // REAL-TIME SEARCH
    const searchInput = document.getElementById('userSearch');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.user-row'); // Ambil semua baris user
            
            rows.forEach(row => {
                // Ambil Nama dan Email dari baris tersebut
                const name = row.querySelector('.user-name').textContent.toLowerCase();
                const email = row.querySelector('.user-email').textContent.toLowerCase();
                
                // Cek apakah ada yang cocok
                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = ""; // Tampilkan
                } else {
                    row.style.display = "none"; // Sembunyikan
                }
            });
        });
    }
});

// SWEETALERT DELETE CONFIRMATION
function confirmDeleteUser(userId) {
    Swal.fire({
        title: 'Delete User?',
        text: "This user will be permanently removed!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Yes, delete!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = '?delete=' + userId;
        }
    })
}