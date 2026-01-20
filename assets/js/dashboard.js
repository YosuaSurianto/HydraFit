/* =========================================
   DASHBOARD LOGIC (WITH SWEETALERT2)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. SETUP ELEMEN ---
    const ctx = document.getElementById('weightChart');
    const btnUpdate = document.getElementById('btnUpdateWeight');
    const inputWeight = document.getElementById('newWeight');
    const displayWeight = document.getElementById('displayWeight');
    const timeBtns = document.querySelectorAll('.time-btn');
    const navLogoutBtn = document.getElementById('navLogoutBtn'); // Tombol Logout di Sidebar

    let myChart; 

    // --- 2. LOGIKA LOGOUT (SWEETALERT2) ---
    if (navLogoutBtn) {
        navLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            // Panggil SweetAlert2 tipe 'confirm'
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your session.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Merah
                cancelButtonColor: '#64748b',  // Abu-abu
                confirmButtonText: 'Yes, Log Me Out'
            }).then((result) => {
                // Jika user klik tombol "Yes"
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        });
    }

    // --- 3. RENDER CHART (Sama seperti sebelumnya) ---
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

    // --- 4. FETCH DATA (GET) ---
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

    // --- 5. TIMEFRAME BUTTONS ---
    timeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            timeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            loadChartData(btn.getAttribute('data-time'));
        });
    });

    // --- 6. UPDATE BERAT (POST) ---
    if (btnUpdate) {
        btnUpdate.addEventListener('click', async () => {
            const weightVal = parseFloat(inputWeight.value);

            // VALIDASI INPUT (SWEETALERT ERROR)
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

                    // SUKSES UPDATE (SWEETALERT SUCCESS)
                    Swal.fire({
                        icon: 'success',
                        title: 'Great Job!',
                        text: `Weight updated to ${weightVal} kg.`,
                        timer: 2000, // Otomatis tutup dalam 2 detik
                        showConfirmButton: false
                    });

                } else {
                    // ERROR DARI SERVER
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

    // --- LOAD AWAL ---
    loadChartData('1W'); 
});