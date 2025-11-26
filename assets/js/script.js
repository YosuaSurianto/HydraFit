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
   BAGIAN 2: LOGIKA INTERAKSI WEB (Navigation)
   ========================================= */

const btnLogin = document.getElementById('btnLogin');
const btnSignup = document.getElementById('btnSignup');
const btnStart = document.getElementById('btnStart');

if(btnLogin) {
    btnLogin.addEventListener('click', () => { window.location.href = 'login.html'; });
}
if(btnSignup) {
    btnSignup.addEventListener('click', () => { window.location.href = 'register.html'; });
}
if(btnStart) {
    btnStart.addEventListener('click', () => { window.location.href = 'register.html'; });
}

// TOGGLE PASSWORD
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

/* =========================================
   BAGIAN 3: SISTEM REGISTER & LOGIN (LOCALSTORAGE)
   ========================================= */

// --- A. REGISTER SYSTEM ---
const registerForm = document.getElementById('registerForm');

if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
        e.preventDefault(); 

        const email = document.getElementById('regEmail').value;
        const password = document.getElementById('passwordInput').value;

        let users = JSON.parse(localStorage.getItem('users')) || [];

        // Cek duplikat
        const isEmailExist = users.find(user => user.email === email);
        if (isEmailExist) {
            alert("Email ini sudah terdaftar! Silakan login.");
            return;
        }

        // Masukkan user baru dengan STATUS: BELUM SELESAI
        users.push({
            email: email,
            password: password,
            isSetupDone: false, // <--- INI KUNCINYA
            
            // Data lain masih kosong
            name: "", 
            username: "",
            birthdate: "",
            gender: "",
            blood: "",
            weight: "",
            height: ""
        });

        localStorage.setItem('users', JSON.stringify(users));
        
        // Simpan email sementara
        localStorage.setItem('registeringEmail', email);

        // Redirect ke STEP 2
        window.location.href = 'create-profile.html';
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
            // --- LOGIKA SATPAM (DIPERKETAT) ---
            // Jika isSetupDone TIDAK true (berarti bisa false, atau tidak ada/undefined), maka TOLAK.
            if (validUser.isSetupDone !== true) {
                alert("Pendaftaran Anda belum selesai! Harap lengkapi profil Anda.");
                
                // Simpan emailnya biar sistem tau siapa yg mau dilanjutin
                localStorage.setItem('registeringEmail', validUser.email);
                
                // Paksa pindah ke Step 2
                window.location.href = 'create-profile.html';
                return; // Stop di sini, jangan lanjut login
            }

            // Kalau sudah lulus, baru boleh masuk
            localStorage.setItem('currentUser', JSON.stringify(validUser));
            
            const displayName = validUser.name ? validUser.name : "User";
            alert("Login Berhasil! Selamat datang, " + displayName);
            
            window.location.href = 'dashboard.html'; 
        } else {
            alert("Email atau Password salah!");
        }
    });
}