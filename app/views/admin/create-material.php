<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-product-page" method="POST" action="/admin/storeMaterial" id="material-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/materials">Materiales</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Nuevo</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/materials" class="lvr-back-btn" title="Volver a Materiales">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Agregar Nuevo Material</h1>
                </div>
                <span class="lvr-draft-badge">MATERIAL-BORRADOR</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. INFORMACIÓN BÁSICA -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-purchase-tag-alt'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Información básica</h3>
                                <p>Identifica el material o material de los productos</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-field-group">
                        <label class="lvr-field-label" for="name"><i class='bx bx-Label'></i> Nombre del material</label>
                        <input type="text" id="name" name="name" class="lvr-input" placeholder="Ej. PLA / PETG / Resina / Asus" required>
                    </div>
                </div>

                <!-- 2. IMAGEN DE LA MATERIAL -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-image-add'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Logo o imagen</h3>
                                <p>Se mostrará en la sección destacada de la página principal</p>
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
                        <i class='bx bx-image'></i>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Material</span>
                        <strong id="summary-material-name">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Imagen</span>
                        <strong id="summary-material-image">No</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Visibilidad</span>
                        <strong>Pública</strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Campos obligatorios</span>
                            <strong id="summary-req-count">0/1</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-req-fill"></div>
                        </div>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar material
                    </button>

                    <a href="/admin/materials" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameEl    = document.getElementById('name');
    const imgInput  = document.getElementById('image-input');
    const uploadBox = document.querySelector('.image-upload-box');

    function updateSummary() {
        const val = nameEl ? nameEl.value.trim() : '';
        document.getElementById('summary-material-name').textContent = val || '—';

        const previewImg = uploadBox ? uploadBox.querySelector('img.image-preview') : null;
        const thumbBox   = document.getElementById('summary-thumb-box');
        if (previewImg && previewImg.src) {
            thumbBox.innerHTML = `<img src="${previewImg.src}" alt="Material">`;
            document.getElementById('summary-material-image').textContent = 'Sí';
        } else {
            thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
            document.getElementById('summary-material-image').textContent = 'No';
        }

        const req = val !== '' ? 1 : 0;
        document.getElementById('summary-req-count').textContent = `${req}/1`;
        const fill = document.getElementById('summary-req-fill');
        fill.style.width = `${req * 100}%`;
        fill.style.backgroundColor = req === 1 ? '#16a34a' : '#2563eb';
    }

    if (nameEl) nameEl.addEventListener('input', updateSummary);
    if (uploadBox) {
        const obs = new MutationObserver(() => setTimeout(updateSummary, 80));
        obs.observe(uploadBox, { childList: true, subtree: true, attributes: true });
    }
    if (imgInput) imgInput.addEventListener('change', () => setTimeout(updateSummary, 120));

    updateSummary();
});
</script>