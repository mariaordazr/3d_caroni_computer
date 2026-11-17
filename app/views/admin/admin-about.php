<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $imgSlot = 0;
    $item0 = $bannerImage ?? null; 
    $imageName0 = $item0['content_value'] ?? null; 
    $isExisting0 = !empty($imageName0);
?>

<section class="content-panel">
    <form class="edit-form lvr-admin-page lvr-cms-page" method="POST" action="/admin/pages/about/update" id="about-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Nosotros</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Gestión de Página: Nosotros</h1>
                </div>
                <span class="lvr-draft-badge">QUIÉNES SOMOS</span>
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
                                <p>Gestiona la imagen panorámica del encabezado superior de Nosotros</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" style="justify-content: center;">
                            <div class="image-slot lvr-banner-wide-slot <?php echo $isExisting0 ? 'has-preview' : ''; ?>" id="about-banner-slot">
                                <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $imgSlot; ?>" value="<?php echo $isExisting0 ? htmlspecialchars($imageName0) : ''; ?>">
                                <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $imgSlot; ?>" value="">
                                <label for="image-slot-<?php echo $imgSlot; ?>">
                                    <?php if ($isExisting0): ?>
                                        <div class="image-preview-slot-frame">
                                            <img src="/assets/uploads/about-us/banner/<?php echo htmlspecialchars($imageName0); ?>" class="image-preview-slot">
                                            <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $imgSlot; ?>">
                                                <i class='bx bx-x'></i>
                                            </button> 
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Subir Banner Principal</span>
                                        </div>
                                    <?php endif; ?>
                                </label>
                                <input type="file" name="image_slot_<?php echo $imgSlot; ?>" id="image-slot-<?php echo $imgSlot; ?>" accept="image/*" style="display:none;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. SECCIÓN 1 (OBJETIVO) -->
                <?php 
                    $txtIdx = 0; 
                    $imgSlot = 1;
                    $imageName1 = $imageIds[$imgSlot - 1] ?? null; 
                    $isExisting1 = !empty($imageName1);
                ?>
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-bullseye'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección 1: Objetivo principal</h3>
                                <p>Presentación principal e imagen destacada de introducción</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-cms-media-split">
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-image'></i> Imagen</label>
                            <div class="image-manager">
                                <div class="image-slots-container">
                                    <div class="image-slot <?php echo $isExisting1 ? 'has-preview' : ''; ?> js-body-slot">
                                        <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $imgSlot; ?>" value="<?php echo $isExisting1 ? htmlspecialchars($imageName1) : ''; ?>">
                                        <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $imgSlot; ?>" value="">
                                    
                                        <label for="image-slot-<?php echo $imgSlot; ?>">
                                            <?php if ($isExisting1): ?>
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/about-us/body/<?php echo htmlspecialchars($imageName1); ?>" class="image-preview-slot">
                                                    <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $imgSlot; ?>"><i class='bx bx-x'></i></button> 
                                                </div>
                                            <?php else: ?>
                                                <div class="image-placeholder-slot">
                                                    <i class='bx bx-image-add'></i>
                                                    <span>Galería</span>
                                                </div>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" name="image_slot_<?php echo $imgSlot; ?>" id="image-slot-<?php echo $imgSlot; ?>" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <div class="lvr-field-group">
                                <label class="lvr-field-label">Título</label>
                                <input type="text" name="about_title_<?php echo $txtIdx; ?>" class="lvr-input form-control js-about-title" value="<?php echo htmlspecialchars($titleIds[$txtIdx] ?? ''); ?>" placeholder="Ej: Nuestro Objetivo">
                            </div>
                            <div class="lvr-field-group">
                                <label class="lvr-field-label">Texto descriptivo</label>
                                <textarea name="about_text_<?php echo $txtIdx; ?>" class="lvr-textarea form-control js-about-text" rows="4" placeholder="Escribe la descripción del objetivo..."><?php echo htmlspecialchars($textIds[$txtIdx] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $txtIdx++; ?>

                <!-- 3. PILARES CORPORATIVOS (MISIÓN, VISIÓN Y VALORES) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-compass'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección 2: Misión, Visión y Valores</h3>
                                <p>Configura las 3 tarjetas de pilares corporativos y sus respectivas imágenes</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <?php 
                            $pillars = [
                                ['label' => 'Misión',  'icon' => 'bx-rocket',  'slot' => 2],
                                ['label' => 'Visión',  'icon' => 'bx-show',    'slot' => 3],
                                ['label' => 'Valores', 'icon' => 'bx-diamond', 'slot' => 4],
                            ];
                            foreach ($pillars as $idxP => $p):
                                $imgSlot = $p['slot'];
                                $imageNameP = $imageIds[$imgSlot - 1] ?? null;
                                $isExistingP = !empty($imageNameP);
                        ?>
                            <div class="lvr-cms-subcard <?php echo $idxP === 2 ? 'lvr-field-full' : ''; ?>">
                                <div class="lvr-cms-subcard-header">
                                    <h4 class="lvr-cms-subcard-title"><i class='bx <?php echo $p['icon']; ?>'></i> <?php echo $p['label']; ?></h4>
                                    <span class="lvr-draft-badge" style="font-size: 0.72rem; padding: 2px 9px;">Pilar <?php echo $idxP + 1; ?></span>
                                </div>

                                <div class="lvr-cms-media-split">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Imagen</label>
                                        <div class="image-manager">
                                            <div class="image-slots-container">
                                                <div class="image-slot <?php echo $isExistingP ? 'has-preview' : ''; ?> js-body-slot">
                                                    <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $imgSlot; ?>" value="<?php echo $isExistingP ? htmlspecialchars($imageNameP) : ''; ?>">
                                                    <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $imgSlot; ?>" value="">
                                                    <label for="image-slot-<?php echo $imgSlot; ?>">
                                                        <?php if ($isExistingP): ?>
                                                            <div class="image-preview-slot-frame">
                                                                <img src="/assets/uploads/about-us/body/<?php echo htmlspecialchars($imageNameP); ?>" class="image-preview-slot">
                                                                <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $imgSlot; ?>"><i class='bx bx-x'></i></button> 
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="image-placeholder-slot">
                                                                <i class='bx bx-image-add'></i>
                                                                <span>Galería</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </label>
                                                    <input type="file" name="image_slot_<?php echo $imgSlot; ?>" id="image-slot-<?php echo $imgSlot; ?>" accept="image/*" style="display:none;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Título</label>
                                            <input type="text" name="about_title_<?php echo $txtIdx; ?>" class="lvr-input form-control js-about-title" value="<?php echo htmlspecialchars($titleIds[$txtIdx] ?? ''); ?>" placeholder="Título de <?php echo $p['label']; ?>">
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Texto</label>
                                            <textarea name="about_text_<?php echo $txtIdx; ?>" class="lvr-textarea form-control js-about-text" rows="3" placeholder="Descripción de <?php echo $p['label']; ?>..."><?php echo htmlspecialchars($textIds[$txtIdx] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                                $txtIdx++;
                            endforeach; 
                        ?>
                    </div>
                </div>

                <!-- 4. SECCIÓN 3 (EQUIPO) -->
                <?php 
                    $imgSlot = 5;
                    $imageName5 = $imageIds[$imgSlot - 1] ?? null; 
                    $isExisting5 = !empty($imageName5);
                ?>
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-group'></i>
                                <span class="lvr-step-num">4</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Sección 3: Nuestro Equipo</h3>
                                <p>Cierre de la página con información e imagen sobre el equipo de trabajo</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-cms-media-split">
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-image'></i> Imagen</label>
                            <div class="image-manager">
                                <div class="image-slots-container">
                                    <div class="image-slot <?php echo $isExisting5 ? 'has-preview' : ''; ?> js-body-slot">
                                        <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $imgSlot; ?>" value="<?php echo $isExisting5 ? htmlspecialchars($imageName5) : ''; ?>">
                                        <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $imgSlot; ?>" value="">
                                        <label for="image-slot-<?php echo $imgSlot; ?>">
                                            <?php if ($isExisting5): ?>
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/about-us/body/<?php echo htmlspecialchars($imageName5); ?>" class="image-preview-slot">
                                                    <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $imgSlot; ?>"><i class='bx bx-x'></i></button> 
                                                </div>
                                            <?php else: ?>
                                                <div class="image-placeholder-slot">
                                                    <i class='bx bx-image-add'></i>
                                                    <span>Galería</span>
                                                </div>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" name="image_slot_<?php echo $imgSlot; ?>" id="image-slot-<?php echo $imgSlot; ?>" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <div class="lvr-field-group">
                                <label class="lvr-field-label">Título</label>
                                <input type="text" name="about_title_<?php echo $txtIdx; ?>" class="lvr-input form-control js-about-title" value="<?php echo htmlspecialchars($titleIds[$txtIdx] ?? ''); ?>" placeholder="Ej: Nuestro Equipo">
                            </div>
                            <div class="lvr-field-group">
                                <label class="lvr-field-label">Texto descriptivo</label>
                                <textarea name="about_text_<?php echo $txtIdx; ?>" class="lvr-textarea form-control js-about-text" rows="4" placeholder="Escribe la descripción del equipo..."><?php echo htmlspecialchars($textIds[$txtIdx] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-about-thumb-box">
                        <?php if ($isExisting0): ?>
                            <img src="/assets/uploads/about-us/banner/<?php echo htmlspecialchars($imageName0); ?>" alt="Banner Nosotros">
                        <?php else: ?>
                            <i class='bx bx-image'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Banner principal</span>
                        <strong id="summary-about-banner"><?php echo $isExisting0 ? 'Activo' : 'Sin imagen'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Secciones con texto</span>
                        <strong id="summary-about-texts">0 / 5</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Imágenes de cuerpo</span>
                        <strong id="summary-about-images">0 / 5</strong>
                    </div>

                    <button type="button" id="btn-open-preview" class="lvr-btn-soft-blue" style="width: 100%; justify-content: center; height: 40px; margin-top: 0.25rem;">
                        <i class='bx bx-show'></i> Vista previa
                    </button>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar cambios
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
                        <h1 class="hero-section__title">Quiénes somos</h1>
                    </div>
                </section>

                <section class="section section--light">
                    <div class="container content-section">
                        <div class="info-grid">
                            <div class="info-grid__item about-text-content">
                                <h3 id="preview-title-0"></h3>
                                <p id="preview-text-0"></p>
                            </div>
                            <div class="info-grid__item">
                                <div class="about-image-wrapper">
                                    <img id="preview-img-1" src="" style="width:100%; border-radius:8px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section">
                    <div class="container">
                        <div class="about-values-grid">
                            <div class="about-card">
                                <h3 id="preview-title-1"></h3>
                                <p id="preview-text-1"></p>
                                <div class="about-card__image">
                                    <img id="preview-img-2" class="img-ratio-4-3" src="">
                                </div>
                            </div>

                            <div class="about-card">
                                <h3 id="preview-title-2"></h3>
                                <p id="preview-text-2"></p>
                                <div class="about-card__image">
                                    <img id="preview-img-3" class="img-ratio-4-3" src="">
                                </div>
                            </div>

                            <div class="about-card">
                                <h3 id="preview-title-3"></h3>
                                <p id="preview-text-3"></p>
                                <div class="about-card__image">
                                    <img id="preview-img-4" class="img-ratio-4-3" src="">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="section section--light">
                    <div class="container content-section">
                        <div class="info-grid">
                            <div class="info-grid__item">
                                <div class="about-image-wrapper">
                                    <img id="preview-img-5" src="" style="width:100%; border-radius:8px;">
                                </div>
                            </div>
                            <div class="info-grid__item about-text-content">
                                <h3 id="preview-title-4"></h3>
                                <p id="preview-text-4"></p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <script src="/assets/admin/js/admin-about-preview.js"></script>
<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSlot = document.getElementById('about-banner-slot');
        const titleInputs = document.querySelectorAll('.js-about-title');
        const textInputs  = document.querySelectorAll('.js-about-text');

        function updateAboutSummary() {
            const hasBanner = bannerSlot && bannerSlot.classList.contains('has-preview');
            document.getElementById('summary-about-banner').textContent = hasBanner ? 'Activo' : 'Sin imagen';

            const thumbBox = document.getElementById('summary-about-thumb-box');
            if (thumbBox && bannerSlot) {
                const img = bannerSlot.querySelector('img.image-preview-slot');
                if (hasBanner && img && img.src) {
                    thumbBox.innerHTML = `<img src="${img.src}" alt="Banner">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
                }
            }

            let filledSections = 0;
            for (let i = 0; i < titleInputs.length; i++) {
                if ((titleInputs[i]?.value.trim() !== '') || (textInputs[i]?.value.trim() !== '')) {
                    filledSections++;
                }
            }
            document.getElementById('summary-about-texts').textContent = `${filledSections} / 5`;

            const activeBodyImages = document.querySelectorAll('.js-body-slot.has-preview').length;
            document.getElementById('summary-about-images').textContent = `${activeBodyImages} / 5`;
        }

        [...titleInputs, ...textInputs].forEach(el => el.addEventListener('input', updateAboutSummary));

        document.querySelectorAll('.image-slot').forEach(slot => {
            const obs = new MutationObserver(() => setTimeout(updateAboutSummary, 80));
            obs.observe(slot, { childList: true, subtree: true, attributes: true });
        });

        updateAboutSummary();
    });
</script>