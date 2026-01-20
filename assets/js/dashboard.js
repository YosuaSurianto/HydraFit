/* =========================================
   DASHBOARD LOGIC (AJAX & CHART.JS)
   ========================================= */

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. SETUP ELEMEN ---
    const ctx = document.getElementById('weightChart');
    const btnUpdate = document.getElementById('btnUpdateWeight');
    const inputWeight = document.getElementById('newWeight');
    const displayWeight = document.getElementById('displayWeight');
    
    // Timeframe Buttons
    const timeBtns = document.querySelectorAll('.time-btn');

    // Modal Elements
    const customAlert = document.getElementById('customAlert');
    const modalMessage = document.getElementById('modalMessage');
    const closeModalBtn = document.getElementById('closeModalBtn');

    let myChart; // Variabel global untuk Chart

    // --- 2. FUNGSI UNTUK MENGGAMBAR CHART ---
    function renderChart(labels, dataPoints) {
        // Hancurkan chart lama jika ada (biar gak numpuk pas ganti timeframe)
        if (myChart) {
            myChart.destroy();
        }

        // Buat Chart Baru
        myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Berat Badan (kg)',
                    data: dataPoints,
                    borderColor: '#2563eb', // Warna Biru Utama
                    backgroundColor: 'rgba(37, 99, 235, 0.1)', // Arsir Bawah
                    borderWidth: 2,
                    tension: 0.3, // Kelengkungan garis (0 = kaku, 0.4 = mulus)
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }, // Sembunyikan legenda
                scales: {
                    y: { 
                        beginAtZero: false, 
                        grid: { color: '#f1f5f9' } 
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { maxTicksLimit: 8 } // Batasi label bawah biar gak penuh
                    }
                }
            }
        });
    }

    // --- 3. FUNGSI FETCH DATA DARI API (GET) ---
    async function loadChartData(range = '1W') {
        try {
            // Panggil API PHP kita
            const response = await fetch(`api_weight.php?range=${range}`);
            const result = await response.json();

            if (result.status === 'success') {
                // Pisahkan data untuk sumbu X (Label) dan Y (Berat)
                const labels = result.data.map(item => item.label);
                const values = result.data.map(item => item.value);

                // Gambar grafiknya
                renderChart(labels, values);
            } else {
                console.error("Gagal memuat data:", result.message);
            }
        } catch (error) {
            console.error("Error Fetching Data:", error);
        }
    }

    // --- 4. LOGIKA TOMBOL TIMEFRAME (1W, 1M, ALL) ---
    timeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Hapus kelas active dari semua tombol
            timeBtns.forEach(b => b.classList.remove('active'));
            // Tambah kelas active ke tombol yang diklik
            btn.classList.add('active');

            // Ambil data range (1W/1M/ALL) dan muat ulang chart
            const range = btn.getAttribute('data-time');
            loadChartData(range);
        });
    });

    // --- 5. LOGIKA UPDATE BERAT BADAN (AJAX POST) ---
    if (btnUpdate) {
        btnUpdate.addEventListener('click', async () => {
            const weightVal = parseFloat(inputWeight.value);

            if (!weightVal || weightVal <= 0) {
                alert("Masukkan berat badan yang valid!");
                return;
            }

            // Ubah tombol jadi 'Loading...'
            const originalText = btnUpdate.innerText;
            btnUpdate.innerText = "Saving...";
            btnUpdate.disabled = true;

            try {
                // Kirim data ke API tanpa reload halaman
                const response = await fetch('api_weight.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ weight: weightVal })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // 1. Munculkan Modal Sukses
                    if(customAlert) {
                        modalMessage.innerText = `Berat badan berhasil diupdate menjadi ${weightVal} kg!`;
                        customAlert.classList.remove('hidden');
                    }

                    // 2. Update Angka Besar di Dashboard (Tanpa Reload)
                    if(displayWeight) {
                        displayWeight.innerText = `${weightVal} kg`;
                    }

                    // --- 3. UPDATE BMI (BAGIAN UTAMA YANG KITA PERBAIKI) ---
                    const bmiValue = document.getElementById('bmiValue');
                    const bmiStatus = document.getElementById('bmiStatus');

                    if (bmiValue && result.new_bmi) {
                        bmiValue.innerText = result.new_bmi.score;
                    }
                    if (bmiStatus && result.new_bmi) {
                        bmiStatus.innerText = result.new_bmi.status;
                        bmiStatus.style.color = result.new_bmi.color;
                    }

                    // 4. Refresh Grafik (Ambil data terbaru)
                    const activeRange = document.querySelector('.time-btn.active').getAttribute('data-time');
                    loadChartData(activeRange);

                    // 5. Reset Input
                    inputWeight.value = '';

                } else {
                    alert("Gagal update: " + result.message);
                }

            } catch (error) {
                console.error("Error Updating:", error);
                alert("Terjadi kesalahan sistem.");
            } finally {
                // Kembalikan tombol seperti semula
                btnUpdate.innerText = originalText;
                btnUpdate.disabled = false;
            }
        });
    }

    // --- 6. LOGIKA TUTUP MODAL ---
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            customAlert.classList.add('hidden');
        });
    }

    // === JALANKAN PERTAMA KALI ===
    // Load data default (1 Minggu terakhir)
    loadChartData('1W'); 
});