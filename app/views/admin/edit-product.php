<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php
// --- 1. LÓGICA PARA RECONSTRUIR LA GARANTÍA ---
$warrantyDB = $product['warranty'] ?? '';
$hasWarrantyVal = (!empty($warrantyDB)) ? 'yes' : 'no';
$wLength = '';
$wUnitVal = 'days';

if ($hasWarrantyVal === 'yes') {
    $parts = explode(' ', $warrantyDB);
    $wLength = $parts[0] ?? '';
    $dbUnit = $parts[1] ?? '';

    $reverseUnitMap = [
        'días' => 'days',
        'meses' => 'months',
        'años' => 'years'
    ];
    $wUnitVal = $reverseUnitMap[$dbUnit] ?? 'days';
}

// --- 2. NORMALIZAR CONDICIÓN (En BD se guarda como 'Nuevo', 'Usado', 'Reacondicionado') ---
$rawCondDB = trim($product['condition'] ?? 'Nuevo');
$condReverseMap = [
    'Nuevo' => 'new', 'new' => 'new',
    'Usado' => 'used', 'used' => 'used',
    'Reacondicionado' => 'refurbished', 'refurbished' => 'refurbished'
];
$currentConditionVal = $condReverseMap[$rawCondDB] ?? 'new';

// --- 3. NORMALIZAR CERTIFICACIÓN ---
$currentCertVal = $product['certification'] ?? 'standard';

// --- 4. RECONSTRUIR LÍNEAS DE CARACTERÍSTICAS PRINCIPALES ---
$rawDescription = trim($product['description'] ?? '');
$featureLines = $rawDescription !== '' ? preg_split('/\r\n|\r|\n/', $rawDescription) : [];
$featureLines = array_values(array_filter(array_map('trim', $featureLines), function($v) { return $v !== ''; }));

$currencySymbol = htmlspecialchars($moneySymbol ?? '$');
?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-product-page" method="POST" action="/admin/updateProduct" id="product-form" enctype="multipart/form-data">
        <input type="hidden" name="id_product" value="<?php echo $product['id_product']; ?>">
        <?php if (!empty($product['hidden'])): ?>
            <input type="hidden" name="hidden" value="1">
        <?php endif; ?>

        <!-- TOPBAR: Breadcrumb + Título + Badge SKU -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/products">Productos</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Editar</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/products" class="lvr-back-btn" title="Volver a Productos">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Editar Producto</h1>
                </div>
                <span class="lvr-draft-badge">SKU: <?php echo htmlspecialchars($product['sku']); ?></span>
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
                                <p>Identifica el producto y describe lo que ofrece</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <!-- SKU -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="sku"><i class='bx bx-barcode'></i> Código de identificación (SKU)</label>
                            <input type="text" id="sku" name="sku" class="lvr-input" required value="<?php echo htmlspecialchars($product['sku']); ?>" placeholder="Ej. CPU-A520-001" style="font-family: monospace !important;">
                        </div>

                        <!-- Nombre -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="name">Nombre</label>
                            <input type="text" id="name" name="name" class="lvr-input" required value="<?php echo htmlspecialchars($product['name']); ?>" placeholder="Ej. CPU Gamer A520">
                            <input type="hidden" id="old_product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>

                        <!-- Categoría -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="category">Categoría</label>
                            <select id="category" name="category" class="lvr-select" required>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id_category']; ?>" <?php echo ($category['id_category'] == $product['category']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst(mb_strtolower($category['name']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Marca -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="brand">Marca</label>
                            <select id="brand" name="brand" class="lvr-select" required>
                                <?php foreach($brands as $brand): ?>
                                    <option value="<?php echo $brand['id_brand']; ?>" <?php echo ($brand['id_brand'] == $product['brand']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst(mb_strtolower($brand['name']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Características principales -->
                        <div class="lvr-field-group lvr-field-full">
                            <label class="lvr-field-label">
                                <i class='bx bx-list-ul'></i> Características principales
                            </label>
                            <textarea id="description" name="description" class="lvr-textarea" rows="5" placeholder="Describe las características de tu producto..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Descripción extendida -->
                        <div class="lvr-field-group lvr-field-full">
                            <label class="lvr-field-label" for="ext_description"><i class='bx bx-file'></i> Descripción extendida</label>
                            <textarea id="ext_description" name="ext_description" class="lvr-textarea" rows="5" placeholder="Describe el producto en detalle: usos, especificaciones, contenido de la caja..."><?php echo htmlspecialchars($product['ext_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. INVENTARIO Y PRECIO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-cube'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Inventario y precio</h3>
                                <p>Define existencias y valor de venta</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <!-- Stock -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="stock">Stock</label>
                            <div class="lvr-stock-stepper">
                                <button type="button" class="lvr-stepper-btn minus" id="btn-stock-minus">−</button>
                                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" min="0" placeholder="0">
                                <button type="button" class="lvr-stepper-btn plus" id="btn-stock-plus">+</button>
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="price"><i class='bx bx-dollar'></i> Precio (<?php echo $currencySymbol; ?>)</label>
                            <div class="lvr-price-input-wrap">
                                <span class="lvr-currency-prefix"><?php echo $currencySymbol; ?></span>
                                <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['original_price']); ?>" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. CALIDAD Y GARANTÍA -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-check-shield'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Calidad y garantía</h3>
                                <p>Certificación, estado y respaldo del producto</p>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1.1rem;">
                        <!-- Nivel de certificación -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-award'></i> Nivel de certificación</label>
                            <select id="certification" name="certification" style="display:none;" >
                                <option value="standard" <?= ($currentCertVal === 'standard') ? 'selected' : '' ?>>Estándar</option>
                                <option value="premium" <?= ($currentCertVal === 'premium') ? 'selected' : '' ?>>Premium</option>
                            </select>
                            <div class="lvr-pill-selector-group" data-sync-select="certification">
                                <button type="button" class="lvr-pill-option <?= ($currentCertVal === 'standard') ? 'is-active' : '' ?>" data-value="standard">Estándar</button>
                                <button type="button" class="lvr-pill-option <?= ($currentCertVal === 'premium') ? 'is-active' : '' ?>" data-value="premium">Premium</button>
                            </div>
                        </div>

                        <!-- Condición del producto -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-sparkles'></i> Condición del producto</label>
                            <select id="condition" name="condition" style="display:none;" >
                                <option value="new" <?= ($currentConditionVal === 'new') ? 'selected' : '' ?>>Nuevo</option>
                                <option value="refurbished" <?= ($currentConditionVal === 'refurbished') ? 'selected' : '' ?>>Reacondicionado</option>
                                <option value="used" <?= ($currentConditionVal === 'used') ? 'selected' : '' ?>>Usado</option>
                            </select>
                            <div class="lvr-pill-selector-group" data-sync-select="condition">
                                <button type="button" class="lvr-pill-option <?= ($currentConditionVal === 'new') ? 'is-active' : '' ?>" data-value="new">Nuevo</button>
                                <button type="button" class="lvr-pill-option <?= ($currentConditionVal === 'refurbished') ? 'is-active' : '' ?>" data-value="refurbished">Reacondicionado</button>
                                <button type="button" class="lvr-pill-option <?= ($currentConditionVal === 'used') ? 'is-active' : '' ?>" data-value="used">Usado</button>
                            </div>
                        </div>

                        <!-- ¿Tiene garantía? -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label">¿Tiene garantía?</label>
                            <select id="has_warranty" name="has_warranty" style="display:none;">
                                <option value="no" <?= ($hasWarrantyVal === 'no') ? 'selected' : '' ?>>No</option>
                                <option value="yes" <?= ($hasWarrantyVal === 'yes') ? 'selected' : '' ?>>Sí</option>
                            </select>
                            <div class="lvr-warranty-split" data-sync-select="has_warranty">
                                <button type="button" class="lvr-pill-option <?= ($hasWarrantyVal === 'yes') ? 'is-active' : '' ?>" data-value="yes">Sí</button>
                                <button type="button" class="lvr-pill-option <?= ($hasWarrantyVal === 'no') ? 'is-active' : '' ?>" data-value="no">No</button>
                            </div>

                            <div id="warranty-details" style="display:<?php echo ($hasWarrantyVal === 'yes') ? 'block' : 'none'; ?>; margin-top: 0.65rem;">
                                <label class="lvr-field-label" for="warranty_length" style="margin-bottom: 6px !important;">Duración de la garantía:</label>
                                <div class="warranty-row" style="display:flex; gap:0.65rem; align-items:center; max-width: 390px;">
                                    <input type="number" id="warranty_length" name="warranty_length" class="lvr-input" min="0" value="<?php echo htmlspecialchars($wLength); ?>" placeholder="0" style="width: 110px !important;">
                                    <select id="warranty_unit" name="warranty_unit" class="lvr-select" style="flex: 1;">
                                        <option value="days" <?= ($wUnitVal === 'days') ? 'selected' : '' ?>>Días</option>
                                        <option value="months" <?= ($wUnitVal === 'months') ? 'selected' : '' ?>>Meses</option>
                                        <option value="years" <?= ($wUnitVal === 'years') ? 'selected' : '' ?>>Años</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. IMÁGENES DEL PRODUCTO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-image-add'></i>
                                <span class="lvr-step-num">4</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Imágenes del producto</h3>
                                <p>La primera imagen será la portada · Sube todas las que desees</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="cover_reference" id="cover_reference" value="<?php echo isset($images[0]) ? 'existing_'.$images[0]['id_image'] : ''; ?>">
                    <input type="hidden" name="image_order" id="image_order" value="">

                    <div class="image-manager">
                        <div class="image-slots-container dynamic-slots-container" id="product-dynamic-slots">
                            
                            <?php foreach ($images as $index => $img): ?>
                                <?php
                                    $rawImg = $img['file_name'] ?? '';
                                    $imgSrc = (strpos($rawImg, 'http://') === 0 || strpos($rawImg, 'https://') === 0)
                                        ? $rawImg
                                        : '/assets/uploads/products/' . $rawImg;
                                ?>
                                <div class="image-slot has-preview dynamic-slot product-slot <?php echo $index === 0 ? 'is-cover' : ''; ?>" draggable="true">
                                    <label style="width: 100%; height: 100%; display: block; margin: 0;">
                                        <div class="image-preview-frame">
                                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="image-preview" referrerpolicy="no-referrer">
                                            
                                            <button type="button" class="remove-preview-btn existing-image" data-image-id="<?php echo $img['id_image']; ?>">
                                                <i class='bx bx-x'></i>
                                            </button>
                                            
                                            <button type="button" class="btn-set-cover" title="Seleccionar como portada">
                                                <i class='bx bx-star'></i> Elegir Portada
                                            </button>
                                            <div class="cover-indicator">
                                                <i class='bx bxs-star'></i> Portada
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <div class="image-slot dynamic-slot product-slot">
                                <label style="width: 100%; height: 100%; display: block; margin: 0; cursor: pointer;">
                                    <div class="image-placeholder desktop-view">
                                        <i class='bx bx-image-add'></i>
                                    </div>
                                    <div class="image-placeholder mobile-view mobile-split">
                                        <div class="upload-btn gallery-btn" data-capture="" title="Subir de galería">
                                            <i class='bx bx-image-add'></i>
                                            <span class="btn-text">Galería</span>
                                        </div>
                                        <div class="upload-btn camera-btn" data-capture="environment" title="Tomar foto">
                                            <i class='bx bx-camera'></i>
                                            <span class="btn-text">Cámara</span>
                                        </div>
                                    </div>
                                    <input type="file" name="product_images[]" accept="image/*" multiple style="display:none;">
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- 5. VIDEO DEL PRODUCTO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-video'></i>
                                <span class="lvr-step-num">5</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Video del producto</h3>
                                <p>Opcional · Se subirá directo a Drive al guardar</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <!-- Columna Izquierda: Título + Selector / Preview de Video -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="video_title">Título del video</label>
                            <input type="text" id="video_title" name="video_title" class="lvr-input" value="<?php echo htmlspecialchars($product['video_title'] ?? ''); ?>" placeholder="Ej. Demostración del equipo">
                            
                            <div class="receipt-video-container">
                                <?php if (isset($product) && !empty($product['video_url'])): ?>
                                    <input type="hidden" name="old_video_url" id="old_video_url" value="<?php echo htmlspecialchars($product['video_url']); ?>">
                                <?php endif; ?>

                                <div class="add-video-card" style="<?php echo (isset($product) && !empty($product['video_url'])) ? 'display:none;' : 'display:block;'; ?>">
                                    <label class="lvr-video-upload-card">
                                        <div class="lvr-video-upload-icon">
                                            <i class='bx bx-cloud-upload'></i>
                                        </div>
                                        <div class="lvr-video-upload-texts">
                                            <strong>Seleccionar video</strong>
                                            <span>MP4, MOV · Se sube al guardar</span>
                                        </div>
                                        <input type="file" id="video_file" name="video_file" class="video-file-input" accept="video/*" style="display:none;">
                                    </label>
                                </div>

                                <div class="video-preview-container" style="<?php echo (isset($product) && !empty($product['video_url'])) ? 'display:block;' : 'display:none;'; ?> margin-top: 10px; position: relative; width: fit-content; text-align: center;">
                                    <div style="position: relative; display: inline-block;">
                                        <?php if(isset($product) && !empty($product['video_url'])): ?>
                                            <iframe src="<?php echo str_replace('/view', '/preview', htmlspecialchars($product['video_url'])); ?>" width="320" height="220" frameborder="0" allow="autoplay" allowfullscreen style="border-radius: 12px; background:#000; pointer-events: none;"></iframe>
                                            <div style="position: absolute; top:0; left:0; width:100%; height:100%; z-index: 10;"></div>
                                        <?php else: ?>
                                            <video src="" controls style="max-width: 100%; height: 200px; border-radius: 12px; background: #000;"></video>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-sm danger remove-video-btn" onclick="<?php echo (isset($product) && !empty($product['video_url'])) ? "deleteProductVideo({$product['id_product']}, '{$product['video_url']}')" : ''; ?>" style="position: absolute; top: -10px; right: -10px; border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 20;">
                                            <i class='bx bx-x'></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Derecha: Descripción del video -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="video_description">Descripción del video</label>
                            <textarea id="video_description" name="video_description" class="lvr-textarea" rows="4" placeholder="Breve descripción del video..."><?php echo htmlspecialchars($product['video_description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div style="margin-top: 1.15rem; padding-top: 0.85rem; border-top: 1px dashed #e2e8f0; display: flex; flex-wrap: wrap; gap: 1.25rem; font-size: 0.78rem; color: #64748b;">
                        <span><strong>Creado:</strong> <?php echo htmlspecialchars($product['created_at'] ?? 'No disponible'); ?></span>
                        <span><strong>Modificado:</strong> <?php echo htmlspecialchars($product['updated_at'] ?? 'No disponible'); ?></span>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-thumb-box">
                        <?php if (!empty($images[0]['file_name'])): ?>
                            <?php
                                $firstRaw = $images[0]['file_name'];
                                $firstSrc = (strpos($firstRaw, 'http://') === 0 || strpos($firstRaw, 'https://') === 0)
                                    ? $firstRaw
                                    : '/assets/uploads/products/' . $firstRaw;
                            ?>
                            <img src="<?php echo htmlspecialchars($firstSrc); ?>" alt="Portada" referrerpolicy="no-referrer">
                        <?php else: ?>
                            <i class='bx bx-image'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Producto</span>
                        <strong id="summary-prod-name"><?php echo htmlspecialchars($product['name']); ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>SKU</span>
                        <strong id="summary-prod-sku"><?php echo htmlspecialchars($product['sku']); ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Stock</span>
                        <strong id="summary-prod-stock"><?php echo (int)$product['stock']; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Condición</span>
                        <strong id="summary-prod-condition"><?php echo htmlspecialchars($rawCondDB); ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Imágenes</span>
                        <strong id="summary-prod-images"><?php echo count($images); ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Video</span>
                        <strong id="summary-prod-video"><?php echo !empty($product['video_url']) ? 'Sí' : 'No'; ?></strong>
                    </div>

                    <div class="lvr-summary-pill definitive">
                        <span>Precio</span>
                        <strong id="summary-prod-price"><?php echo $currencySymbol; ?> <?php echo number_format((float)$product['original_price'], 2); ?></strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Campos obligatorios</span>
                            <strong id="summary-req-count">3/3</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-req-fill" style="width: 100%; background-color: #16a34a;"></div>
                        </div>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar producto
                    </button>

                    <a href="/admin/products" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    const CURRENCY_SYMBOL = <?php echo json_encode($moneySymbol ?? '$'); ?>;

    // ============================================================================
    // INTERACCIÓN DE CARACTERÍSTICAS, STEPPER, PÍLDORAS Y PANEL DE RESUMEN
    // ============================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const skuInput       = document.getElementById('sku');
        const nameInput      = document.getElementById('name');
        const stockInput     = document.getElementById('stock');
        const priceInput     = document.getElementById('price');
        const condSelect     = document.getElementById('condition');
        const descTextarea   = document.getElementById('description');
        const featuresList   = document.getElementById('lvr-features-list');
        const addFeatureBtn  = document.getElementById('btn-add-feature');
        const slotsContainer = document.getElementById('product-dynamic-slots');

        // 1. Sincronizar lista dinámica de Características -> Textarea #description
        function syncFeaturesToTextarea() {
            if (!featuresList || !descTextarea) return;
            const rows = featuresList.querySelectorAll('.lvr-feature-item');
            const values = [];

            rows.forEach((row, index) => {
                const numBadge = row.querySelector('.lvr-feature-num');
                const input = row.querySelector('.lvr-feature-input');
                const rmBtn = row.querySelector('.lvr-feature-remove');

                if (numBadge) numBadge.textContent = index + 1;
                if (input) input.placeholder = `Característica ${index + 1}`;
                if (rmBtn) rmBtn.style.display = rows.length > 1 ? 'inline-flex' : 'none';

                if (input && input.value.trim() !== '') {
                    values.push(input.value.trim());
                }
            });

            descTextarea.value = values.join('\n');
            const counterEl = document.getElementById('features-count-label');
            if (counterEl) {
                counterEl.textContent = values.length === 1 ? '· 1 añadida' : `· ${values.length} añadidas`;
            }
        }

        function createFeatureRow(val = '') {
            const div = document.createElement('div');
            div.className = 'lvr-feature-item';
            div.innerHTML = `
                <span class="lvr-feature-num">1</span>
                <input type="text" class="lvr-input lvr-feature-input" value="${val.replace(/"/g, '"')}" placeholder="Característica">
                <button type="button" class="lvr-feature-remove" title="Quitar característica">
                    <i class='bx bx-x'></i>
                </button>
            `;
            return div;
        }

        if (addFeatureBtn && featuresList) {
            addFeatureBtn.addEventListener('click', function() {
                const newRow = createFeatureRow('');
                featuresList.appendChild(newRow);
                syncFeaturesToTextarea();
                newRow.querySelector('.lvr-feature-input')?.focus();
            });

            featuresList.addEventListener('input', syncFeaturesToTextarea);
            featuresList.addEventListener('click', function(e) {
                const rmBtn = e.target.closest('.lvr-feature-remove');
                if (rmBtn && featuresList.querySelectorAll('.lvr-feature-item').length > 1) {
                    rmBtn.closest('.lvr-feature-item').remove();
                    syncFeaturesToTextarea();
                }
            });
        }

        // 2. Stepper de Stock (- / +)
        const btnMinus = document.getElementById('btn-stock-minus');
        const btnPlus  = document.getElementById('btn-stock-plus');
        if (btnMinus && stockInput) {
            btnMinus.addEventListener('click', function() {
                const current = parseInt(stockInput.value, 10) || 0;
                stockInput.value = Math.max(0, current - 1);
                updateProductSummary();
            });
        }
        if (btnPlus && stockInput) {
            btnPlus.addEventListener('click', function() {
                const current = parseInt(stockInput.value, 10) || 0;
                stockInput.value = current + 1;
                updateProductSummary();
            });
        }

        // 3. Píldoras sincronizadas con sus <select> ocultos
        document.querySelectorAll('[data-sync-select]').forEach(group => {
            const selectId = group.getAttribute('data-sync-select');
            const selectEl = document.getElementById(selectId);
            if (!selectEl) return;

            group.querySelectorAll('.lvr-pill-option').forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-value');
                    group.querySelectorAll('.lvr-pill-option').forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');

                    selectEl.value = val;
                    selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                    updateProductSummary();
                });
            });
        });

        // 4. Sincronización en tiempo real del Panel Resumen
        window.updateProductSummary = function() {
            const nameVal  = nameInput ? nameInput.value.trim() : '';
            const skuVal   = skuInput ? skuInput.value.trim() : '';
            const stockVal = stockInput ? (parseInt(stockInput.value, 10) || 0) : 0;
            const priceVal = priceInput ? (parseFloat(priceInput.value) || 0) : 0;

            const condMap = { 'new': 'Nuevo', 'refurbished': 'Reacondicionado', 'used': 'Usado' };
            const condVal = (condSelect && condSelect.value && condMap[condSelect.value]) ? condMap[condSelect.value] : '—';

            document.getElementById('summary-prod-name').textContent      = nameVal !== '' ? nameVal : '—';
            document.getElementById('summary-prod-sku').textContent       = skuVal !== '' ? skuVal : '—';
            document.getElementById('summary-prod-stock').textContent     = stockVal;
            document.getElementById('summary-prod-condition').textContent = condVal;
            document.getElementById('summary-prod-price').textContent     = `${CURRENCY_SYMBOL}${priceVal.toFixed(2)}`;

            const previewSlots = slotsContainer ? slotsContainer.querySelectorAll('.image-slot.has-preview') : [];
            document.getElementById('summary-prod-images').textContent = previewSlots.length;

            const thumbBox = document.getElementById('summary-thumb-box');
            if (thumbBox) {
                const coverSlot = (slotsContainer && slotsContainer.querySelector('.image-slot.is-cover img')) 
                               || (slotsContainer && slotsContainer.querySelector('.image-slot.has-preview img'));
                if (coverSlot && coverSlot.src) {
                    thumbBox.innerHTML = `<img src="${coverSlot.src}" alt="Portada" referrerpolicy="no-referrer">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
                }
            }

            const videoInput = document.getElementById('video_file');
            const oldVideoInput = document.getElementById('old_video_url');
            const hasVideo = (videoInput && videoInput.files && videoInput.files.length > 0) 
                          || (oldVideoInput && oldVideoInput.value.trim() !== '');
            document.getElementById('summary-prod-video').textContent = hasVideo ? 'Sí' : 'No';

            let completedReq = 0;
            if (skuVal !== '') completedReq++;
            if (nameVal !== '') completedReq++;
            if (priceInput && priceInput.value.trim() !== '' && priceVal > 0) completedReq++;

            const reqCountEl = document.getElementById('summary-req-count');
            const reqFillEl  = document.getElementById('summary-req-fill');
            if (reqCountEl) reqCountEl.textContent = `${completedReq}/3`;
            if (reqFillEl) {
                reqFillEl.style.width = `${Math.round((completedReq / 3) * 100)}%`;
                reqFillEl.style.backgroundColor = completedReq === 3 ? '#16a34a' : '#2563eb';
            }
        };

        [skuInput, nameInput, stockInput, priceInput, condSelect].forEach(el => {
            if (el) {
                el.addEventListener('input', updateProductSummary);
                el.addEventListener('change', updateProductSummary);
            }
        });

        if (slotsContainer) {
            const obs = new MutationObserver(() => setTimeout(updateProductSummary, 80));
            obs.observe(slotsContainer, { childList: true, subtree: true, attributes: true });
        }

        syncFeaturesToTextarea();
        updateProductSummary();
    });

    // ============================================================================
    // LÓGICA DE VIDEO (GUARDADO DIRECTO A DRIVE Y ELIMINACIÓN)
    // ============================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form.edit-form');
        
        if (form) {
            form.addEventListener('submit', async function(e) {
                const videoInput = document.getElementById('video_file');
                const hasVideo = videoInput && videoInput.files && videoInput.files.length > 0;
                
                if (hasVideo) {
                    e.preventDefault(); 
                    
                    const formData = new FormData(form);
                    formData.delete('video_file'); 
                    
                    const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('.lvr-btn-save-note');
                    if(submitBtn) submitBtn.disabled = true;

                    Swal.fire({
                        title: 'Guardando Datos...',
                        text: 'Creando registro principal...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    try {
                        let response = await fetch(form.action, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json' },
                            body: formData
                        });
                        
                        let result = await response.json();
                        
                        if (result.success) {
                            Swal.close();
                            const productId = result.product_id;
                            const productName = document.getElementById('name').value;
                            
                            uploadVideoBackground(productId, productName, videoInput.files[0], function() {
                                window.location.href = '/admin/products';
                            });
                        } else {
                            Swal.fire('Error', result.message || 'Error al guardar los datos.', 'error');
                            if(submitBtn) submitBtn.disabled = false;
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Fallo al guardar el producto en la base de datos.', 'error');
                        if(submitBtn) submitBtn.disabled = false;
                    }
                }
            });
        }
    });

    async function uploadVideoBackground(productId, productName, file, onCompleteCallback) {
        const widget = document.createElement('div');
        widget.id = 'bg-upload-widget';
        widget.style.cssText = `
            position: fixed; bottom: 25px; right: 25px; width: 330px; background: #ffffff; 
            border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.22); border: 1px solid #e9ecef; 
            border-left: 6px solid #0d6efd; padding: 16px; z-index: 999999; font-family: 'Inter', sans-serif; transition: all 0.3s ease;
        `;
        widget.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 700; font-size: 0.85rem; color: #0d6efd; display: flex; align-items: center; gap: 6px;">
                    <i class='bx bx-cloud-upload bx-flashing' style='font-size: 1.25rem;'></i> Subiendo Video Directo...
                </span>
                <span id="video-progress-text" style="font-size: 0.8rem; font-weight: bold; color: #6c757d;">0%</span>
            </div>
            <div style="font-size: 0.9rem; font-weight: 700; color: #212529; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 8px;">
                ${productName}
            </div>
            <progress id="video-progress" value="0" max="100" style="width: 100%; height: 8px; border-radius: 4px;"></progress>
            <div id="video-status-text" style="font-size: 0.72rem; color: #6c757d; margin-top: 8px; font-style: italic;">
                Solicitando conexión segura a Google...
            </div>
        `;
        document.body.appendChild(widget);

        try {
            let finalFileName = 'vid_' + Date.now() + '_' + file.name.replace(/[^a-zA-Z0-9.\-_]/g, '');

            // FASE 1: Pedir la URL de subida a través de nuestro propio backend PHP (Cero CORS)
            const initForm = new FormData();
            initForm.append('action', 'init_upload');
            initForm.append('product_id', productId);
            initForm.append('product_name', productName);
            initForm.append('file_name', finalFileName);
            initForm.append('mime_type', file.type || 'video/mp4');

            const oldProductName = document.getElementById('old_product_name');
            if (oldProductName) initForm.append('old_product_name', oldProductName.value);

            const oldVideoUrl = document.getElementById('old_video_url');
            if (oldVideoUrl && oldVideoUrl.value) initForm.append('old_video_url', oldVideoUrl.value);

            let initRes = await fetch('/admin/uploadAsyncProductVideo', {
                method: 'POST',
                body: initForm
            });
            let initData = await initRes.json();

            if (!initData || !initData.resumeUrl) {
                throw new Error(initData?.message || "No se pudo obtener el permiso de subida de Drive.");
            }

            document.getElementById('video-status-text').innerText = 'Transfiriendo archivo directo a Drive...';

            // FASE 2: Subir el binario directo al enlace firmado de Google Drive
            await new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open("PUT", initData.resumeUrl, true);
                
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        let percent = Math.round((e.loaded / e.total) * 100);
                        document.getElementById('video-progress').value = percent;
                        document.getElementById('video-progress-text').innerText = percent + '%';
                    }
                };
                
                xhr.onload = async function() {
                    try {
                        if (xhr.status === 200 || xhr.status === 201 || xhr.status === 308) {
                            document.getElementById('video-status-text').innerText = 'Aplicando permisos y guardando enlace...';
                            
                            let fileResponse = JSON.parse(xhr.responseText);

                            // FASE 3: Finalizar permisos y guardar en BD vía nuestro backend PHP
                            const saveForm = new FormData();
                            saveForm.append('action', 'save_link');
                            saveForm.append('product_id', productId);
                            saveForm.append('file_id', fileResponse.id);

                            let saveDbRes = await fetch('/admin/uploadAsyncProductVideo', {
                                method: 'POST',
                                body: saveForm
                            });
                            let dbResult = await saveDbRes.json();

                            if (dbResult && dbResult.success) {
                                widget.style.borderLeftColor = '#198754';
                                widget.innerHTML = `<span style="color: #198754; font-weight: bold; font-size: 0.9rem;"><i class='bx bxs-check-circle'></i> ¡Video subido con éxito!</span>`;
                                resolve();
                            } else {
                                throw new Error(dbResult?.message || "Fallo al guardar enlace del video.");
                            }
                        } else {
                            throw new Error("La transferencia se interrumpió (HTTP " + xhr.status + ").");
                        }
                    } catch (e) {
                        reject(e);
                    }
                };
                
                xhr.onerror = function() { reject(new Error("Error de red durante la transferencia del video.")); };
                xhr.send(file);
            });

            setTimeout(() => { if (onCompleteCallback) onCompleteCallback(); }, 1500);

        } catch (err) {
            widget.style.borderLeftColor = '#dc3545';
            widget.innerHTML = `<div style="color:#dc3545; font-weight:bold; font-size:0.85rem;"><i class='bx bxs-error-circle'></i> Error:</div><div style="font-size:0.75rem; margin-top:4px;">${err.message}</div>`;
            setTimeout(() => { if (onCompleteCallback) onCompleteCallback(); }, 4000);
        }
    }

    window.deleteProductVideo = function(productId, videoUrl) {
        Swal.fire({
            title: '¿Eliminar Video?',
            text: "Esta acción borrará el video de Drive permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    let response = await fetch('/admin/deleteProductVideo', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ product_id: productId, video_url: videoUrl })
                    });
                    let data = await response.json();
                    if (data.success) {
                        Swal.fire('¡Eliminado!', 'El video ha sido borrado.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar.', 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Fallo de conexión.', 'error');
                }
            }
        });
    };

    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'video_file') {
            const file = e.target.files[0];
            if (!file) return;
            const container = e.target.closest('.receipt-video-container');
            const previewContainer = container.querySelector('.video-preview-container');
            const addCard = container.querySelector('.add-video-card');
            
            const iframeEl = previewContainer.querySelector('iframe');
            if (iframeEl) iframeEl.remove();

            let videoEl = previewContainer.querySelector('video');
            if (!videoEl) {
                videoEl = document.createElement('video');
                videoEl.controls = true;
                videoEl.style.cssText = 'max-width: 100%; height: 200px; border-radius: 12px; background: #000;';
                previewContainer.querySelector('div').insertBefore(videoEl, previewContainer.querySelector('div').firstChild);
            }
            
            videoEl.src = URL.createObjectURL(file);
            addCard.style.display = 'none';
            previewContainer.style.display = 'block';
            if (typeof window.updateProductSummary === 'function') window.updateProductSummary();
        }
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-video-btn');
        if (btn && !btn.hasAttribute('onclick')) {
            const container = btn.closest('.receipt-video-container');
            container.querySelector('#video_file').value = '';
            const videoEl = container.querySelector('video');
            if (videoEl) videoEl.src = '';
            container.querySelector('.video-preview-container').style.display = 'none';
            container.querySelector('.add-video-card').style.display = 'block';
            if (typeof window.updateProductSummary === 'function') window.updateProductSummary();
        }
    });
</script>