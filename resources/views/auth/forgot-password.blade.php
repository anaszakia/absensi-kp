<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalyst - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-sm w-full max-w-sm">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-black rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 bg-white rounded-full"></div>
                </div>
                <h1 class="text-xl font-semibold text-gray-900">Logo</h1>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-2xl font-semibold text-gray-900 text-center mb-4">Lupa password anda?</h2>
        
        <!-- Description -->
        <p class="text-sm text-gray-600 text-center mb-8">
            Jangan khawatir, reset password anda disini!
        </p>

        <!-- Forgot Password Form -->
        <form class="space-y-6" id="forgotPasswordForm">
            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    class="w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    placeholder="Masukkan e-mail yang terdaftar"
                >
            </div>

            <!-- Reset Password Button -->
            <button 
                type="submit" 
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out"
            >
                Reset Password
            </button>

            <!-- Back to Login Link -->
            <div class="text-sm text-center text-gray-600 mt-6">
                <a href="/login" class="text-blue-600 hover:text-blue-500 flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Masuk kembali
                </a>
            </div>
        </form>

        <!-- Success Message (hidden by default) -->
        <div id="successMessage" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <div>
                    <p class="text-sm text-green-800 font-medium">Cek E-mail kamu </p>
                    <p class="text-sm text-green-700 mt-1">
                        Kami kirimkan link reset password melalui e-mail anda<span id="emailSent"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission handler
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            
            // Validation
            if (!email) {
                alert('Please enter your email address');
                return;
            }
            
            // Email validation regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }
            
            // Simulate sending reset email
            console.log('Password reset requested for:', email);
            
            // Show success message
            document.getElementById('emailSent').textContent = email;
            document.getElementById('successMessage').classList.remove('hidden');
            
            // Hide the form
            document.querySelector('form').style.display = 'none';
            
            // Update title and description
            document.querySelector('h2').textContent = 'Check your email';
            document.querySelector('p').textContent = 'We sent a password reset link to ' + email;
        });

        // Resend email function
        function resendEmail() {
            const email = document.getElementById('email').value;
            console.log('Resending password reset email to:', email);
            alert('Password reset email sent again!');
        }

        // Add resend option after 30 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            if (!successMessage.classList.contains('hidden')) {
                const resendDiv = document.createElement('div');
                resendDiv.className = 'text-sm text-center text-gray-600 mt-4';
                resendDiv.innerHTML = `
                    Didn't receive the email? 
                    <button onclick="resendEmail()" class="text-blue-600 hover:text-blue-500 underline">
                        Click to resend
                    </button>
                `;
                successMessage.appendChild(resendDiv);
            }
        }, 30000);
    </script>
</body>
</html>