<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Llamada al Manifest para la PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#343a40">
    <link rel="apple-touch-icon" href="/assets/icon-192x192.png">

    <!-- Registro del Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registrado en login'))
            .catch(error => console.log('Error SW:', error));
        });
        // 1. APLICACIÓN INSTANTÁNEA ANTES DEL PRIMER PINTADO (Evita el parpadeo de Sidebar y Lista/Grid)
        (function() {
            try {
                const isMobileOrTablet = window.innerWidth <= 992;
                const savedSidebar = localStorage.getItem('sidebar_collapsed');
                const shouldCollapse = (savedSidebar === 'true') || (savedSidebar === null && isMobileOrTablet) || isMobileOrTablet;

                if (shouldCollapse) {
                    document.documentElement.classList.add(' init-sidebar-collapsed');
                    document.documentElement.setAttribute('data-sidebar', 'collapsed');
                }

                const savedView = localStorage.getItem('product_view') || 'list';
                document.documentElement.setAttribute('data-view', savedView);
            } catch (e) {}
        })();

        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // 2. Guardar y restaurar posición del scroll
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPosition_' + window.location.pathname, window.scrollY);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const key = 'scrollPosition_' + window.location.pathname;
            const savedScroll = sessionStorage.getItem(key);
            if (savedScroll !== null) {
                const pos = parseInt(savedScroll, 10);
                requestAnimationFrame(() => {
                    window.scrollTo({ top: pos, behavior: 'instant' });
                });
                sessionStorage.removeItem(key);
            }
        });
    }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    
    <link rel="icon" type="image/x-icon" href="/assets/uploads/logo/gruponet.webp">
    
    <title><?php echo htmlspecialchars($pageName);?><?php echo isset($pageTitle) ? ' | ' . $pageTitle : ''; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Raleway:wght@100..900&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <link rel="stylesheet" href="/assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>

    <link rel="stylesheet" href="/assets/css/styles.css?v=preview"> 
    <link rel="stylesheet" href="/assets/admin/css/admin.css?v=<2.10">
    <link rel="stylesheet" href="/assets/admin/css/lvr.css?v=<1.17">

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // 1. Guardar automáticamente la posición exacta del scroll justo antes de recargar o salir de la página
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('scrollPosition_' + window.location.pathname, window.scrollY);
        });

        // 2. Restaurar la posición apenas el DOM haya renderizado las tarjetas/tablas
        document.addEventListener('DOMContentLoaded', function() {
            const key = 'scrollPosition_' + window.location.pathname;
            const savedScroll = sessionStorage.getItem(key);
            if (savedScroll !== null) {
                const pos = parseInt(savedScroll, 10);
                // Ejecutamos en el siguiente frame de pintado para garantizar la altura del documento
                requestAnimationFrame(() => {
                    window.scrollTo({ top: pos, behavior: 'instant' });
                });
                sessionStorage.removeItem(key);
            }
        });
    </script>

    

    <style>
        /* Fuerza al plugin a usar el 100% del ancho de tu columna */
        .iti { width: 100%; margin-bottom: 1rem; } 
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php
            // Calcular iniciales del usuario (ej. "Maria Ordaz" -> "MO")
            $rawUserName = trim($userInfo['name'] ?? 'Admin');
            $nameParts = preg_split('/\s+/', $rawUserName);
            $userInitials = mb_strtoupper(mb_substr($nameParts[0] ?? 'A', 0, 1, 'UTF-8'), 'UTF-8');
            if (count($nameParts) > 1) {
                $userInitials .= mb_strtoupper(mb_substr($nameParts[1], 0, 1, 'UTF-8'), 'UTF-8');
            }
            $hasCustomAvatar = !empty($userInfo['avatar']) && $userInfo['avatar'] !== 'default.jpg';
            $totalProductsBadge = isset($products) && is_array($products) && ($active_menu ?? '') === 'products' ? count($products) : null;
        ?>
        <aside class="sidebar" id="admin-sidebar">
            <script>
                if (document.documentElement.getAttribute('data-sidebar') === 'collapsed' || window.innerWidth <= 992) {
                    document.getElementById('admin-sidebar').classList.add('sidebar--collapsed');
                }
            </script>
            <!-- 1. CABECERA: Icono + Título + Botón Colapsar Desktop / Cerrar Móvil -->
            <div class="sidebar__header lvr-sb-header">
                <a href="/" class="sidebar__logo">
                    <img src="/assets/uploads/logo/gruponet.webp" alt="Logo Gruponet">
                </a>

                <button type="button" class="sidebar__toggle desktop-toggle lvr-sb-collapse-btn" id="sidebar-toggle" title="Colapsar / Expandir menú">
                    <i class='bx bx-chevrons-left'></i>
                </button>

                <button type="button" class="sidebar__close-mobile" id="sidebar-close-mobile" title="Cerrar menú">
                    <i class='bx bx-x'></i>
                </button>
            </div>
            
            <!-- 3. NAVEGACIÓN -->
            <nav class="sidebar__nav lvr-sb-nav">
                <ul>
                    <?php if ($userInfo['role'] == 'admin' || $userInfo['role'] == 'vendedor'): ?>
                        <li class="nav-header" data-section="admin">
                            <span>ADMINISTRACIÓN</span>
                            <i class='bx bx-chevron-down nav-header-chevron'></i>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'products') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/products" title="Productos">
                                <span class="lvr-nav-icon"><i class='bx bx-shopping-bag'></i></span>
                                <span class="lvr-nav-label">Productos</span>
                                <?php if ($totalProductsBadge !== null): ?>
                                    <span class="lvr-nav-badge"><?php echo $totalProductsBadge; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'offers') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/offers" title="Productos en Oferta">
                                <span class="lvr-nav-icon"><i class='bx bx-gift'></i></span>
                                <span class="lvr-nav-label">Productos en Oferta</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'new-products') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/new-products" title="Recién Llegados">
                                <span class="lvr-nav-icon"><i class='bx bx-star'></i></span>
                                <span class="lvr-nav-label">Recién Llegados</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'categories') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/categories" title="Categorías">
                                <span class="lvr-nav-icon"><i class='bx bx-category'></i></span>
                                <span class="lvr-nav-label">Categorías</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'brands') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/brands" title="Marcas">
                                <span class="lvr-nav-icon"><i class='bx bx-buildings'></i></span>
                                <span class="lvr-nav-label">Marcas</span>
                            </a>
                        </li>
                        <li class="nav-item <?php echo (isset($active_menu) && $active_menu == 'users') ? 'active' : ''; ?>" data-section-item="admin">
                            <a href="/admin/users" title="Usuarios">
                                <span class="lvr-nav-icon"><i class='bx bx-user'></i></span>
                                <span class="lvr-nav-label">Usuarios</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    
                    <?php if ($userInfo['role'] == 'admin'): ?>
                        <li class="nav-header" data-section="pages">
                            <span>GESTIÓN DE PÁGINAS</span>
                            <i class='bx bx-chevron-down nav-header-chevron'></i>
                        </li>

                        <li class="nav-item <?php echo ($active_menu === 'pages-general') ? 'active' : ''; ?>" data-section-item="pages">
                            <a href="/admin/pages/general" title="General">
                                <span class="lvr-nav-icon"><i class='bx bx-slider-alt'></i></span>
                                <span class="lvr-nav-label">General</span>
                            </a>
                        </li>

                        <li class="nav-item <?php echo ($active_menu === 'pages-home') ? 'active' : ''; ?>" data-section-item="pages">
                            <a href="/admin/pages/home" title="Página de Inicio">
                                <span class="lvr-nav-icon"><i class='bx bx-home-alt'></i></span>
                                <span class="lvr-nav-label">Página de Inicio</span>
                            </a>
                        </li>

                        <li class="nav-item <?php echo ($active_menu === 'pages-about') ? 'active' : ''; ?>" data-section-item="pages">
                            <a href="/admin/pages/about" title="Página de Nosotros">
                                <span class="lvr-nav-icon"><i class='bx bx-buildings'></i></span>
                                <span class="lvr-nav-label">Página de Nosotros</span>
                            </a>
                        </li>

                        <li class="nav-item <?php echo ($active_menu === 'pages-services') ? 'active' : ''; ?>" data-section-item="pages">
                            <a href="/admin/pages/services" title="Página de Servicios">
                                <span class="lvr-nav-icon"><i class='bx bx-wrench'></i></span>
                                <span class="lvr-nav-label">Página de Servicios</span>
                            </a>
                        </li>

                        <li class="nav-item <?php echo ($active_menu === 'pages-shipping') ? 'active' : ''; ?>" data-section-item="pages">
                            <a href="/admin/pages/shipping" title="Página de Envíos">
                                <span class="lvr-nav-icon"><i class='bx bxs-truck'></i></span>
                                <span class="lvr-nav-label">Página de Envíos</span>
                            </a>
                        </li>

                        <li class="nav-header" data-section="config">
                            <span>CONFIGURACIÓN</span>
                            <i class='bx bx-chevron-down nav-header-chevron'></i>
                        </li>
                        <li class="nav-item <?php echo ($active_menu === 'settings') ? 'active' : ''; ?>" data-section-item="config">
                            <a href="/admin/settings" title="Conf. Avanzada">
                                <span class="lvr-nav-icon"><i class='bx bx-cog'></i></span>
                                <span class="lvr-nav-label">Conf. Avanzada</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- 4. FOOTER: Perfil con Iniciales/Avatar + Punto Verde + Botón Salir -->
            <div class="sidebar__footer lvr-sb-footer">
                <a href="/admin/users/edit/<?php echo $_SESSION['idUser']; ?>" class="lvr-sb-user-card" title="Editar mi perfil">
                    <div class="lvr-sb-avatar">
                        <?php if ($hasCustomAvatar): ?>
                            <img src="/assets/uploads/users/<?php echo htmlspecialchars($userInfo['avatar']); ?>" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="lvr-sb-initials" style="display:none;"><?php echo htmlspecialchars($userInitials); ?></span>
                        <?php else: ?>
                            <span class="lvr-sb-initials"><?php echo htmlspecialchars($userInitials); ?></span>
                        <?php endif; ?>
                        <span class="lvr-sb-online-dot"></span>
                    </div>
                    <div class="lvr-sb-user-info">
                        <strong><?php echo htmlspecialchars($userInfo['name']); ?></strong>
                        <span><?php echo htmlspecialchars($userInfo['email']); ?></span>
                    </div>
                </a>

                <a href="/auth/logout" class="logout-btn lvr-sb-logout-icon" title="Cerrar sesión">
                    <i class='bx bx-log-out'></i>
                </a>
            </div>
        </aside>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('admin-sidebar');

                // En tablet y móvil (<= 992px), iniciar siempre colapsado (solo iconos)
                if (sidebar && window.innerWidth <= 992) {
                    sidebar.classList.add('sidebar--collapsed');
                }

                // 1. Buscador en tiempo real del Sidebar + Atajo Ctrl+K / Cmd+K
                const sbSearchInput = document.getElementById('lvr-sidebar-search');
                const sbNav = document.querySelector('.lvr-sb-nav');

                if (sbSearchInput && sbNav) {
                    sbSearchInput.addEventListener('input', function() {
                        const q = this.value.trim().toLowerCase();
                        const items = sbNav.querySelectorAll('.nav-item');
                        const headers = sbNav.querySelectorAll('.nav-header');

                        items.forEach(li => {
                            const text = li.textContent.toLowerCase();
                            li.style.display = (q === '' || text.includes(q)) ? '' : 'none';
                        });

                        headers.forEach(h => {
                            const sec = h.getAttribute('data-section');
                            if (!sec || q === '') {
                                h.style.display = '';
                                return;
                            }
                            const visibleChildren = Array.from(sbNav.querySelectorAll(`.nav-item[data-section-item="${sec}"]`))
                                .some(el => el.style.display !== 'none');
                            h.style.display = visibleChildren ? '' : 'none';
                        });
                    });

                    document.addEventListener('keydown', function(e) {
                        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                            e.preventDefault();
                            if (sidebar && sidebar.classList.contains('sidebar--collapsed')) {
                                sidebar.classList.remove('sidebar--collapsed');
                                localStorage.setItem('sidebar_collapsed', 'false');
                            }
                            sbSearchInput.focus();
                        }
                    });
                }

                // 2. Contraer / Expandir secciones al hacer clic en los encabezados
                document.querySelectorAll('.lvr-sb-nav .nav-header').forEach(header => {
                    header.addEventListener('click', function() {
                        if (sidebar && sidebar.classList.contains('sidebar--collapsed')) return;

                        const section = this.getAttribute('data-section');
                        if (!section) return;
                        const isCollapsed = this.classList.toggle('is-collapsed');
                        document.querySelectorAll(`.lvr-sb-nav .nav-item[data-section-item="${section}"]`).forEach(item => {
                            item.classList.toggle('section-hidden', isCollapsed);
                        });
                    });
                });
            });
        </script>

        <main class="main-content">
            
