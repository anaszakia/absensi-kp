<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalyst - Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .social-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .input-focus {
            transition: all 0.3s ease;
        }
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        .stagger-5 { animation-delay: 0.5s; }
        .stagger-6 { animation-delay: 0.6s; }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background-color: #ef4444; width: 33%; }
        .strength-medium { background-color: #f59e0b; width: 66%; }
        .strength-strong { background-color: #10b981; width: 100%; }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="glass-effect p-8 rounded-2xl shadow-2xl w-full max-w-md fade-in-up">
        <!-- Logo -->
        <div class="flex justify-center mb-6 fade-in-up stagger-1">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <div class="w-4 h-4 bg-white rounded-full"></div>
                </div>
                <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Catalyst</h1>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-6 fade-in-up stagger-2">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Buat Akun</h2>
            <p class="text-gray-600">Bergabunglah dengan ribuan pengguna lainnya</p>
        </div>

        <!-- Social Login Buttons -->
        <div class="space-y-3 mb-6 fade-in-up stagger-3">
            <button class="social-btn w-full flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium shadow-sm">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Daftar dengan Google
            </button>
            
            <button class="social-btn w-full flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-gray-700 bg-white hover:bg-gray-50 font-medium shadow-sm">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/>
                </svg>
                Daftar dengan Apple
            </button>
        </div>

        <!-- Divider -->
        <div class="relative mb-6 fade-in-up stagger-3">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500 font-medium">atau daftar dengan email</span>
            </div>
        </div>

        <!-- Registration Form -->
        <form class="space-y-5 fade-in-up stagger-4" id="registerForm">
            <!-- Full Name Input -->
            <div>
                <label for="fullName" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <div class="relative">
                    <input 
                        type="text" 
                        id="fullName" 
                        class="input-focus w-full px-4 py-3 pl-11 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm bg-white"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                </div>
                <span class="text-red-500 text-xs mt-1 hidden" id="nameError">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Nama lengkap harus diisi
                </span>
            </div>

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <input 
                        type="email" 
                        id="email" 
                        class="input-focus w-full px-4 py-3 pl-11 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm bg-white"
                        placeholder="nama@email.com"
                        required
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>
                <span class="text-red-500 text-xs mt-1 hidden" id="emailError">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Email tidak valid
                </span>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        class="input-focus w-full px-4 py-3 pl-11 pr-11 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm bg-white"
                        placeholder="Buat password yang kuat"
                        required
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <button 
                        type="button" 
                        id="togglePassword" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                    >
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <!-- Password Strength Indicator -->
                <div class="mt-2">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Kekuatan Password</span>
                        <span id="strengthText">Lemah</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1">
                        <div class="password-strength strength-weak" id="strengthBar"></div>
                    </div>
                </div>
                <span class="text-red-500 text-xs mt-1 hidden" id="passwordError">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Password minimal 8 karakter
                </span>
            </div>

            <!-- Confirm Password Input -->
            <div>
                <label for="confirmPassword" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        class="input-focus w-full px-4 py-3 pl-11 pr-11 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm bg-white"
                        placeholder="Ulangi password"
                        required
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-shield-alt text-gray-400"></i>
                    </div>
                    <button 
                        type="button" 
                        id="toggleConfirmPassword" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                    >
                        <i class="fas fa-eye" id="eyeConfirmIcon"></i>
                    </button>
                </div>
                <span class="text-red-500 text-xs mt-1 hidden" id="confirmPasswordError">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Password tidak cocok
                </span>
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start fade-in-up stagger-5">
                <input 
                    id="terms" 
                    name="terms" 
                    type="checkbox" 
                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded mt-1 transition-colors"
                    required
                >
                <label for="terms" class="ml-3 block text-sm text-gray-700">
                    Saya setuju dengan 
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium transition-colors">Syarat & Ketentuan</a> 
                    dan 
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium transition-colors">Kebijakan Privasi</a>
                </label>
            </div>

            <!-- Sign Up Button -->
            <button 
                type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105 fade-in-up stagger-6"
            >
                <i class="fas fa-user-plus mr-2"></i>
                Buat Akun
            </button>

            <!-- Sign In Link -->
            <div class="text-center mt-6 fade-in-up stagger-6">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="/login" class="text-purple-600 hover:text-purple-700 font-semibold transition-colors">
                        Masuk sekarang
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        // Password visibility toggle for password field
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });

        // Password visibility toggle for confirm password field
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const eyeConfirmIcon = document.getElementById('eyeConfirmIcon');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeConfirmIcon.classList.remove('fa-eye-slash');
                eyeConfirmIcon.classList.add('fa-eye');
            } else {
                eyeConfirmIcon.classList.remove('fa-eye');
                eyeConfirmIcon.classList.add('fa-eye-slash');
            }
        });

        // Password strength checker
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            strengthBar.className = 'password-strength';
            
            if (strength <= 1) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Lemah';
            } else if (strength <= 2) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Sedang';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Kuat';
            }
        });

        // Real-time validation
        function showError(elementId, show) {
            const errorEl = document.getElementById(elementId);
            if (show) {
                errorEl.classList.remove('hidden');
            } else {
                errorEl.classList.add('hidden');
            }
        }

        // Name validation
        document.getElementById('fullName').addEventListener('blur', function() {
            showError('nameError', this.value.trim().length < 2);
        });

        // Email validation
        document.getElementById('email').addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            showError('emailError', !emailRegex.test(this.value));
        });

        // Password validation
        passwordInput.addEventListener('blur', function() {
            showError('passwordError', this.value.length < 8);
        });

        // Confirm password validation
        confirmPasswordInput.addEventListener('blur', function() {
            showError('confirmPasswordError', this.value !== passwordInput.value);
        });

        // Form submission handler
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fullName = document.getElementById('fullName').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            let hasError = false;

            // Validation
            if (fullName.length < 2) {
                showError('nameError', true);
                hasError = true;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('emailError', true);
                hasError = true;
            }

            if (password.length < 8) {
                showError('passwordError', true);
                hasError = true;
            }

            if (password !== confirmPassword) {
                showError('confirmPasswordError', true);
                hasError = true;
            }

            if (!terms) {
                alert('Harap setujui Syarat & Ketentuan dan Kebijakan Privasi');
                hasError = true;
            }

            if (hasError) return;

            // Success animation
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            button.disabled = true;

            // Simulate API call
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Berhasil!';
                
                setTimeout(() => {
                    alert('Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
                    // Here you would redirect to login page or dashboard
                    // window.location.href = '/login';
                }, 1000);
            }, 2000);
        });

        // Social registration handlers
        document.querySelector('.social-btn:first-child').addEventListener('click', function() {
            console.log('Google registration clicked');
            // window.location.href = '/auth/google';
        });

        document.querySelector('.social-btn:last-child').addEventListener('click', function() {
            console.log('Apple registration clicked');
            // window.location.href = '/auth/apple';
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.input-focus');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>