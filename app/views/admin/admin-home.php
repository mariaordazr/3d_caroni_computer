<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-cms-page" method="POST" action="/admin/pages/home/update" id="home-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Inicio</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Gestión de Página: Inicio</h1>
                </div>
                <span class="lvr-draft-badge">PORTADA WEB</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. SLIDER PRINCIPAL -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-slideshow'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Slider principal</h3>
                                <p>Gestiona hasta 5 imágenes panorámicas (4:1) para el carrusel de cabecera</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" id="home-slider-slots">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <?php 
                                    $item1 = $sliderImages[$i] ?? null; 
                                    $imageName1 = $item1['content_value'] ?? null; 
                                    $isExisting1 = !empty($imageName1);
                                ?>
                                <div class="image-slot <?php echo $isExisting1 ? 'has-preview' : ''; ?>">
                                    <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $i; ?>" value="<?php echo $isExisting1 ? htmlspecialchars($imageName1) : ''; ?>">
                                    <input type="hidden" class="delete-slot-flag" name="delete_slot_<?php echo $i; ?>" value="0">
                                
                                    <span class="slot-order-label"><?php echo $i + 1; ?></span>
                                    <label for="image-slot-<?php echo $i; ?>">
                                        <?php if ($isExisting1): ?>
                                            <div class="image-preview-slot-frame">
                                                <img src="/assets/uploads/home/slider/<?php echo htmlspecialchars($imageName1); ?>" class="image-preview-slot">
                                                <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $i; ?>">
                                                    <i class='bx bx-x'></i>
                                                </button> 
                                            </div>
                                        <?php else: ?>
                                            <div class="image-placeholder-slot">
                                                <i class='bx bx-image-add'></i>
                                                <span>Slide <?php echo $i + 1; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </label>
                                    <input type="file" name="image_slot_<?php echo $i; ?>" id="image-slot-<?php echo $i; ?>" accept="image/*" style="display:none;">
                                    <?php if ($isExisting1): ?>
                                        <input type="hidden" name="delete_image_<?php echo $i; ?>" value="">
                                    <?php endif; ?>
                                </div>
                                <?php if ($i < 4): ?>
                                    <button type="button" class="btn-swap-slot" aria-label="Intercambiar slots" title="Intercambiar posición">
                                        <i class='bx bx-transfer'></i>
                                    </button>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <!-- 2. CATEGORÍAS DESTACADAS -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-grid-alt'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Categorías destacadas</h3>
                                <p>Selecciona exactamente las 7 categorías que deseas mostrar en la página de inicio</p>
                            </div>
                        </div>
                        <span class="lvr-tag-amber" id="cat-counter-badge">0 / 7 seleccionadas</span>
                    </div>

                    <div class="form-grid-admin lvr-featured-pills-grid">
                        <?php 
                        if (!empty($categories)): 
                            foreach ($categories as $category): 
                                $categoryId = (string)($category['id_category']);
                                $isChecked = in_array($categoryId, $featuredCategoryIds);
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    name="featured_categories[]" 
                                    value="<?php echo htmlspecialchars($categoryId); ?>" 
                                    id="category-<?php echo htmlspecialchars($categoryId); ?>"
                                    <?php echo $isChecked ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="category-<?php echo htmlspecialchars($categoryId); ?>">
                                    <span><?php echo htmlspecialchars(ucfirst(mb_strtolower($category['name'], 'UTF-8'))); ?></span>
                                </label>
                            </div>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </div>

                    <div id="categories-error-message" class="alert alert-danger lvr-alert-warning-soft" style="display: none;">
                        <i class='bx bx-error-circle' style="font-size: 1.1rem;"></i>
                        <span><strong>¡Atención!</strong> Debes seleccionar exactamente 7 categorías destacadas para poder guardar.</span>
                    </div>
                </div>

                <!-- 3. MARCAS DESTACADAS -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-award'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Marcas destacadas</h3>
                                <p>Selecciona exactamente las 7 marcas que deseas destacar en la página de inicio</p>
                            </div>
                        </div>
                        <span class="lvr-tag-amber" id="brand-counter-badge">0 / 7 seleccionadas</span>
                    </div>

                    <div class="form-grid-admin lvr-featured-pills-grid">
                        <?php 
                        if (!empty($brands)): 
                            foreach ($brands as $brand): 
                                $brandId = (string)($brand['id_brand']);
                                $isChecked = in_array($brandId, $featuredBrandIds);
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    name="featured_brands[]" 
                                    value="<?php echo htmlspecialchars($brandId); ?>" 
                                    id="brand-<?php echo htmlspecialchars($brandId); ?>"
                                    <?php echo $isChecked ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="brand-<?php echo htmlspecialchars($brandId); ?>">
                                    <span><?php echo htmlspecialchars(ucfirst(mb_strtolower($brand['name'], 'UTF-8'))); ?></span>
                                </label>
                            </div>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </div>

                    <div id="brands-error-message" class="alert alert-danger lvr-alert-warning-soft" style="display: none;">
                        <i class='bx bx-error-circle' style="font-size: 1.1rem;"></i>
                        <span><strong>¡Atención!</strong> Debes seleccionar exactamente 7 marcas destacadas para poder guardar.</span>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-home-thumb-box">
                        <i class='bx bx-slideshow'></i>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Slides activos</span>
                        <strong id="summary-home-slides">0 / 5</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Categorías</span>
                        <strong id="summary-home-cats">0 / 7</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Marcas</span>
                        <strong id="summary-home-brands">0 / 7</strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Requisitos (Cat. y Marcas)</span>
                            <strong id="summary-home-req-count">0/2</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-home-req-fill"></div>
                        </div>
                    </div>

                    <button type="button" id="btn-open-preview" class="lvr-btn-soft-blue" style="width: 100%; justify-content: center; height: 40px;">
                        <i class='bx bx-show'></i> Vista previa
                    </button>

                    <!-- Conservamos la clase .btn-primary para que admin-home.js controle el disabled según la regla de 7 ítems -->
                    <button type="submit" class="lvr-btn-save-note btn-primary">
                        <i class='bx bx-check'></i> Guardar inicio
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
                <h3 style="color:#fff; margin:0; font-size: 1.1rem;">Vista Previa (No guardado)</h3>
                <div style="display:flex; gap:10px;">
                    <button type="button" id="btn-close-preview" class="btn" style="background:#fff; color:#0f172a; border-radius:999px; padding:6px 16px; font-weight:600;">Seguir Editando</button>
                    <button type="button" onclick="document.querySelector('.edit-form').submit();" class="btn btn-primary" style="background:#2563eb; color:#fff; border-radius:999px; padding:6px 18px; font-weight:600;">Guardar Cambios</button>
                </div>
            </div>

            <div class="preview-content-wrapper" style="background-color: #f8fafc; padding-bottom: 50px;">
                <section class="home-section">
                    <div class="home-slider swiper" style="aspect-ratio: 3.5/1; position: relative; overflow: hidden;">
                        <div class="swiper-wrapper" id="preview-slider-wrapper" style="display: flex;"></div>
                        <div class="swiper-button-next" style="position:absolute; right:10px; top:50%; z-index:10; color:white; font-size: 2rem; font-weight: bold; cursor: pointer;">></div>
                        <div class="swiper-button-prev" style="position:absolute; left:10px; top:50%; z-index:10; color:white; font-size: 2rem; font-weight: bold; cursor: pointer;"><</div>
                    </div>
                </section>
                
                <div class="container" style="text-align: center; margin: 2rem 0;">
                    <p style="color: #64748b; font-style: italic;">(Aquí se mostrarían las Categorías Destacadas y Productos Recientes tal cual están en la web)</p>
                </div>

                <section class="home-banner-section" style="margin: 3rem auto; max-width: 1200px; padding: 0 1rem;">
                    <h4 style="text-align: center; color: #64748b; margin-bottom: 1rem;">Vista Previa: Banner Secundario</h4>
                    <div style="width: 100%; aspect-ratio: 4/1; border-radius: 12px; overflow: hidden; background: #e2e8f0;">
                        <img id="preview-secondary-banner" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <div id="preview-banner-placeholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#64748b;">Sin imagen</div>
                    </div>
                </section>
            </div>
        </div>

    <script src="/assets/admin/js/admin-home-preview.js"></script>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderContainer = document.getElementById('home-slider-slots');
        const bannerSlot      = document.getElementById('secondary-banner-slot');
        const catChecks       = document.querySelectorAll('input[name="featured_categories[]"]');
        const brandChecks     = document.querySelectorAll('input[name="featured_brands[]"]');

        function updateHomeSummary() {
            // 1. Slides activos y miniatura principal en Resumen
            const sliderSlots = sliderContainer ? sliderContainer.querySelectorAll('.image-slot.has-preview') : [];
            const slidesEl = document.getElementById('summary-home-slides');
            if (slidesEl) slidesEl.textContent = `${sliderSlots.length} / 5`;

            const thumbBox = document.getElementById('summary-home-thumb-box');
            if (thumbBox) {
                const firstSlideImg = sliderContainer ? sliderContainer.querySelector('.image-slot.has-preview img') : null;
                if (firstSlideImg && firstSlideImg.src) {
                    thumbBox.innerHTML = `<img src="${firstSlideImg.src}" alt="Slide 1">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bx-slideshow'></i>`;
                }
            }

            // 2. Categorías y Marcas (7 requeridas)
            let checkedCats = 0;
            catChecks.forEach(c => { if (c.checked) checkedCats++; });
            let checkedBrands = 0;
            brandChecks.forEach(b => { if (b.checked) checkedBrands++; });

            const catsEl   = document.getElementById('summary-home-cats');
            const brandsEl = document.getElementById('summary-home-brands');
            const catBadge = document.getElementById('cat-counter-badge');
            const brdBadge = document.getElementById('brand-counter-badge');

            if (catsEl) catsEl.textContent = `${checkedCats} / 7`;
            if (brandsEl) brandsEl.textContent = `${checkedBrands} / 7`;
            if (catBadge) catBadge.textContent = `${checkedCats} / 7 seleccionadas`;
            if (brdBadge) brdBadge.textContent = `${checkedBrands} / 7 seleccionadas`;

            // 3. Banner secundario
            const hasBanner = bannerSlot && bannerSlot.classList.contains('has-preview');
            const bannerEl  = document.getElementById('summary-home-banner');
            if (bannerEl) bannerEl.textContent = hasBanner ? 'Sí' : 'No';

            // 4. Barra de progreso de requisitos (7 categorías + 7 marcas = 2/2)
            let reqDone = 0;
            if (checkedCats === 7) reqDone++;
            if (checkedBrands === 7) reqDone++;

            const reqCountEl = document.getElementById('summary-home-req-count');
            const reqFillEl  = document.getElementById('summary-home-req-fill');
            if (reqCountEl) reqCountEl.textContent = `${reqDone}/2`;
            if (reqFillEl) {
                reqFillEl.style.width = `${(reqDone / 2) * 100}%`;
                reqFillEl.style.backgroundColor = reqDone === 2 ? '#16a34a' : '#2563eb';
            }
        }

        catChecks.forEach(c => c.addEventListener('change', updateHomeSummary));
        brandChecks.forEach(b => b.addEventListener('change', updateHomeSummary));

        [sliderContainer, bannerSlot].forEach(el => {
            if (el) {
                const obs = new MutationObserver(() => setTimeout(updateHomeSummary, 80));
                obs.observe(el, { childList: true, subtree: true, attributes: true });
            }
        });

        updateHomeSummary();
    });
</script>