<?php
// login.php
session_start();
require_once 'config/config.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($email) && !empty($senha)) {
        if ($email === AUTH_EMAIL && $senha === AUTH_PASSWORD) {
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $error = "E-mail ou senha incorretos.";
        }
    } else {
        $error = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Premium - TechManager Pro</title>
    
    <!-- Google Fonts & FontAwesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: #111827;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border-color: #374151;
            --primary: #06b6d4;
            --primary-hover: #0891b2;
            --btn-grad: linear-gradient(90deg, #38bdf8, #0ea5e9);
            --btn-grad-hover: linear-gradient(90deg, #0ea5e9, #0284c7);
            --danger: #ef4444;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Split Layout */
        .login-container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Left Side: Tech Graphic */
        .login-visual {
            flex: 1;
            background-image: url('public/tech_bg.png');
            background-size: cover;
            background-position: center;
            position: relative;
            display: block;
        }
        
        /* Subtle dark blue gradient overlay to blend into the form side */
        .login-visual::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(11, 15, 25, 0.1), rgba(11, 15, 25, 0.8) 95%);
            pointer-events: none;
        }

        /* Right Side: Form Panel */
        .login-form-panel {
            width: 50%;
            max-width: 600px;
            min-width: 450px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 50px 80px;
            background-color: var(--bg-color);
            position: relative;
            z-index: 10;
        }

        /* Form Centered Container */
        .form-centered-container {
            margin-top: auto;
            margin-bottom: auto;
            width: 100%;
            max-width: 400px;
            align-self: center;
        }

        /* Logo Area */
        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 45px;
        }
        .logo-text {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }
        .logo-svg path {
            transition: all 0.3s ease;
        }

        /* Headers */
        h1 {
            font-size: 2.1rem;
            font-weight: 400;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 40px;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
            width: 100%;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            padding-right: 48px; /* Extra space for password eye */
            background-color: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 8px;
            color: white;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control::placeholder {
            color: #475569;
        }
        .form-control:focus {
            outline: none;
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
            background-color: rgba(30, 41, 59, 0.6);
        }

        /* Eye Icon for Show/Hide Password */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            cursor: pointer;
            font-size: 1.1rem;
            background: none;
            border: none;
            outline: none;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover {
            color: var(--text-main);
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: var(--btn-grad);
            color: #0f172a;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 8px 30px rgba(14, 165, 233, 0.35);
        }
        .btn-login:hover {
            background: var(--btn-grad-hover);
            transform: translateY(-1px);
            box-shadow: 0 10px 35px rgba(14, 165, 233, 0.45);
        }
        .btn-login:active {
            transform: translateY(1px);
        }

        /* Forgot password link */
        .forgot-link-container {
            text-align: center;
            margin-top: 25px;
        }
        .forgot-link {
            color: #38bdf8;
            font-size: 0.85rem;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .forgot-link:hover {
            text-decoration: underline;
            opacity: 0.9;
        }

        /* Footer Copyright */
        .footer-copyright {
            font-size: 0.75rem;
            color: #475569;
            text-align: center;
            margin-top: auto;
        }

        /* Notification Toast styles */
        .toast {
            position: fixed; 
            top: 24px; 
            right: 24px; 
            padding: 16px 24px; 
            border-radius: 10px;
            background: #1f2937; 
            color: var(--text-main); 
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--danger); 
            display: flex; 
            align-items: center; 
            gap: 12px;
            z-index: 10000; 
            transform: translateX(130%); 
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-weight: 600; 
            font-size: 0.9rem; 
            border: 1px solid rgba(255,255,255,0.05);
        }
        .toast.active { transform: translateX(0); }
        .toast i { font-size: 1.3rem; color: var(--danger); }

        /* Responsive Layout */
        @media (max-width: 900px) {
            .login-visual {
                display: none;
            }
            .login-form-panel {
                width: 100%;
                max-width: 100%;
                min-width: 100%;
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        
        <!-- Left Side: Circuits Visual -->
        <div class="login-visual"></div>

        <!-- Right Side: Form Panel -->
        <div class="login-form-panel">
            
            <div class="form-centered-container">
                
                <!-- Logo -->
                <div class="logo-area">
                    <svg class="logo-svg" width="30" height="30" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Curved tech wave letter T -->
                        <path d="M7 10C7 7.79086 8.79086 6 11 6H21C23.2091 6 25 7.79086 25 10V11.5C25 12.3284 24.3284 13 23.5 13H18.5V23.5C18.5 25.9853 16.4853 28 14 28H13.5C11.0147 28 9 25.9853 9 23.5C9 22.6716 9.67157 22 10.5 22C11.3284 22 12 22.6716 12 23.5C12 24.3284 12.6716 25 13.5 25C14.3284 25 15 24.3284 15 23.5V13H8.5C7.67157 13 7 12.3284 7 11.5V10Z" fill="url(#logo-grad)"/>
                        <circle cx="15.5" cy="5" r="2.5" fill="#38bdf8"/>
                        <defs>
                            <linearGradient id="logo-grad" x1="7" y1="6" x2="25" y2="28" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#38bdf8" />
                                <stop offset="1" stop-color="#0ea5e9" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <span class="logo-text">TechManager <span style="font-weight: 300;">Pro</span></span>
                </div>

                <!-- Form Header -->
                <h1>Login Premium</h1>
                <p class="subtitle">Bem-vindo de volta. Acesse sua conta.</p>

                <!-- Action Form -->
                <form method="POST" action="login.php">
                    
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Senha</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="password-toggle" id="password-toggle" tabindex="-1">
                                <i class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">Acessar Sistema</button>

                    <div class="forgot-link-container">
                        <a href="#" class="forgot-link" onclick="alert('Funcionalidade de recuperação indisponível nesta demonstração. Use o e-mail: admin@techmanager.com e senha: password123')">Esqueci minha senha</a>
                    </div>

                </form>

            </div>

            <!-- Footer copyright -->
            <div class="footer-copyright">
                &copy; 2026 TechManager Pro. Todos os direitos reservados.
            </div>

        </div>

    </div>

    <!-- Notification system for errors -->
    <script>
        function showNotification(message, type = 'error') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `
                <i class="fa-solid fa-circle-xmark"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('active'), 10);
            setTimeout(() => {
                toast.classList.remove('active');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Toggle password visibility logic
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const eyeIcon = passwordToggle.querySelector('i');

        passwordToggle.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye';
            } else {
                passwordInput.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye-slash';
            }
        });
    </script>

    <?php if (!empty($error)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showNotification("<?php echo addslashes($error); ?>", "error");
            });
        </script>
    <?php endif; ?>

</body>
</html>
