/* =========================================
   LOGIKA DASHBOARD (FULL FEATURES)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. SETUP DATA & ELEMEN ---
    let currentUser = JSON.parse(localStorage.getItem('currentUser'));
    
    // Elemen UI Dasar
    const welcomeMsg = document.getElementById('welcomeMsg');
    const userAvatar = document.getElementById('userAvatar');
    const displayWeight = document.getElementById('displayWeight');
    const bmiValueElement = document.getElementById('bmiValue');
    const bmiStatusElement = document.getElementById('bmiStatus');
    const newWeightInput = document.getElementById('newWeight');
    const btnUpdateWeight = document.querySelector('.btn-update'); 

    // Elemen Custom Alert Modal
    const customAlert = document.getElementById('customAlert');
    const modalMessage = document.getElementById('modalMessage');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Validasi Login
    if (!currentUser) {
        alert("Anda belum login!"); 
        window.location.href = 'login.html';
        return;
    }

    // --- FUNGSI CUSTOM ALERT (MODAL) ---
    function showCustomAlert(message) {
        if (customAlert && modalMessage) {
            modalMessage.innerText = message;
            customAlert.classList.remove('hidden'); // Munculkan modal
        } else {
            // Fallback jika HTML modal tidak ditemukan
            alert(message); 
        }
    }

    // Event Listener Tutup Modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            customAlert.classList.add('hidden'); // Sembunyikan modal
            // Reload halaman setelah user klik OK (biar grafik update)
            location.reload(); 
        });
    }

    // --- UPDATE UI DASHBOARD ---
    function updateDashboardUI() {
        // A. Update Nama
        if (welcomeMsg) {
            const firstName = currentUser.name.split(' ')[0];
            welcomeMsg.innerText = `Hello, ${firstName}! 👋`;
        }

        // B. Update Avatar
        if (userAvatar) {
            userAvatar.innerText = currentUser.name.charAt(0).toUpperCase();
        }

        // C. Update Berat Badan Utama
        if (displayWeight) {
            displayWeight.innerText = currentUser.weight ? `${currentUser.weight} kg` : "-- kg";
        }

        // D. Hitung BMI
        if (bmiValueElement && bmiStatusElement) {
            if (currentUser.weight && currentUser.height) {
                const weight = parseFloat(currentUser.weight);
                const heightCm = parseFloat(currentUser.height);
                
                if (weight > 0 && heightCm > 0) {
                    const heightM = heightCm / 100;
                    const bmi = weight / (heightM * heightM);
                    
                    bmiValueElement.innerText = bmi.toFixed(1);

                    let status = "Unknown";
                    let color = "#334155";

                    if (bmi < 18.5) { status = "Underweight"; color = "#3b82f6"; }
                    else if (bmi >= 18.5 && bmi < 24.9) { status = "Normal"; color = "#22c55e"; }
                    else if (bmi >= 25 && bmi < 29.9) { status = "Overweight"; color = "#f97316"; }
                    else { status = "Obesity"; color = "#ef4444"; }

                    bmiStatusElement.innerText = status;
                    bmiStatusElement.style.color = color;
                }
            } else {
                bmiValueElement.innerText = "--.--";
                bmiStatusElement.innerText = "No Data";
            }
        }
    }

    // Jalankan update tampilan saat load
    updateDashboardUI();


    // --- 2. UPDATE BERAT BADAN (DENGAN CUSTOM ALERT) ---
    if (btnUpdateWeight && newWeightInput) {
        btnUpdateWeight.addEventListener('click', () => {
            const newWeight = parseFloat(newWeightInput.value);

            // Validasi Input
            if (newWeight >= 30 && newWeight <= 300) { 
                // A. Update Data Utama
                currentUser.weight = newWeight;

                // B. Update History (Buat array baru jika belum ada)
                if (!currentUser.weightHistory) {
                    currentUser.weightHistory = []; 
                }
                
                const todayDate = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                
                currentUser.weightHistory.push({
                    date: todayDate,
                    weight: newWeight
                });

                // C. Simpan ke LocalStorage (Sesi Ini)
                localStorage.setItem('currentUser', JSON.stringify(currentUser));

                // D. Simpan ke Database Users (Semua User)
                let users = JSON.parse(localStorage.getItem('users')) || [];
                const userIndex = users.findIndex(u => u.email === currentUser.email);
                if (userIndex !== -1) {
                    users[userIndex].weight = newWeight;
                    users[userIndex].weightHistory = currentUser.weightHistory;
                    localStorage.setItem('users', JSON.stringify(users));
                }

                // E. TAMPILKAN CUSTOM ALERT
                showCustomAlert(`Berat badan berhasil diupdate menjadi ${newWeight} kg!`);
                
            } else {
                alert("Mohon masukkan berat badan yang valid (30kg - 300kg).");
            }
        });
    }


    // --- 3. SIDEBAR & LOGOUT ---
    const toggleSidebarBtn = document.getElementById('toggleSidebar');
    const sidebar = document.getElementById('sidebar');
    const btnLogout = document.getElementById('btnLogout');
    
    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }

    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            if (confirm("Yakin ingin keluar?")) {
                localStorage.removeItem('currentUser');
                window.location.href = 'index.html';
            }
        });
    }


    // --- 4. GRAFIK (REALTIME DATA) ---
    const ctx = document.getElementById('weightChart');
    
    // Cek apakah ada data history untuk digambar
    if (ctx && currentUser.weightHistory) {
        const labels = currentUser.weightHistory.map(item => item.date);
        const dataPoints = currentUser.weightHistory.map(item => item.weight);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: dataPoints,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 5,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: false, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    } else if (ctx && currentUser.weight) {
        // Fallback: Jika belum ada history, tampilkan 1 titik saja (Data Hari Ini)
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Hari Ini'],
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: [parseFloat(currentUser.weight)],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: false } }
            }
        });
    }

});