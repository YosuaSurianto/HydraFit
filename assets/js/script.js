/* =========================================
   BAGIAN 1: ANIMASI BACKGROUND (CANVAS)
   ========================================= */
const canvas = document.getElementById('particles-canvas');

// Pengecekan: Hanya jalankan animasi jika elemen canvas ada di halaman tersebut
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
            ctx.fillStyle = '#94a3b8'; // Warna abu-abu (Slate-400)
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
        // Rumus kepadatan: Semakin besar pembagi (9000), semakin sedikit titiknya
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
                
                // Jika jarak dekat, gambar garis
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
   BAGIAN 2: LOGIKA INTERAKSI WEB (Navigation)
   ========================================= */

const btnLogin = document.getElementById('btnLogin');
const btnSignup = document.getElementById('btnSignup');
const btnStart = document.getElementById('btnStart');

// 1. PINDAH HALAMAN
if(btnLogin) {
    btnLogin.addEventListener('click', () => { window.location.href = 'login.html'; });
}
if(btnSignup) {
    btnSignup.addEventListener('click', () => { window.location.href = 'register.html'; });
}
if(btnStart) {
    btnStart.addEventListener('click', () => { window.location.href = 'register.html'; });
}

// 2. FITUR SHOW/HIDE PASSWORD
// Kita cari icon mata berdasarkan class atau ID
const togglePassword = document.getElementById('togglePassword');

// Kita cari input password. Karena ID-nya beda di Login vs Register, kita cari salah satu.
const passwordInputReg = document.getElementById('passwordInput'); // ID di Register
const passwordInputLog = document.getElementById('loginPassword'); // ID di Login

// Tentukan mana yang aktif saat ini (Register atau Login)
const activePasswordInput = passwordInputReg || passwordInputLog;

if (togglePassword && activePasswordInput) {
    togglePassword.addEventListener('click', function () {
        // Cek tipe: password atau text?
        const type = activePasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        activePasswordInput.setAttribute('type', type);
        
        // Ganti Icon SVG
        if (type === 'text') {
            // Icon Mata Terbuka (Show)
            this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        } else {
            // Icon Mata Dicoret (Hide) - Default
            this.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        }
    });
}

/* =========================================
   BAGIAN 3: SISTEM REGISTER & LOGIN (LOCALSTORAGE)
   ========================================= */

// --- A. REGISTER SYSTEM ---
const registerForm = document.getElementById('registerForm');

if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Stop reload halaman

        // 1. Ambil nilai dari input
        const name = document.getElementById('regName').value;
        const email = document.getElementById('regEmail').value;
        // Ambil password dari ID register
        const password = document.getElementById('passwordInput').value;

        // 2. Ambil data user lama dari LocalStorage (kalau ada)
        let users = JSON.parse(localStorage.getItem('users')) || [];

        // 3. Cek apakah email sudah terdaftar?
        const isEmailExist = users.find(user => user.email === email);
        if (isEmailExist) {
            alert("Email ini sudah terdaftar! Silakan login.");
            return;
        }

        // 4. Masukkan user baru ke Array
        users.push({
            name: name,
            email: email,
            password: password
        });

        // 5. Simpan balik ke LocalStorage
        localStorage.setItem('users', JSON.stringify(users));

        // 6. Sukses
        alert("Registrasi Berhasil! Silakan Login.");
        window.location.href = 'login.html';
    });
}

// --- B. LOGIN SYSTEM ---
const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // 1. Ambil input
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // 2. Ambil database user dari LocalStorage
        let users = JSON.parse(localStorage.getItem('users')) || [];

        // 3. Cari user yang email DAN passwordnya cocok
        const validUser = users.find(user => user.email === email && user.password === password);

        if (validUser) {
            // 4. Jika ketemu, simpan sesi login
            localStorage.setItem('currentUser', JSON.stringify(validUser));

            alert("Login Berhasil! Selamat datang, " + validUser.name);
            
            // Redirect ke Dashboard (Pastikan nanti file dashboard.html dibuat)
            window.location.href = 'dashboard.html'; 
        } else {
            // 5. Jika tidak ketemu
            alert("Email atau Password salah!");
        }
    });
}