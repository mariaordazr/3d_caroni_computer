<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $i = 0; 
    $imageName0 = $bannerImage['content_value'] ?? null; 
    $isExisting0 = !empty($imageName0);
?>

<section class="content-panel">
    <form class="edit-form lvr-admin-page lvr-cms-page" method="POST" action="/admin/pages/shipping/update" id="shipping-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Envíos</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Gestión de Página: Envíos</h1>
                </div>
                <span class="lvr-draft-badge">ENVÍOS Y SEDE</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. BANNER PRINCIPAL -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-image'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Banner principal</h3>
                                <p>Gestiona la imagen panorámica del encabezado superior de Envíos</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" style="justify-content: center;">
                            <div class="image-slot lvr-banner-wide-slot <?php echo $isExisting0 ? 'has-preview' : ''; ?>" id="shipping-banner-slot">
                                <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $i; ?>" value="<?php echo $isExisting0 ? htmlspecialchars($imageName0) : ''; ?>">
                                <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $i; ?>" value="">
                                <label for="image-slot-<?php echo $i; ?>">
                                    <?php if ($isExisting0): ?>
                                        <div class="image-preview-slot-frame">
                                            <img src="/assets/uploads/shipping/banner/<?php echo htmlspecialchars($imageName0); ?>" class="image-preview-slot">
                                            <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $i; ?>"><i class='bx bx-x'></i></button> 
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Subir Banner de Envíos</span>
                                        </div>
                                    <?php endif; ?>
                                </label>
                                <input type="file" name="image_slot_<?php echo $i; ?>" id="image-slot-<?php echo $i; ?>" accept="image/*" style="display:none;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SECCIÓN ENVÍOS / AGENCIAS (MATERIALES) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bxs-truck'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección Envíos y agencias de encomienda</h3>
                                <p>Información sobre envíos nacionales y logos de las empresas de transporte</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2" style="margin-bottom: 1.15rem;">
                        <div class="lvr-field-group">
                            <label for="s1_title" class="lvr-field-label">Título de Envíos</label>
                            <input type="text" name="shipping_title_0" id="s1_title" class="lvr-input form-control" value="<?php echo htmlspecialchars($titleIds[0] ?? ''); ?>" placeholder="Ej: Envíos a Nivel Nacional">
                        </div>

                        <div class="lvr-field-group">
                            <label for="s1_text" class="lvr-field-label">Texto de Envíos</label>
                            <textarea name="shipping_text_0" id="s1_text" class="lvr-textarea-2lines form-control" rows="2" placeholder="Condiciones y detalles de envío..."><?php echo htmlspecialchars($textIds[0] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="lvr-field-group">
                        <label class="lvr-field-label"><i class='bx bx-package'></i> Logos de agencias / couriers</label>
                        <div class="image-manager">
                            <div class="image-slots-container" id="brands-container">
                                <?php 
                                $currentIndex = 2; 
                                if (!empty($brandContent)): 
                                    foreach ($brandContent as $brand):
                                        $imageName = $brand['content_value'];
                                ?>
                                        <div class="image-slot has-preview" data-index="<?php echo $currentIndex; ?>">
                                            <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $currentIndex; ?>" value="<?php echo htmlspecialchars($imageName); ?>">
                                            <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $currentIndex; ?>" value="">
                                        
                                            <label for="image-slot-<?php echo $currentIndex; ?>">
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/shipping/body/<?php echo htmlspecialchars($imageName); ?>" class="image-preview-slot">
                                                    <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $currentIndex; ?>"><i class='bx bx-x'></i></button> 
                                                </div>
                                            </label>
                                            <input type="file" name="image_slot_<?php echo $currentIndex; ?>" id="image-slot-<?php echo $currentIndex; ?>" accept="image/*" style="display:none;">
                                        </div>
                                <?php 
                                        $currentIndex++;
                                    endforeach; 
                                endif; 
                                ?>
                                <div class="image-slot" id="btn-add-brand">
                                    <label>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Añadir Agencia</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. SECCIÓN LOCAL / TIENDA FÍSICA -->
                <?php 
                    $i = 1; 
                    $imageNameLocal = $shippingImage['content_value'] ?? null; 
                    $isExistingLocal = !empty($imageNameLocal);
                ?>
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-store-alt'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección Local y ubicación en Google Maps</h3>
                                <p>Fotografía de la tienda física, dirección detallada y mapa interactivo</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-cms-media-split">
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-image'></i> Foto del Local</label>
                            <div class="image-manager">
                                <div class="image-slots-container">
                                    <div class="image-slot <?php echo $isExistingLocal ? 'has-preview' : ''; ?>" id="shipping-local-slot">
                                        <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $i; ?>" value="<?php echo $isExistingLocal ? htmlspecialchars($imageNameLocal) : ''; ?>">
                                        <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $i; ?>" value="">
                                    
                                        <label for="image-slot-<?php echo $i; ?>">
                                            <?php if ($isExistingLocal): ?>
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/shipping/body/<?php echo htmlspecialchars($imageNameLocal); ?>" class="image-preview-slot">
                                                    <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $i; ?>"><i class='bx bx-x'></i></button> 
                                                </div>
                                            <?php else: ?>
                                                <div class="image-placeholder-slot">
                                                    <i class='bx bx-image-add'></i>
                                                    <span>Subir Local</span>
                                                </div>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" name="image_slot_<?php echo $i; ?>" id="image-slot-<?php echo $i; ?>" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <div class="lvr-field-group">
                                <label for="s2_title" class="lvr-field-label">Título del Local</label>
                                <input type="text" name="shipping_title_1" id="s2_title" class="lvr-input form-control" value="<?php echo htmlspecialchars($titleIds[1] ?? ''); ?>" placeholder="Ej: Visita Nuestra Tienda Física">
                            </div>

                            <div class="lvr-field-group">
                                <label for="s2_address" class="lvr-field-label">Texto de Dirección y Horarios</label>
                                <textarea name="shipping_text_1" id="s2_address" class="lvr-textarea form-control" rows="3" placeholder="Dirección exacta, puntos de referencia, horarios..."><?php echo htmlspecialchars($textIds[1] ?? ''); ?></textarea>
                            </div>

                            <div class="lvr-field-group">
                                <label for="s2_map_url" class="lvr-field-label"><i class='bx bx-map-pin'></i> URL o Iframe de Google Maps</label>
                                <input type="text" name="shipping_link" id="s2_map_url" class="lvr-input form-control" value="<?php echo htmlspecialchars($shippingLink['content_value'] ?? ''); ?>" placeholder="https://www.google.com/maps/embed?pb=...">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-shipping-thumb-box">
                        <?php if ($isExisting0): ?>
                            <img src="/assets/uploads/shipping/banner/<?php echo htmlspecialchars($imageName0); ?>" alt="Banner Envíos">
                        <?php else: ?>
                            <i class='bx bxs-truck'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Banner principal</span>
                        <strong id="summary-shipping-banner"><?php echo $isExisting0 ? 'Activo' : 'Sin imagen'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Foto del local</span>
                        <strong id="summary-shipping-local"><?php echo $isExistingLocal ? 'Activa' : 'Sin imagen'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Agencias de envío</span>
                        <strong id="summary-shipping-brands">0</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Mapa configurado</span>
                        <strong id="summary-shipping-map">No</strong>
                    </div>

                    <button type="button" id="btn-open-preview" class="lvr-btn-soft-blue" style="width: 100%; justify-content: center; height: 40px; margin-top: 0.25rem;">
                        <i class='bx bx-show'></i> Vista previa
                    </button>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar envíos
                    </button>

                    <a href="/admin/products" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

        <!-- MODAL DE RECORTE (CROPPER.JS) -->
        <div id="crop-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.75); backdrop-filter: blur(3px); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; padding:24px; border-radius:16px; width:90%; max-width:820px; display:flex; flex-direction:column; gap:16px; max-height:90vh; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
                <h3 id="crop-modal-title" style="margin:0; font-size:1.15rem; color:#0f172a;">Recortar Imagen</h3>
                <div style="max-height: 60vh; overflow: hidden; background:#f8fafc; border-radius:12px; display:flex; justify-content:center;">
                    <img id="image-to-crop" style="max-width: 100%; display: block;">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" id="btn-cancel-crop" class="btn" style="background:#fee2e2; color:#dc2626; border-radius:999px; padding:8px 18px; font-weight:600; border:none; cursor:pointer;">Cancelar</button>
                    <button type="button" id="btn-crop-confirm" class="btn btn-primary" style="background:#2563eb; color:#fff; border-radius:999px; padding:8px 20px; font-weight:600; border:none; cursor:pointer;">Recortar y Usar</button>
                </div>
            </div>
        </div>

        <!-- MODAL DE VISTA PREVIA -->
        <div id="preview-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,1); z-index:99999; overflow-y:auto;">
            <div style="position:sticky; top:0; left:0; width:100%; background:#0f172a; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.2);">
                <h3 style="color:#fff; margin:0; font-size:1.1rem;">Vista Previa (No guardado)</h3>
                <div style="display:flex; gap:10px;">
                    <button type="button" id="btn-close-preview" class="btn" style="background:#fff; color:#0f172a; border-radius:999px; padding:6px 16px; font-weight:600;">Seguir Editando</button>
                    <button type="button" onclick="document.querySelector('.edit-form').submit();" class="btn btn-primary" style="background:#2563eb; color:#fff; border-radius:999px; padding:6px 18px; font-weight:600;">Guardar Cambios</button>
                </div>
            </div>

            <div class="preview-content-wrapper" style="background-color: #ededed; padding-bottom: 50px;">
                <section class="hero-section" id="preview-banner-bg" style="background-size: cover; background-position: center;">
                    <div class="container">
                        <h1 class="hero-section__title">Envíos a Nivel Nacional</h1>
                    </div>
                </section>

                <section class="section">
                    <div class="container content-section">
                        <h2 id="preview-title-0"></h2>
                        <p id="preview-text-0"></p>
                        <div style="display: flex; gap: 3rem; overflow-x: auto; padding: 20px 0; justify-content: center; align-items: center;" id="preview-brands-container"></div>
                    </div>
                </section>

                <section class="section section--light">
                    <div class="container content-section">
                        <div class="shipping-location-container">
                            <h2 id="preview-title-1"></h2>
                            <p class="about__description" id="preview-text-1"></p>
                            
                            <div class="store-image-container">
                                <img id="preview-img-1" src="" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            
                            <div id="preview-map-container" style="margin-top: 2rem; width: 100%;">
                                <iframe id="preview-map-iframe" width="100%" height="450" style="border:0; border-radius:12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <script src="/assets/admin/js/admin-shipping-preview.js"></script>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSlot      = document.getElementById('shipping-banner-slot');
        const localSlot       = document.getElementById('shipping-local-slot');
        const brandsContainer = document.getElementById('brands-container');
        const mapInput        = document.getElementById('s2_map_url');

        function updateShippingSummary() {
            const hasBanner = bannerSlot && bannerSlot.classList.contains('has-preview');
            document.getElementById('summary-shipping-banner').textContent = hasBanner ? 'Activo' : 'Sin imagen';

            const thumbBox = document.getElementById('summary-shipping-thumb-box');
            if (thumbBox && bannerSlot) {
                const img = bannerSlot.querySelector('img.image-preview-slot');
                if (hasBanner && img && img.src) {
                    thumbBox.innerHTML = `<img src="${img.src}" alt="Banner Envíos">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bxs-truck'></i>`;
                }
            }

            const hasLocal = localSlot && localSlot.classList.contains('has-preview');
            document.getElementById('summary-shipping-local').textContent = hasLocal ? 'Activa' : 'Sin imagen';

            if (brandsContainer) {
                const activeBrands = brandsContainer.querySelectorAll('.image-slot.has-preview').length;
                document.getElementById('summary-shipping-brands').textContent = activeBrands;
            }

            const hasMap = mapInput && mapInput.value.trim() !== '';
            document.getElementById('summary-shipping-map').textContent = hasMap ? 'Sí' : 'No';
        }

        if (mapInput) mapInput.addEventListener('input', updateShippingSummary);

        [bannerSlot, localSlot, brandsContainer].forEach(container => {
            if (container) {
                const obs = new MutationObserver(() => setTimeout(updateShippingSummary, 80));
                obs.observe(container, { childList: true, subtree: true, attributes: true });
            }
        });

        updateShippingSummary();
    });
</script>