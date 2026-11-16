<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Llamada al Manifest para la PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#131e32">
    <link rel="apple-touch-icon" href="/assets/icon-192x192.png">

    <!-- Registro del Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registrado en login'))
            .catch(error => console.log('Error SW:', error));
        });
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("clave");

        togglePassword.addEventListener("click", function () {
            // Alternar el tipo de input entre 'password' y 'text'
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            
            // Alternar el icono del ojo
            this.classList.toggle("bx-hide");
            this.classList.toggle("bx-show");
        });
    });
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de administración interno">
    <meta name="author" content="<?php echo htmlspecialchars($pageName);?>">
    
    <link rel="icon" type="image/x-icon" href="/assets/uploads/logo/gruponet.webp">
    
    <title><?php echo htmlspecialchars($pageName);?><?php echo isset($pageTitle) ? ' | ' . $pageTitle : ''; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Raleway:wght@700&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    
    <!-- Archivos CSS -->
    <link rel="stylesheet" href="/assets/css/styles.css?v=<2.1">
    <link rel="stylesheet" href="/assets/css/lvr.css?v=1.0"> 
</head>

<body class="lvr-login-body">

    <div class="lvr-login-wrapper">
        <div class="lvr-login-card">
            
            <!-- Logo Header -->
            <div class="lvr-login-header">
                <img src="/assets/uploads/logo/gruponet.webp" alt="Logo de la empresa" class="lvr-login-logo">
                <div class="lvr-login-badge">
                    <i class='bx bx-check-shield'></i> Acceso interno
                </div>
            </div>

            <!-- Títulos -->
            <h1 class="lvr-login-title">Panel de administración</h1>
            <p class="lvr-login-subtitle">Gestión de productos de Caroní Computer. Solo personal autorizado.</p>

            <!-- Formulario -->
            <form action="/auth/login_gestion_2025" method="POST" class="lvr-login-form">
                
                <?php if (!empty($alert)): ?>
                    <div class="alert-error" style="margin-bottom: 1.5rem;"><?php echo $alert; ?></div>
                <?php endif; ?>

                <div class="lvr-input-group">
                    <label for="usuario">Usuario</label>
                    <div class="lvr-input-wrapper">
                        <i class='bx bx-user lvr-input-icon'></i>
                        <input type="text" id="usuario" name="usuario" placeholder="tu.usuario" required>
                    </div>
                </div>

                <div class="lvr-input-group">
                    <label for="clave">Contraseña</label>
                    <div class="lvr-input-wrapper">
                        <i class='bx bx-lock-alt lvr-input-icon'></i>
                        <input type="password" id="clave" name="clave" placeholder="••••••••" required>
                        <i class='bx bx-show lvr-toggle-password' id="togglePassword" title="Mostrar/Ocultar contraseña"></i>
                    </div>
                </div>

                <!-- Checkbox Oculto pero funcional para mantener la sesión si así lo requieres, 
                     o si no quieres que se vea, lo dejamos oculto visualmente pero activo en código -->
                <div style="display: none;">
                    <input type="checkbox" name="remember_me" id="remember_me" value="1" checked>
                </div>

                <button type="submit" class="lvr-btn-submit">Entrar al panel</button>
            </form>
        </div>

        <!-- Footer del Login -->
        <div class="lvr-login-footer">
            <p>Si no tienes credenciales, solicítalas al administrador del sistema.</p>
            <small>©<?php echo date('Y'); ?> GRUPO CARONÍ COMPUTER C.A. · Uso interno</small>
        </div>
    </div>

</body>
</html>