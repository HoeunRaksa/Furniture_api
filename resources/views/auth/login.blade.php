<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Furniture Admin</title>

    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e293b 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .login-container {
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3),
                        0 0 0 1px rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4),
                        0 0 0 1px rgba(255, 255, 255, 0.15);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeIn 0.6s ease 0.2s forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            to { opacity: 1; }
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: rotate(10deg) scale(1.05);
        }

        .logo-icon i {
            font-size: 32px;
            color: white;
        }

        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 20px;
            animation: slideIn 0.5s ease forwards;
            opacity: 0;
            transform: translateX(-20px);
        }

        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }
        .form-group:nth-child(3) { animation-delay: 0.5s; }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-input:focus + i,
        .input-wrapper:focus-within i {
            color: #667eea;
        }

        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease 0.6s forwards;
            opacity: 0;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
            cursor: pointer;
        }

        .remember-checkbox label {
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.5s ease 0.7s forwards;
            opacity: 0;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message.show {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Floating Particles -->
    <div class="particles">
        @for ($i = 0; $i < 20; $i++)
            @php
                $left = rand(0, 100);
                $delay = $i * 0.5;
                $duration = rand(15, 25);
            @endphp
            <div class="particle" data-left="{{ $left }}" data-delay="{{ $delay }}" data-duration="{{ $duration }}"></div>
        @endfor
    </div>

    <script>
        document.querySelectorAll('.particle').forEach(p => {
            p.style.left = p.dataset.left + '%';
            p.style.animationDelay = p.dataset.delay + 's';
            p.style.animationDuration = p.dataset.duration + 's';
        });
    </script>

    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <h1 class="login-title">Furniture Admin</h1>
                <p class="login-subtitle">Sign in to your account</p>
            </div>

            <!-- Error Message -->
            @if ($errors->any())
            <div class="error-message show">
                <i class="bi bi-exclamation-circle me-2"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="username" id="username" class="form-input" 
                               placeholder="Enter your username" value="{{ old('username') }}" required />
                        <i class="bi bi-person"></i>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-input" 
                               placeholder="Enter your password" required autocomplete="current-password" />
                        <i class="bi bi-lock"></i>
                    </div>
                </div>

                <!-- Remember -->
                <div class="remember-row">
                    <div class="remember-checkbox">
                        <input type="checkbox" name="remember" id="remember" />
                        <label for="remember">Remember me</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="login-btn" id="submitBtn">
                    <span id="btnText">Sign In</span>
                    <i class="bi bi-arrow-right ms-2" id="btnIcon"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- MDB JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

    <script>
        $(document).ready(function() {
            // Form submit animation
            $('#loginForm').on('submit', function() {
                const $btn = $('#submitBtn');
                const $btnText = $('#btnText');
                const $btnIcon = $('#btnIcon');

                $btn.prop('disabled', true);
                $btnText.text('Signing in...');
                $btnIcon.removeClass('bi-arrow-right').addClass('bi-arrow-repeat').css('animation', 'spin 1s linear infinite');
            });
        });

        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
