/* =========================================
   BAGIAN 1: ANIMASI BACKGROUND (CANVAS)
   ========================================= */
const canvas = document.getElementById('particles-canvas');

if (canvas) {
    const ctx = canvas.getContext('2d');
    let particlesArray;

    function setCanvasSize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    setCanvasSize();

    class Particle {
        constructor(x, y, directionX, directionY, size, color) {
            this.x = x;
            this.y = y;
            this.directionX = directionX;
            this.directionY = directionY;
            this.size = size;
            this.color = color;
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2, false);
            ctx.fillStyle = '#94a3b8';
            ctx.fill();
        }
        update() {
            if (this.x > canvas.width || this.x < 0) this.directionX = -this.directionX;
            if (this.y > canvas.height || this.y < 0) this.directionY = -this.directionY;
            this.x += this.directionX;
            this.y += this.directionY;
            this.draw();
        }
    }

    function init() {
        particlesArray = [];
        let numberOfParticles = (canvas.width * canvas.height) / 9000;
        for (let i = 0; i < numberOfParticles; i++) {
            let size = (Math.random() * 2) + 1;
            let x = (Math.random() * ((innerWidth - size * 2) - (size * 2)) + size * 2);
            let y = (Math.random() * ((innerHeight - size * 2) - (size * 2)) + size * 2);
            let directionX = (Math.random() * 1) - 0.5;
            let directionY = (Math.random() * 1) - 0.5;
            particlesArray.push(new Particle(x, y, directionX, directionY, size, '#94a3b8'));
        }
    }

    function connect() {
        for (let a = 0; a < particlesArray.length; a++) {
            for (let b = a; b < particlesArray.length; b++) {
                let distance = ((particlesArray[a].x - particlesArray[b].x) * (particlesArray[a].x - particlesArray[b].x))
                    + ((particlesArray[a].y - particlesArray[b].y) * (particlesArray[a].y - particlesArray[b].y));
                if (distance < (canvas.width/7) * (canvas.height/7)) {
                    let opacityValue = 1 - (distance/20000);
                    ctx.strokeStyle = 'rgba(148, 163, 184,' + opacityValue + ')';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }

    function animate() {
        requestAnimationFrame(animate);
        ctx.clearRect(0, 0, innerWidth, innerHeight);
        for (let i = 0; i < particlesArray.length; i++) {
            particlesArray[i].update();
        }
        connect();
    }

    window.addEventListener('resize', () => {
        setCanvasSize();
        init();
    });

    init();
    animate();
}

/* =========================================
   BAGIAN 2: GLOBAL MODAL SYSTEM (POP-UP)
   ========================================= */

// Fungsi global untuk menampilkan modal
// Bisa dipanggil dari file JS manapun setelah script.js diload
function showModal(type, title, message, onConfirm = null) {
    // 1. Cek apakah HTML modal sudah ada? Kalau belum, inject.
    if (!document.getElementById('globalModal')) {
        const modalHTML = `
            <div id="globalModal" class="modal-overlay">
                <div class="modal-box">
                    <div id="modalIcon" class="modal-icon"></div>
                    <h3 id="modalTitle">Title</h3>
                    <p id="modalMessage">Message</p>
                    <div class="modal-actions">
                        <button id="modalBtnCancel" class="btn-modal secondary" style="display:none">Cancel</button>
                        <button id="modalBtnConfirm" class="btn-modal primary">OK</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    const modal = document.getElementById('globalModal');
    const icon = document.getElementById('modalIcon');
    const titleEl = document.getElementById('modalTitle');
    const msgEl = document.getElementById('modalMessage');
    const btnConfirm = document.getElementById('modalBtnConfirm');
    const btnCancel = document.getElementById('modalBtnCancel');

    // Set Konten
    titleEl.innerText = title;
    msgEl.innerText = message;

    // Set Icon & Warna
    icon.className = 'modal-icon ' + type; 
    let iconSVG = '';
    if (type === 'success') iconSVG = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
    if (type === 'error') iconSVG = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
    if (type === 'info') iconSVG = `<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
    icon.innerHTML = iconSVG;

    // Reset tombol (kloning node untuk hapus event listener lama)
    const newBtnConfirm = btnConfirm.cloneNode(true);
    btnConfirm.parentNode.replaceChild(newBtnConfirm, btnConfirm);
    
    const newBtnCancel = btnCancel.cloneNode(true);
    btnCancel.parentNode.replaceChild(newBtnCancel, btnCancel);

    // Atur Aksi Tombol
    if (onConfirm) {
        newBtnCancel.style.display = 'block';
        newBtnCancel.innerText = "Cancel";
        newBtnConfirm.innerText = "Yes";
        
        newBtnConfirm.addEventListener('click', () => {
            modal.classList.remove('active');
            onConfirm();
        });
        
        newBtnCancel.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    } else {
        newBtnCancel.style.display = 'none';
        newBtnConfirm.innerText = "OK";
        
        newBtnConfirm.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    // Tampilkan Modal
    setTimeout(() => {
        modal.classList.add('active');
    }, 10);
}


/* =========================================
   BAGIAN 3: LOGIKA NAVIGASI & AUTH
   ========================================= */

const btnLogin = document.getElementById('btnLogin');
const btnSignup = document.getElementById('btnSignup');
const btnStart = document.getElementById('btnStart');

// Navigation
if(btnLogin) {
    btnLogin.addEventListener('click', () => { window.location.href = 'login.html'; });
}
if(btnSignup) {
    btnSignup.addEventListener('click', () => { window.location.href = 'register.html'; });
}
if(btnStart) {
    btnStart.addEventListener('click', () => { window.location.href = 'register.html'; });
}

// Toggle Password (Mata)
const togglePassword = document.getElementById('togglePassword');
const passwordInputReg = document.getElementById('passwordInput');
const passwordInputLog = document.getElementById('loginPassword');
const activePasswordInput = passwordInputReg || passwordInputLog;

if (togglePassword && activePasswordInput) {
    togglePassword.addEventListener('click', function () {
        const type = activePasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        activePasswordInput.setAttribute('type', type);
        
        if (type === 'text') {
            this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        } else {
            this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        }
    });
}


// --- A. REGISTER SYSTEM ---
const registerForm = document.getElementById('registerForm');

if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault(); 

        const email = document.getElementById('regEmail').value;
        const password = document.getElementById('passwordInput').value;

        let users = JSON.parse(localStorage.getItem('users')) || [];

        const isEmailExist = users.find(user => user.email === email);
        if (isEmailExist) {
            showModal('error', 'Gagal Daftar', 'Email ini sudah terdaftar! Silakan login.');
            return;
        }

        users.push({
            email: email,
            password: password,
            isSetupDone: false, // Menandakan profil belum lengkap
            
            // Placeholder data
            name: "", 
            username: "",
            weightHistory: [], // Array untuk menyimpan riwayat berat badan
            
            birthdate: "", gender: "", blood: "", weight: "", height: ""
        });

        localStorage.setItem('users', JSON.stringify(users));
        localStorage.setItem('registeringEmail', email);

        // Pakai Modal Sukses, lalu redirect
        showModal('success', 'Akun Terdaftar!', 'Silakan lengkapi profil Anda.', () => {
            window.location.href = 'create-profile.html';
        });
    });
}

// --- B. LOGIN SYSTEM ---
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        let users = JSON.parse(localStorage.getItem('users')) || [];
        const validUser = users.find(user => user.email === email && user.password === password);

        if (validUser) {
            // Cek Status Setup
            if (validUser.isSetupDone !== true) {
                showModal('info', 'Profil Belum Lengkap', 'Harap selesaikan pendaftaran profil Anda.', () => {
                    localStorage.setItem('registeringEmail', validUser.email);
                    window.location.href = 'create-profile.html';
                });
                return;
            }

            // Login Sukses
            localStorage.setItem('currentUser', JSON.stringify(validUser));
            
            const displayName = validUser.name ? validUser.name : "User";
            
            showModal('success', 'Login Berhasil!', `Selamat datang kembali, ${displayName}`, () => {
                window.location.href = 'dashboard.html';
            });

        } else {
            showModal('error', 'Login Gagal', 'Email atau Password salah!');
        }
    });
}