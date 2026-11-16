<?php include ROOT_PATH . 'app/views/templates/header.php'; ?>

<main class="main-content lvr-product-page-wrapper" style="background-color: #f1f5f9; padding-bottom: 4rem;">
    <?php if ($product && $product['product_hidden']==0): ?>
        <div class="container" style="padding-top: 2rem;">
            
            <!-- Breadcrumb (Migas de pan) -->
            <div class="lvr-breadcrumb-detail">
                <a href="/">Inicio</a> <i class='bx bx-chevron-right'></i> 
                <a href="/productos">Productos</a> <i class='bx bx-chevron-right'></i> 
                <!-- NUEVO: Enlace dinámico a la categoría -->
                <?php if (!empty($product['category'])): ?>
                    <a href="/productos?filter_choice=cat-<?php echo htmlspecialchars($product['category']); ?>"><?php echo htmlspecialchars($product['category_name'] ?? 'Categoría'); ?></a> <i class='bx bx-chevron-right'></i> 
                <?php else: ?>
                    <a href="#"><?php echo htmlspecialchars($product['category_name'] ?? 'Categoría'); ?></a> <i class='bx bx-chevron-right'></i> 
                <?php endif; ?>
                <span class="lvr-breadcrumb-title" title="<?php echo htmlspecialchars($product['name']); ?>"><?php echo htmlspecialchars($product['name']); ?></span>
            </div>

            <!-- Tarjeta 1: Layout Principal -->
            <div class="lvr-card-section product-detail-layout">

                <!-- Columna Izquierda: Galería -->
                <div class="product-gallery">
                    <?php 
                        $driveId = '';
                        if (!empty($product['video_url'])) {
                            if (preg_match('/[-\w]{25,}/', $product['video_url'], $match)) {
                                $driveId = $match[0];
                            }
                        }
                    ?>
                    <div class="gallery-thumbnails">
                        <?php foreach ($images as $index => $image): ?>
                            <?php 
                                $originalFile = !empty($image['file_name']) ? $image['file_name'] : 'default.jpg';
                                $isDrive = (strpos($originalFile, 'http://') === 0 || strpos($originalFile, 'https://') === 0);
                                $showWebp = false;

                                if ($isDrive) {
                                    $fullSrc  = $originalFile;
                                    $smallSrc = preg_replace('/=w\d+$/', '=w400', $originalFile);
                                    $showWebp = true;
                                } else {
                                    $fullSrc  = '/assets/uploads/products/' . rawurlencode($originalFile);
                                    $smallSrc = $fullSrc;
                                    if ($originalFile !== 'default.jpg') {
                                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                                        $origPath  = ROOT_PATH . 'public/assets/uploads/products/' . $originalFile;
                                        $smallPath = ROOT_PATH . 'public/assets/uploads/products/' . $smallFile;

                                        if (!file_exists($smallPath) && file_exists($origPath) && function_exists('imagewebp')) {
                                            $info = @getimagesize($origPath);
                                            if ($info) {
                                                list($w, $h, $type) = $info;
                                                $srcImg = null;
                                                if ($type == IMAGETYPE_JPEG) $srcImg = @imagecreatefromjpeg($origPath);
                                                elseif ($type == IMAGETYPE_PNG) $srcImg = @imagecreatefrompng($origPath);
                                                elseif ($type == IMAGETYPE_WEBP) $srcImg = @imagecreatefromwebp($origPath);
                                                if ($srcImg && $w > 0 && $h > 0) {
                                                    $newW = min($w, 500);
                                                    $newH = (int)round($h * ($newW / $w));
                                                    $dstImg = imagecreatetruecolor($newW, $newH);
                                                    imagealphablending($dstImg, false);
                                                    imagesavealpha($dstImg, true);
                                                    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $w, $h);
                                                    @imagewebp($dstImg, $smallPath, 82);
                                                    imagedestroy($srcImg);
                                                    imagedestroy($dstImg);
                                                }
                                            }
                                        }

                                        if (file_exists($smallPath)) {
                                            $showWebp = true;
                                            $smallSrc = '/assets/uploads/products/' . rawurlencode($smallFile);
                                        }
                                    }
                                }
                            ?>
                            <div class="thumbnail-item <?php echo ($index === 0) ? 'is-active' : ''; ?>" 
                                 data-type="image"
                                 data-fullsrc="<?php echo htmlspecialchars($fullSrc); ?>"
                                 data-smallsrc="<?php echo htmlspecialchars($smallSrc); ?>">
                                <picture>
                                    <?php if ($showWebp): ?>
                                        <source srcset="<?php echo htmlspecialchars($smallSrc); ?>" <?php echo !$isDrive ? 'type="image/webp"' : ''; ?>>
                                    <?php endif; ?>
                                    <img src="<?php echo htmlspecialchars($fullSrc); ?>" 
                                         alt="imagen <?php echo $index + 1; ?>" 
                                         loading="eager" 
                                         decoding="async"
                                         referrerpolicy="no-referrer">
                                </picture>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($driveId): ?>
                            <div class="thumbnail-item video-thumbnail" data-type="video" data-video-id="<?php echo $driveId; ?>" style="position: relative; display: flex; align-items: center; justify-content: center; background: #000;">
                                <i class='bx bx-play-circle' style="color: #fff; font-size: 2.2rem; position: absolute; z-index: 2;"></i>
                                <img src="https://drive.google.com/thumbnail?id=<?php echo $driveId; ?>&sz=w200" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.5;" alt="Video del Producto" referrerpolicy="no-referrer">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="gallery-main-image" style="position: relative;">
                        <div class="lvr-gallery-badges">
                            <?php if (!empty($product['is_new'])): ?>
                                <span class="lvr-badge-new">NUEVO</span>
                            <?php endif; ?>
                            <?php if (!empty($product['offer_price']) && $product['offer_price'] < $product['original_price']): ?>
                                <span class="lvr-badge-offer">OFERTA</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($product['certification']) && $product['certification'] === 'premium'): ?>
                            <img src="/assets/uploads/logo/premium-seal.png" class="premium-seal" alt="Certificado Premium">
                        <?php endif; ?>
                        
                        <?php
                            $mainRawFile = $images[0]['file_name'] ?? 'default.png';
                            $mainImgSrc  = (strpos($mainRawFile, 'http://') === 0 || strpos($mainRawFile, 'https://') === 0)
                                ? $mainRawFile
                                : '/assets/uploads/products/' . rawurlencode($mainRawFile);
                        ?>
                        <img id="main-product-image" src="<?php echo htmlspecialchars($mainImgSrc); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="eager" fetchpriority="high" referrerpolicy="no-referrer">
                    
                        <?php if ($driveId): ?>
                            <div id="main-product-video" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #000; overflow: hidden; border-radius: 12px;">
                                <iframe id="video-iframe" src="" allow="autoplay; fullscreen" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;"></iframe>
                                <div style="position: absolute; top: 0; right: 0; width: 65px; height: 65px; z-index: 10; background: transparent; cursor: default;" title="Ventana externa deshabilitada"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna Derecha: Información -->
                <div class="product-info">
                    <div class="lvr-info-header">
                        <span class="lvr-category-brand"><?php echo mb_strtoupper($product['category_name'] ?? 'CATEGORÍA') . ' · CERTIFICADO'; ?></span>
                    </div>
                    
                    <h1 class="product-info__title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="lvr-sku-text">SKU: <?php echo htmlspecialchars($product['sku']); ?></p>

                    <?php if ($showPrice=='1'): ?>
                        <?php 
                            $hasDollar = (strpos($moneySymbol, '$') !== false);
                        ?>
                        <div class="product-info__price">
                            <?php if ($product['offer_price'] !== null && $product['offer_price'] < $product['original_price']): ?>
                                <div class="offer-line">
                                    <span class="offer-price">
                                        <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['offer_price'], 2)); ?>
                                        <?php if($hasDollar): ?><small>USD</small><?php endif; ?>
                                    </span>
                                    <span class="original-price"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="offer-price">
                                    <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?>
                                    <?php if($hasDollar): ?><small>USD</small><?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($showStock): ?>
                        <div class="lvr-stock-wrapper">
                            <div class="lvr-stock-pill <?php echo ($product['stock'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                                <span class="dot"></span> 
                                <?php if ($product['stock'] > 0): ?>
                                    Stock disponible · <?php echo $product['stock']; ?> unidades
                                <?php else: ?>
                                    Agotado
                                <?php endif; ?>
                            </div>
                            <?php if ($product['stock'] <= 0): ?>
                                <span class="lvr-out-of-stock-note">Puedes solicitarlo bajo pedido</span>
                            <?php endif; ?>
                        </div>

                        <div class="lvr-quick-features">
                            <div class="lvr-qf-box">
                                <i class='bx bx-check-shield'></i>
                                <div>
                                    <span>Garantía</span>
                                    <strong><?php echo !empty($product['warranty']) ? htmlspecialchars($product['warranty']) : '30 días'; ?></strong>
                                </div>
                            </div>
                            <div class="lvr-qf-box">
                                <i class='bx bx-package'></i>
                                <div>
                                    <span>Condición</span>
                                    <strong>Nuevo</strong>
                                </div>
                            </div>
                        </div>
                    
                        <?php if ($product['stock'] > 0): ?>
                            <div class="lvr-action-section">
                                <label class="lvr-qty-label">Cantidad</label>
                                <div class="lvr-action-row">
                                    <div class="lvr-qty-selector">
                                        <button type="button" class="lvr-qty-btn minus"><i class='bx bx-minus'></i></button>
                                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                        <button type="button" class="lvr-qty-btn plus"><i class='bx bx-plus'></i></button>
                                    </div>
                                    
                                    <?php 
                                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                                        $currentUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                                        $waPhone = htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));
                                        
                                        $baseMsg = 'Hola, quiero comprar *' . $product['name'] . '*. Cantidad: ';
                                        $urlMsg = '. Aquí está el enlace: ' . $currentUrl;
                                    ?>
                                    <button class="btn btn-primary btn-buy-now lvr-btn-whatsapp">
                                        <a id="wa-dynamic-link" 
                                           data-phone="<?php echo $waPhone; ?>" 
                                           data-basemsg="<?php echo htmlspecialchars($baseMsg); ?>" 
                                           data-urlmsg="<?php echo htmlspecialchars($urlMsg); ?>" 
                                           href="https://wa.me/<?php echo $waPhone;?>?text=<?php echo urlencode($baseMsg . '1' . $urlMsg); ?>" 
                                           target="_blank" title="WhatsApp">
                                            <i class='bx bxl-whatsapp'></i> Comprar por WhatsApp
                                        </a>
                                    </button>
                                </div>
                                <p class="lvr-response-time"><i class='bx bx-time-five'></i> Respuesta promedio en menos de 10 minutos</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="lvr-benefits-box">
                        <ul>
                            <li><i class='bx bx-package'></i> Envíos a todo el país</li>
                            <li><i class='bx bx-check-shield'></i> Compra segura y producto verificado</li>
                            <li><i class='bx bx-headphone'></i> Asesoría técnica especializada</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2: Pestañas de Características y Más información -->
            <?php 
                $has_desc = !empty($product['description']);
                $has_ext_desc = !empty($product['ext_description']);
                // Se muestra características si tiene descripción o si ambas están vacías (como fallback)
                $show_features = $has_desc || !$has_ext_desc; 
            ?>
            <div class="lvr-card-section lvr-tabs-section">
                <div class="lvr-tabs-header">
                    <?php if ($show_features): ?>
                        <button class="lvr-tab-btn active" data-target="tab-features">Características</button>
                    <?php endif; ?>
                    
                    <?php if ($has_ext_desc): ?>
                        <button class="lvr-tab-btn <?php echo (!$show_features) ? 'active' : ''; ?>" data-target="tab-more-info">Más información</button>
                    <?php endif; ?>
                </div>
                
                <div class="lvr-tabs-content">
                    <?php if ($show_features): ?>
                        <div class="lvr-tab-pane active" id="tab-features">
                            <?php if ($has_desc): ?>
                                <div class="lvr-features-container" style="text-align: left;">
                                    <?php
                                    $features = explode("\n", str_replace("\r\n", "\n", $product['description']));
                                    foreach ($features as $feature):
                                        $feature = trim($feature);
                                        if (empty($feature)) continue;
                                    ?>
                                        <p class="lvr-feature-text" style="text-align: left; margin: 0 0 0.65rem 0;"><?php echo htmlspecialchars($feature); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="color: #64748b; font-size: 0.95rem; text-align: left;">No hay características para mostrar.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($has_ext_desc): ?>
                    <div class="lvr-tab-pane <?php echo (!$show_features) ? 'active' : ''; ?>" id="tab-more-info">
                        <div class="product-extended-description">
                            <?php echo nl2br(htmlspecialchars($product['ext_description'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="product-gallery-modal" class="modal">
                <span class="modal-close-btn">&times;</span>
                <div class="modal-content">
                    <div class="zoom-canvas" id="zoom-canvas">
                        <img id="modal-current-image" src="" alt="Imagen de la galería" referrerpolicy="no-referrer">
                    </div>
                    <a class="modal-prev-btn">&#10094;</a>
                    <a class="modal-next-btn">&#10095;</a>
                </div>
                <div class="modal-thumbnails"></div>
            </div>

        </div>
    <?php else: ?>
        <div class="no-results-message" style="text-align: center; padding: 4rem 1rem;">
            <i class='bx bx-search-alt'></i>
            <h2>No se encontraron productos</h2>
            <p>Prueba a cambiar los filtros o a restablecer la búsqueda.</p>
        </div>
    <?php endif; ?>
</main>

<script src="/assets/js/product-detail.js"></script>
<?php include ROOT_PATH . 'app/views/templates/footer.php'; ?>