<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $i = 0;
    $item0 = $bannerImage ?? null; 
    $imageName0 = $item0['content_value'] ?? null; 
    $isExisting0 = !empty($imageName0);
?>

<section class="content-panel">
    <form class="edit-form lvr-admin-page lvr-cms-page" method="POST" action="/admin/pages/services/update" id="services-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Servicios</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Gestión de Página: Servicios</h1>
                </div>
                <span class="lvr-draft-badge">SERVICIOS TÉCNICOS</span>
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
                                <p>Gestiona la imagen panorámica superior de la página de Servicios</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" style="justify-content: center;">
                            <div class="image-slot lvr-banner-wide-slot <?php echo $isExisting0 ? 'has-preview' : ''; ?>" id="services-banner-slot">
                                <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $i; ?>" value="<?php echo $isExisting0 ? htmlspecialchars($imageName0) : ''; ?>">
                                <input type="hidden" class="delete-slot-flag" name="delete_slot_<?php echo $i; ?>" value="0">
                            
                                <label for="image-slot-<?php echo $i; ?>">
                                    <?php if ($isExisting0): ?>
                                        <div class="image-preview-slot-frame">
                                            <img src="/assets/uploads/services/banner/<?php echo htmlspecialchars($imageName0); ?>" class="image-preview-slot">
                                            <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $i; ?>">
                                                <i class='bx bx-x'></i>
                                            </button> 
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Subir Banner de Servicios</span>
                                        </div>
                                    <?php endif; ?>
                                </label>
                                <input type="file" name="image_slot_<?php echo $i; ?>" id="image-slot-<?php echo $i; ?>" accept="image/*" style="display:none;">
                                <?php if ($isExisting0): ?>
                                    <input type="hidden" name="delete_image_<?php echo $i; ?>" value="">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. TARJETAS DE SERVICIOS (1, 2 Y 3) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-wrench'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección 1: Tarjetas de servicios</h3>
                                <p>Configura las 3 tarjetas principales de servicios con su título, descripción e imagen</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <?php 
                            $serviceCards = [
                                ['num' => 1, 'icon' => 'bx-chip',    'label' => 'Tarjeta de Servicio 1'],
                                ['num' => 2, 'icon' => 'bx-wrench',  'label' => 'Tarjeta de Servicio 2'],
                                ['num' => 3, 'icon' => 'bx-desktop', 'label' => 'Tarjeta de Servicio 3'],
                            ];
                            foreach ($serviceCards as $idxC => $sc):
                                $i = $sc['num'];
                                $imgNameCard = $imageIds[$i - 1] ?? null;
                                $isExistingCard = !empty($imgNameCard);
                        ?>
                            <div class="lvr-cms-subcard <?php echo $idxC === 2 ? 'lvr-field-full' : ''; ?>">
                                <div class="lvr-cms-subcard-header">
                                    <h4 class="lvr-cms-subcard-title"><i class='bx <?php echo $sc['icon']; ?>'></i> <?php echo $sc['label']; ?></h4>
                                    <span class="lvr-draft-badge" style="font-size: 0.72rem; padding: 2px 9px;">Servicio #<?php echo $i; ?></span>
                                </div>

                                <div class="lvr-cms-media-split">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Imagen</label>
                                        <div class="image-manager">
                                            <div class="image-slots-container">
                                                <div class="image-slot <?php echo $isExistingCard ? 'has-preview' : ''; ?> js-service-card-slot">
                                                    <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $i; ?>" value="<?php echo $isExistingCard ? htmlspecialchars($imgNameCard) : ''; ?>">
                                                    <input type="hidden" class="delete-slot-flag" name="delete_slot_<?php echo $i; ?>" value="0">
                                                
                                                    <label for="image-slot-<?php echo $i; ?>">
                                                        <?php if ($isExistingCard): ?>
                                                            <div class="image-preview-slot-frame">
                                                                <img src="/assets/uploads/services/body/<?php echo htmlspecialchars($imgNameCard); ?>" class="image-preview-slot">
                                                                <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $i; ?>">
                                                                    <i class='bx bx-x'></i>
                                                                </button> 
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="image-placeholder-slot">
                                                                <i class='bx bx-image-add'></i>
                                                                <span>Galería</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </label>
                                                    <input type="file" name="image_slot_<?php echo $i; ?>" id="image-slot-<?php echo $i; ?>" accept="image/*" style="display:none;">
                                                    <?php if ($isExistingCard): ?>
                                                        <input type="hidden" name="delete_image_<?php echo $i; ?>" value="">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                        <div class="lvr-field-group">
                                            <label for="card<?php echo $i; ?>_title" class="lvr-field-label">Título</label>
                                            <input type="text" name="service_title_<?php echo $i; ?>" id="card<?php echo $i; ?>_title" class="lvr-input form-control js-service-card-title" value="<?php echo htmlspecialchars($titleIds[$i] ?? ''); ?>" placeholder="Título del servicio <?php echo $i; ?>">
                                        </div>

                                        <div class="lvr-field-group">
                                            <label for="card<?php echo $i; ?>_text" class="lvr-field-label">Texto descriptivo</label>
                                            <textarea name="service_text_<?php echo $i; ?>" id="card<?php echo $i; ?>_text" class="lvr-textarea form-control" rows="3" placeholder="Descripción del servicio <?php echo $i; ?>..."><?php echo htmlspecialchars($textIds[$i] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 3. SECCIÓN 2 (VIDEO YOUTUBE) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bxl-youtube'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección 2: Video demostrativo</h3>
                                <p>Título, descripción y enlace de YouTube para mostrar el taller o proceso técnico</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <div class="lvr-field-group">
                            <label for="s2_title" class="lvr-field-label">Título de la sección</label>
                            <input type="text" name="service_title_4" id="s2_title" class="lvr-input form-control" value="<?php echo htmlspecialchars($titleIds[4] ?? ''); ?>" placeholder="Ej: Conoce nuestro laboratorio">
                        </div>

                        <div class="lvr-field-group">
                            <label for="s2_text2" class="lvr-field-label"><i class='bx bxl-youtube'></i> Enlace o iframe de YouTube</label>
                            <textarea name="service_link" id="s2_text2" class="lvr-textarea-2lines form-control" rows="2" placeholder="Pega la URL o código embed de YouTube..."><?php echo htmlspecialchars($serviceLink['content_value'] ?? ''); ?></textarea>
                        </div>

                        <div class="lvr-field-group lvr-field-full">
                            <label for="s2_text1" class="lvr-field-label">Texto descriptivo del video</label>
                            <textarea name="service_text_4" id="s2_text1" class="lvr-textarea form-control" rows="3" placeholder="Breve descripción que acompaña al video..."><?php echo htmlspecialchars($textIds[4] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 4. CLIENTES / ALIADOS (LOGOS DINÁMICOS) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-buildings'></i>
                                <span class="lvr-step-num">4</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Clientes y aliados comerciales</h3>
                                <p>Encabezado de la sección y galería de logos de empresas aliadas</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2" style="margin-bottom: 1.15rem;">
                        <div class="lvr-field-group">
                            <label for="s1_title" class="lvr-field-label">Título de Clientes / Aliados</label>
                            <input type="text" name="service_title_0" id="s1_title" class="lvr-input form-control" value="<?php echo htmlspecialchars($titleIds[0] ?? ''); ?>" placeholder="Ej: Empresas que confían en nosotros">
                        </div>

                        <div class="lvr-field-group">
                            <label for="s1_text" class="lvr-field-label">Subtítulo o texto</label>
                            <textarea name="service_text_0" id="s1_text" class="lvr-textarea-2lines form-control" rows="2" placeholder="Descripción breve de aliados..."><?php echo htmlspecialchars($textIds[0] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="lvr-field-group">
                        <label class="lvr-field-label"><i class='bx bx-images'></i> Logos de clientes</label>
                        <div class="image-manager">
                            <div class="image-slots-container" id="clients-container">
                                <?php 
                                $currentIndex = 4; 
                                if (!empty($clientsContent)): 
                                    foreach ($clientsContent as $client):
                                        $imageName = $client['content_value'];
                                ?>
                                        <div class="image-slot has-preview" data-index="<?php echo $currentIndex; ?>">
                                            <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $currentIndex; ?>" value="<?php echo htmlspecialchars($imageName); ?>">
                                            <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $currentIndex; ?>" value="">
                                        
                                            <label for="image-slot-<?php echo $currentIndex; ?>">
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/services/body/<?php echo htmlspecialchars($imageName); ?>" class="image-preview-slot">
                                                    <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $currentIndex; ?>"><i class='bx bx-x'></i></button> 
                                                </div>
                                            </label>
                                            <input type="file" name="image_slot_<?php echo $currentIndex; ?>" id="image-slot-<?php echo $currentIndex; ?>" accept="image/*" style="display:none;">
                                        </div>
                                        <?php $currentIndex++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="image-slot" id="btn-add-client">
                                    <label>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Añadir Logo</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-services-thumb-box">
                        <?php if ($isExisting0): ?>
                            <img src="/assets/uploads/services/banner/<?php echo htmlspecialchars($imageName0); ?>" alt="Banner Servicios">
                        <?php else: ?>
                            <i class='bx bx-wrench'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Banner principal</span>
                        <strong id="summary-services-banner"><?php echo $isExisting0 ? 'Activo' : 'Sin imagen'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Tarjetas activas</span>
                        <strong id="summary-services-cards">0 / 3</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Video YouTube</span>
                        <strong id="summary-services-video">No</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Logos de clientes</span>
                        <strong id="summary-services-clients">0</strong>
                    </div>

                    <button type="button" id="btn-open-preview" class="lvr-btn-soft-blue" style="width: 100%; justify-content: center; height: 40px; margin-top: 0.25rem;">
                        <i class='bx bx-show'></i> Vista previa
                    </button>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar servicios
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
                        <h1 class="hero-section__title">Nuestros Servicios</h1>
                    </div>
                </section>

                <section class="section">
                    <div class="container content-section">
                        <h2 id="preview-title-0"></h2>
                        <p id="preview-text-0"></p>
                        <div style="display: flex; gap: 2rem; overflow-x: auto; padding: 20px 0; justify-content: center;" id="preview-clients-container"></div>
                    </div>
                </section>

                <section class="section">
                    <div class="container">
                        <div class="services-grid">
                            <div class="service-card">
                                <div class="service-card__icon"><i class='bx bx-chip'></i></div>
                                <h3 id="preview-title-1"></h3>
                                <p id="preview-text-1"></p>
                                <div class="service-card__image">
                                    <img id="preview-img-1" class="img-ratio-4-3" src="" style="display:none;">
                                </div>
                                <a href="#" class="btn btn-primary">Saber más</a>
                            </div>

                            <div class="service-card">
                                <div class="service-card__icon"><i class='bx bx-wrench'></i></div>
                                <h3 id="preview-title-2"></h3>
                                <p id="preview-text-2"></p>
                                <div class="service-card__image">
                                    <img id="preview-img-2" class="img-ratio-4-3" src="" style="display:none;">
                                </div>
                                <a href="#" class="btn btn-primary">Saber más</a>
                            </div>

                            <div class="service-card">
                                <div class="service-card__icon"><i class='bx bx-desktop'></i></div>
                                <h3 id="preview-title-3"></h3>
                                <p id="preview-text-3"></p>
                                <div class="service-card__image">
                                    <img id="preview-img-3" class="img-ratio-4-3" src="" style="display:none;">
                                </div>
                                <a href="#" class="btn btn-primary">Saber más</a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <div class="container content-section">
                        <h2 id="preview-title-4"></h2>
                        <p id="preview-text-4"></p>
                        <div class="video-wrapper" id="preview-video-container" style="display:none;">
                            <iframe id="preview-video-iframe" width="100%" height="450" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <script src="/assets/admin/js/admin-services-preview.js"></script>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSlot       = document.getElementById('services-banner-slot');
        const cardTitles       = document.querySelectorAll('.js-service-card-title');
        const videoLinkInput   = document.getElementById('s2_text2');
        const clientsContainer = document.getElementById('clients-container');

        function updateServicesSummary() {
            const hasBanner = bannerSlot && bannerSlot.classList.contains('has-preview');
            document.getElementById('summary-services-banner').textContent = hasBanner ? 'Activo' : 'Sin imagen';

            const thumbBox = document.getElementById('summary-services-thumb-box');
            if (thumbBox && bannerSlot) {
                const img = bannerSlot.querySelector('img.image-preview-slot');
                if (hasBanner && img && img.src) {
                    thumbBox.innerHTML = `<img src="${img.src}" alt="Banner Servicios">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bx-wrench'></i>`;
                }
            }

            let filledCards = 0;
            cardTitles.forEach(inp => { if (inp.value.trim() !== '') filledCards++; });
            document.getElementById('summary-services-cards').textContent = `${filledCards} / 3`;

            const hasVideo = videoLinkInput && videoLinkInput.value.trim() !== '';
            document.getElementById('summary-services-video').textContent = hasVideo ? 'Sí' : 'No';

            if (clientsContainer) {
                const clientLogos = clientsContainer.querySelectorAll('.image-slot.has-preview').length;
                document.getElementById('summary-services-clients').textContent = clientLogos;
            }
        }

        cardTitles.forEach(el => el.addEventListener('input', updateServicesSummary));
        if (videoLinkInput) videoLinkInput.addEventListener('input', updateServicesSummary);

        [bannerSlot, clientsContainer].forEach(container => {
            if (container) {
                const obs = new MutationObserver(() => setTimeout(updateServicesSummary, 80));
                obs.observe(container, { childList: true, subtree: true, attributes: true });
            }
        });

        updateServicesSummary();
    });
</script>