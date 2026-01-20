/* =========================================
   DASHBOARD LOGIC (FINAL + CUSTOM ALERTS)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. SETUP ELEMEN ---
    const ctx = document.getElementById('weightChart');
    const btnUpdate = document.getElementById('btnUpdateWeight');
    const inputWeight = document.getElementById('newWeight');
    const displayWeight = document.getElementById('displayWeight');
    const timeBtns = document.querySelectorAll('.time-btn');

    // --- SETUP MODAL ELEMENTS ---
    const customAlert = document.getElementById('customAlert');
    const alertIcon = document.getElementById('alertIcon');
    const alertTitle = document.getElementById('alertTitle');
    const alertMessage = document.getElementById('alertMessage');
    const closeAlertBtn = document.getElementById('closeAlertBtn');

    const logoutModal = document.getElementById('logoutModal');
    const navLogoutBtn = document.getElementById('navLogoutBtn');
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
    const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');

    let myChart; 

    // --- 2. FUNGSI ALERT PINTAR (GANTI ALERT BAWAAN) ---
    function showAlert(type, title, message) {
        if (!customAlert) return;

        // Reset Kelas Warna
        alertIcon.className = 'modal-icon-wrapper'; // Hapus semua warna dulu
        
        let iconSVG = '';

        if (type === 'success') {
            alertIcon.classList.add('success'); // Tambah warna hijau
            // Icon Centang
            iconSVG = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        } else if (type === 'error') {
            alertIcon.classList.add('error'); // Tambah warna merah
            // Icon Silang (X)
            iconSVG = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        }

        // Isi Konten Modal
        alertIcon.innerHTML = iconSVG;
        alertTitle.innerText = title;
        alertMessage.innerText = message;

        // Munculkan Modal
        customAlert.classList.remove('hidden');
    }

    // Event Tutup Alert
    if (closeAlertBtn) {
        closeAlertBtn.addEventListener('click', () => {
            customAlert.classList.add('hidden');
        });
    }

    // --- 3. LOGIKA LOGOUT (MODAL KONFIRMASI) ---
    if (navLogoutBtn) {
        navLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Cegah link langsung jalan
            logoutModal.classList.remove('hidden'); // Munculkan modal logout
        });
    }

    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', () => {
            logoutModal.classList.add('hidden'); // Batal logout
        });
    }

    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', () => {
            window.location.href = 'logout.php'; // Gass logout beneran
        });
    }

    // --- 4. RENDER CHART (Sama seperti sebelumnya) ---
    function renderChart(labels, dataPoints) {
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

    // --- 5. FETCH DATA (GET) ---
    async function loadChartData(range = '1W') {
        try {
            const response = await fetch(`api_weight.php?range=${range}`);
            const result = await response.json();

            if (result.status === 'success') {
                const labels = result.data.map(item => item.label);
                const values = result.data.map(item => item.value);
                renderChart(labels, values);
            }
        } catch (error) {
            console.error("Error:", error);
        }
    }

    // --- 6. TIMEFRAME BUTTONS ---
    timeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            timeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            loadChartData(btn.getAttribute('data-time'));
        });
    });

    // --- 7. UPDATE BERAT (POST) ---
    if (btnUpdate) {
        btnUpdate.addEventListener('click', async () => {
            const weightVal = parseFloat(inputWeight.value);

            // GANTI ALERT BAWAAN DISINI
            if (!weightVal || weightVal <= 0) {
                showAlert('error', 'Invalid Input', 'Please enter a valid weight number!');
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
                    
                    // Update BMI
                    const bmiValue = document.getElementById('bmiValue');
                    const bmiStatus = document.getElementById('bmiStatus');
                    if (bmiValue && result.new_bmi) bmiValue.innerText = result.new_bmi.score;
                    if (bmiStatus && result.new_bmi) {
                        bmiStatus.innerText = result.new_bmi.status;
                        bmiStatus.style.color = result.new_bmi.color;
                    }

                    // Refresh Chart & Reset Input
                    const activeRange = document.querySelector('.time-btn.active').getAttribute('data-time');
                    loadChartData(activeRange);
                    inputWeight.value = '';

                    // PAKAI ALERT KITA (SUKSES)
                    showAlert('success', 'Great Job!', `Weight updated to ${weightVal} kg.`);

                } else {
                    // PAKAI ALERT KITA (ERROR SERVER)
                    showAlert('error', 'Update Failed', result.message);
                }

            } catch (error) {
                console.error("Error Updating:", error);
                showAlert('error', 'System Error', 'Something went wrong. Try again.');
            } finally {
                btnUpdate.innerText = originalText;
                btnUpdate.disabled = false;
            }
        });
    }

    // --- LOAD AWAL ---
    loadChartData('1W'); 
});