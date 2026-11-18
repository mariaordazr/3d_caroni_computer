<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-product-page" method="POST" action="/admin/storeUser" id="user-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/users">Usuarios</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Nuevo</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/users" class="lvr-back-btn" title="Volver a Usuarios">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Agregar Nuevo Usuario</h1>
                </div>
                <span class="lvr-draft-badge">USUARIO-BORRADOR</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. INFORMACIÓN Y CREDENCIALES -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-user-plus'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Datos personales y credenciales</h3>
                                <p>Identifica al colaborador y configura sus permisos de acceso</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="name"><i class='bx bx-user'></i> Nombre completo</label>
                            <input type="text" id="name" name="name" class="lvr-input" placeholder="Ej. Carlos Pérez" required autocomplete="off">
                        </div>

                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="username"><i class='bx bx-at'></i> Nombre de usuario</label>
                            <input type="text" id="username" name="username" class="lvr-input" placeholder="Ej. carlos.perez" required autocomplete="new-username">
                        </div>

                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="email"><i class='bx bx-envelope'></i> Correo electrónico</label>
                            <input type="email" id="email" name="email" class="lvr-input" placeholder="correo@ejemplo.com" required autocomplete="off">
                        </div>

                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="role"><i class='bx bx-shield-quarter'></i> Rol del usuario</label>
                            <select id="role" name="role" class="lvr-select" required>
                                <option value="seller" selected>Vendedor</option>
                                <option value="tech">Técnico</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <!-- Contraseña con ojito -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="password"><i class='bx bx-lock-alt'></i> Contraseña</label>
                            <div style="position: relative; display: flex; align-items: center; width: 100%;">
                                <input type="password" id="password" name="password" class="lvr-input" placeholder="••••••••" required autocomplete="new-password" style="padding-right: 42px !important;">
                                <button type="button" class="lvr-toggle-pass-btn" data-target="password" title="Mostrar / ocultar contraseña" style="position: absolute; right: 10px; background: transparent; border: none; color: #64748b; font-size: 1.2rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 4px;">
                                    <i class='bx bx-show'></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirmar contraseña con ojito -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="password_confirmation"><i class='bx bx-check-shield'></i> Confirmar contraseña</label>
                            <div style="position: relative; display: flex; align-items: center; width: 100%;">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="lvr-input" placeholder="••••••••" required autocomplete="new-password" style="padding-right: 42px !important;">
                                <button type="button" class="lvr-toggle-pass-btn" data-target="password_confirmation" title="Mostrar / ocultar contraseña" style="position: absolute; right: 10px; background: transparent; border: none; color: #64748b; font-size: 1.2rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 4px;">
                                    <i class='bx bx-show'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. AVATAR DEL USUARIO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-image-add'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Avatar del usuario</h3>
                                <p>Opcional · Imagen de perfil para identificar al colaborador</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-upload-box" style="width: 132px; height: 132px; border-radius: 16px;">
                        <label for="image-input" style="border-radius: 16px; border: 1.5px dashed #cbd5e1; background: #ffffff; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <div class="image-placeholder desktop-view" id="image-preview-container" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; color: #64748b;">
                                <i class='bx bx-image-add' style="font-size: 1.4rem; color: #64748b;"></i>
                                <span style="font-size: 0.78rem; font-weight: 600;">Galería</span>
                            </div>
                            <div class="image-placeholder mobile-view mobile-split" id="image-preview-container">
                                <div class="upload-btn camera-btn" data-capture="environment" title="Tomar foto">
                                    <i class='bx bx-camera'></i>
                                    <span class="btn-text">Cámara</span>
                                </div>
                                <div class="upload-btn gallery-btn" data-capture="" title="Subir de galería">
                                    <i class='bx bx-image-add'></i>
                                    <span class="btn-text">Galería</span>
                                </div>
                            </div>
                        </label>
                        <input type="file" name="image" id="image-input" accept="image/*" style="display:none;">
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-thumb-box">
                        <i class='bx bx-user'></i>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Nombre</span>
                        <strong id="summary-user-name">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Usuario</span>
                        <strong id="summary-user-username">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Correo</span>
                        <strong id="summary-user-email">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Avatar</span>
                        <strong id="summary-user-avatar">No</strong>
                    </div>

                    <div class="lvr-summary-pill definitive">
                        <span>Rol</span>
                        <strong id="summary-user-role" style="font-size: 0.95rem;">Vendedor</strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Campos obligatorios</span>
                            <strong id="summary-req-count">0/4</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-req-fill"></div>
                        </div>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar usuario
                    </button>

                    <a href="/admin/users" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Funcionalidad del ojito para Mostrar / Ocultar contraseña
    document.querySelectorAll('.lvr-toggle-pass-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputEl = document.getElementById(targetId);
            const iconEl = this.querySelector('i');
            if (!inputEl) return;

            if (inputEl.type === 'password') {
                inputEl.type = 'text';
                if (iconEl) {
                    iconEl.classList.remove('bx-show');
                    iconEl.classList.add('bx-hide');
                }
                this.style.color = '#2563eb';
            } else {
                inputEl.type = 'password';
                if (iconEl) {
                    iconEl.classList.remove('bx-hide');
                    iconEl.classList.add('bx-show');
                }
                this.style.color = '#64748b';
            }
        });
    });

    // 2. Sincronización del panel de Resumen en vivo
    const nameEl     = document.getElementById('name');
    const userEl     = document.getElementById('username');
    const emailEl    = document.getElementById('email');
    const roleEl     = document.getElementById('role');
    const passEl     = document.getElementById('password');
    const passConfEl = document.getElementById('password_confirmation');
    const imgInput   = document.getElementById('image-input');
    const uploadBox  = document.querySelector('.image-upload-box');

    const roleNames = { 'seller': 'Vendedor', 'tech': 'Técnico', 'admin': 'Administrador' };

    function updateSummary() {
        const n = nameEl.value.trim();
        const u = userEl.value.trim();
        const e = emailEl.value.trim();
        const r = roleEl.value;
        const p = passEl.value;
        const c = passConfEl.value;

        document.getElementById('summary-user-name').textContent     = n || '—';
        document.getElementById('summary-user-username').textContent = u || '—';
        document.getElementById('summary-user-email').textContent    = e || '—';
        document.getElementById('summary-user-role').textContent     = roleNames[r] || r;

        const previewImg = uploadBox ? uploadBox.querySelector('img.image-preview') : null;
        const thumbBox   = document.getElementById('summary-thumb-box');
        if (previewImg && previewImg.src) {
            thumbBox.innerHTML = `<img src="${previewImg.src}" alt="Avatar">`;
            document.getElementById('summary-user-avatar').textContent = 'Sí';
        } else {
            thumbBox.innerHTML = `<i class='bx bx-user'></i>`;
            document.getElementById('summary-user-avatar').textContent = 'No';
        }

        let req = 0;
        if (n !== '') req++;
        if (u !== '') req++;
        if (e !== '') req++;
        if (p !== '' && p === c) req++;

        document.getElementById('summary-req-count').textContent = `${req}/4`;
        const fill = document.getElementById('summary-req-fill');
        fill.style.width = `${Math.round((req / 4) * 100)}%`;
        fill.style.backgroundColor = req === 4 ? '#16a34a' : '#2563eb';
    }

    [nameEl, userEl, emailEl, roleEl, passEl, passConfEl].forEach(el => {
        if (el) {
            el.addEventListener('input', updateSummary);
            el.addEventListener('change', updateSummary);
        }
    });

    if (uploadBox) {
        const obs = new MutationObserver(() => setTimeout(updateSummary, 80));
        obs.observe(uploadBox, { childList: true, subtree: true, attributes: true });
    }
    if (imgInput) imgInput.addEventListener('change', () => setTimeout(updateSummary, 120));

    updateSummary();
});
</script>