/* =========================================
   DASHBOARD LOGIC (COMPLETE)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // =========================================
    // 1. SIDEBAR TOGGLE LOGIC (FITUR BARU)
    // =========================================
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    
    // Cek apakah elemen ada? (Biar aman)
    if (toggleBtn && sidebar) {
        
        // Event Klik Tombol <<
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            
            // Simpan status di LocalStorage (biar browser ingat pilihan user)
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarState', isCollapsed ? 'closed' : 'open');
        });

        // Cek status terakhir saat halaman diload ulang
        const savedState = localStorage.getItem('sidebarState');
        if (savedState === 'closed') {
            sidebar.classList.add('collapsed');
        }
    }

    // =========================================
    // 2. SETUP ELEMEN DASHBOARD
    // =========================================
    const ctx = document.getElementById('weightChart');
    const btnUpdate = document.getElementById('btnUpdateWeight');
    const inputWeight = document.getElementById('newWeight');
    const displayWeight = document.getElementById('displayWeight');
    const timeBtns = document.querySelectorAll('.time-btn');
    
    // FIX: Menggunakan querySelector untuk class karena di PHP tidak ada ID navLogoutBtn
    const navLogoutBtn = document.querySelector('.logout-link'); 

    let myChart; 

    // =========================================
    // 3. LOGIKA LOGOUT (SWEETALERT2)
    // =========================================
    if (navLogoutBtn) {
        navLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Tahan link logout asli
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your session.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Merah
                cancelButtonColor: '#64748b',  // Abu-abu
                confirmButtonText: 'Yes, Log Me Out'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php'; // Lanjut logout
                }
            });
        });
    }

    // =========================================
    // 4. RENDER CHART (CHART.JS)
    // =========================================
    function renderChart(labels, dataPoints) {
        if (!ctx) return; // Cegah error jika chart tidak ada di halaman ini (misal di halaman Course)

        if (myChart) myChart.destroy();
        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: dataPoints,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: false, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } }
                }
            }
        });
    }

    // =========================================
    // 5. FETCH DATA (GET)
    // =========================================
    async function loadChartData(range = '1W') {
        if (!ctx) return; // Skip jika di halaman Course

        try {
            const response = await fetch(`api_weight.php?range=${range}`);
            const result = await response.json();

            if (result.status === 'success') {
                const labels = result.data.map(item => item.label);
                const values = result.data.map(item => item.value);
                renderChart(labels, values);
            }
        } catch (error) {
            console.error("Error loading chart:", error);
        }
    }

    // =========================================
    // 6. TIMEFRAME BUTTONS
    // =========================================
    if (timeBtns) {
        timeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                timeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                loadChartData(btn.getAttribute('data-time'));
            });
        });
    }

    // =========================================
    // 7. UPDATE BERAT (POST)
    // =========================================
    if (btnUpdate) {
        btnUpdate.addEventListener('click', async () => {
            const weightVal = parseFloat(inputWeight.value);

            // Validasi Input
            if (!weightVal || weightVal <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Input',
                    text: 'Please enter a valid weight number!',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            const originalText = btnUpdate.innerText;
            btnUpdate.innerText = "Saving...";
            btnUpdate.disabled = true;

            try {
                const response = await fetch('api_weight.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ weight: weightVal })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // Update UI Dashboard
                    if(displayWeight) displayWeight.innerText = `${weightVal} kg`;
                    
                    // Update BMI (jika elemen ada)
                    const bmiValue = document.getElementById('bmiValue');
                    const bmiStatus = document.getElementById('bmiStatus');
                    if (bmiValue && result.new_bmi) bmiValue.innerText = result.new_bmi.score;
                    if (bmiStatus && result.new_bmi) {
                        bmiStatus.innerText = result.new_bmi.status;
                        bmiStatus.style.color = result.new_bmi.color;
                    }

                    // Refresh Chart & Reset Input
                    const activeBtn = document.querySelector('.time-btn.active');
                    const activeRange = activeBtn ? activeBtn.getAttribute('data-time') : '1W';
                    loadChartData(activeRange);
                    
                    inputWeight.value = '';

                    // Sukses Alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Great Job!',
                        text: `Weight updated to ${weightVal} kg.`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: result.message
                    });
                }

            } catch (error) {
                console.error("Error Updating:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Something went wrong. Try again.'
                });
            } finally {
                btnUpdate.innerText = originalText;
                btnUpdate.disabled = false;
            }
        });
    }

    // --- LOAD AWAL (Hanya jika di halaman dashboard yang punya chart) ---
    if (ctx) {
        loadChartData('1W'); 
    }
});

/* =========================================
   COURSE SEARCH LOGIC (REAL-TIME)
   ========================================= */
document.addEventListener('DOMContentLoaded', function() {
    
    const searchInput = document.getElementById('searchInput');
    const noResultMsg = document.getElementById('noResultMsg');

    // Cek apakah elemen ada? (Biar gak error di halaman selain Course)
    if (searchInput && noResultMsg) {
        
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const cards = document.querySelectorAll('.course-card');
            let hasResult = false;

            cards.forEach(card => {
                const titleElement = card.querySelector('.course-title');
                
                // Jaga-jaga kalau element title gak ketemu
                if (titleElement) {
                    const title = titleElement.textContent.toLowerCase();

                    if (title.includes(filter)) {
                        card.style.display = ""; // Tampilkan
                        hasResult = true;
                    } else {
                        card.style.display = "none"; // Sembunyikan
                    }
                }
            });

            // Toggle pesan "Not Found"
            if (!hasResult) {
                noResultMsg.style.display = "block";
            } else {
                noResultMsg.style.display = "none";
            }
        });
    }
});