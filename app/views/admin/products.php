<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>

<section class="content-panel product-view-container view-list">    
    <?php if (!empty($products)): ?>
        <!-- =========================================================
             1. VISTA DE LISTA (ESTILO LOVABLE)
             ========================================================= -->
        <div class="product-list-container">
            <div class="product-list-ml">
                <?php foreach($products as $product): ?>
                    <?php 
                        $originalFile = !empty($product['file_name']) ? $product['file_name'] : 'default.jpg'; 
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
                                if (file_exists(ROOT_PATH . 'public/assets/uploads/products/' . $smallFile)) {
                                    $showWebp = true;
                                    $smallSrc = '/assets/uploads/products/' . rawurlencode($smallFile);
                                }
                            }
                        }

                        $isHidden = ($product['hidden'] != 0);
                        $hasOffer = ($product['offer_price'] !== null);
                        $isNew    = (!$hasOffer && $product['id_new_product'] !== null);
                    ?>
                    <div class="product-item-ml lvr-product-list-item <?php echo $isHidden ? 'is-hidden' : ''; ?>">
                        <!-- Columna 1: Miniatura + Nombre + SKU + Categoría/Material -->
                        <a href="/admin/products/edit/<?php echo $product['id_product']; ?>" class="lvr-product-main-link">
                            <div class="product-item-ml__image lvr-product-thumb">
                                <picture>
                                    <?php if ($showWebp): ?>
                                        <source srcset="<?php echo htmlspecialchars($smallSrc); ?>" <?php echo !$isDrive ? 'type="image/webp"' : ''; ?>>
                                    <?php endif; ?>
                                    <img src="<?php echo htmlspecialchars($fullSrc); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name'] ?? 'Producto'); ?>" 
                                         loading="lazy"
                                         referrerpolicy="no-referrer">
                                </picture>
                            </div>
                            <div class="product-item-ml__info">
                                <div class="lvr-client-heading" title="<?php echo htmlspecialchars($product['name']); ?>">
                                    <span class="lvr-client-name-text"><?php echo htmlspecialchars(ucfirst(mb_strtolower($product['name']))); ?></span>
                                    <?php if ($isHidden): ?>
                                        <span class="lvr-hidden-tag">(Oculto)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="lvr-code-line">SKU: <?php echo htmlspecialchars($product['sku']); ?></div>
                                <div class="lvr-date-line">
                                    <?php echo htmlspecialchars($product['category_name']); ?> · <?php echo htmlspecialchars($product['brand_name']); ?>
                                </div>
                            </div>
                        </a>

                        <!-- Columna 2 y 3: Píldoras de Estado y Bloque de Precio -->
                        <div class="compact-row-details lvr-product-details-group">
                            <div class="product-item-ml__status lvr-product-badges">
                                <!-- Estado Stock -->
                                <div class="status-toggle-container">
                                    <button type="button" class="status-pill <?php echo ($product['stock'] > 0) ? 'active' : 'inactive'; ?> stock-toggle-btn" data-product-id="<?php echo $product['id_product']; ?>">
                                        <i class='bx <?php echo ($product['stock'] > 0) ? 'bx-check-circle' : 'bx-x-circle'; ?>'></i>
                                        <span><?php echo ($product['stock'] > 0) ? 'Activo' : 'Agotado'; ?></span>
                                    </button>
                                    <div class="status-dropdown">
                                        <a href="#" class="stock-option" data-stock-value="1"><i class='bx bx-check-circle'></i> Disponible</a>
                                        <a href="#" class="stock-option" data-stock-value="0"><i class='bx bx-x-circle'></i> Agotado</a>
                                    </div>
                                </div>

                                <!-- Tipo (Oferta / Nuevo / Ninguno) -->
                                <div class="type-toggle-container">
                                    <button type="button" class="status-pill <?php echo $hasOffer ? 'offer' : ($isNew ? 'new' : 'none'); ?> type-toggle-btn" data-product-id="<?php echo $product['id_product']; ?>">
                                        <i class='bx <?php echo $hasOffer ? 'bx-purchase-tag-alt' : ($isNew ? 'bx-star' : 'bx-minus-circle'); ?>'></i>
                                        <span><?php echo $hasOffer ? 'Oferta' : ($isNew ? 'Nuevo' : 'Ninguno'); ?></span>
                                    </button>
                                    <div class="type-dropdown">
                                        <a href="#" class="type-option" data-type="offer"><i class='bx bx-purchase-tag-alt'></i> Oferta</a>
                                        <a href="#" class="type-option" data-type="new"><i class='bx bx-star'></i> Nuevo</a>
                                        <a href="#" class="type-option" data-type="none"><i class='bx bx-minus-circle'></i> Ninguno</a>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="/admin/products/edit/<?php echo $product['id_product']; ?>" class="lvr-product-price-link">
                                <div class="product-item-ml__pricing lvr-pricing-box">
                                    <div class="pricing__offer-stock">
                                        <?php if ($hasOffer): ?>
                                            <span class="pricing__offer"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['offer_price'], 2)); ?></span>
                                        <?php endif; ?>
                                        <span class="pricing__original <?php echo $hasOffer ? 'has-offer' : ''; ?>">
                                            <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?>
                                        </span>
                                    </div>
                                    <span class="pricing__stock">
                                        <i class='bx bx-package'></i> <?php echo htmlspecialchars($product['stock']); ?> unidades
                                    </span>
                                </div>
                            </a>
                        </div>

                        <!-- Columna 4: Menú 3 puntos -->
                        <div class="product-item-ml__actions">
                            <div class="actions-menu-container">
                                <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                                <div class="actions-dropdown">
                                    <a href="/admin/products/edit/<?php echo $product['id_product']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                    <a href="/producto/<?php echo $product['id_product']; ?>" target="_blank"><i class='bx bx-link-external'></i> Ver publicación</a>
                                    <a href="#" 
                                        class="btn-visibility-toggle" 
                                        data-id="<?php echo $product['id_product']; ?>" 
                                        data-current-hidden="<?php echo $product['hidden']; ?>">
                                        <i class='bx <?php echo ($product['hidden'] == 1) ? 'bx-show' : 'bx-hide'; ?>'></i>
                                        <?php echo ($product['hidden'] == 1) ? 'Mostrar en web' : 'Ocultar de web'; ?>
                                    </a>

                                    <?php if ($active_menu === 'products'): ?>
                                        <a href="/admin/<?php echo $active_menu; ?>/delete/<?php echo $product['id_product']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                                    <?php elseif (in_array($active_menu, ['offers', 'new-products'])): ?>
                                        <a href="/admin/<?php echo $active_menu; ?>/delete/<?php echo $product['id_product']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar <?php echo htmlspecialchars($buttonAgg); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- =========================================================
             2. VISTA DE CUADRÍCULA (4 POR FILA EN PC, 2 EN TABLET, 1 EN MÓVIL)
             ========================================================= -->
        <div class="product-grid-container">
            <?php foreach($products as $product): ?>
                <?php 
                    $originalFile = !empty($product['file_name']) ? $product['file_name'] : 'default.jpg'; 
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
                            if (file_exists(ROOT_PATH . 'public/assets/uploads/products/' . $smallFile)) {
                                $showWebp = true;
                                $smallSrc = '/assets/uploads/products/' . rawurlencode($smallFile);
                            }
                        }
                    }

                    $isHidden = ($product['hidden'] != 0);
                    $hasOffer = ($product['offer_price'] !== null);
                    $isNew    = (!$hasOffer && $product['id_new_product'] !== null);
                ?>
                <div class="grid-item-card lvr-product-card <?php echo $isHidden ? 'is-hidden' : ''; ?>">
                    <!-- Menú de 3 puntos arriba a la derecha -->
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/products/edit/<?php echo $product['id_product']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/producto/<?php echo $product['id_product']; ?>" target="_blank"><i class='bx bx-link-external'></i> Ver publicación</a>
                                <a href="#" 
                                    class="btn-visibility-toggle" 
                                    data-id="<?php echo $product['id_product']; ?>" 
                                    data-current-hidden="<?php echo $product['hidden']; ?>">
                                    <i class='bx <?php echo ($product['hidden'] == 1) ? 'bx-show' : 'bx-hide'; ?>'></i>
                                    <?php echo ($product['hidden'] == 1) ? 'Mostrar en web' : 'Ocultar de web'; ?>
                                </a>

                                <?php if ($active_menu === 'products'): ?>
                                    <a href="/admin/<?php echo $active_menu; ?>/delete/<?php echo $product['id_product']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                                <?php elseif (in_array($active_menu, ['offers', 'new-products'])): ?>
                                    <a href="/admin/<?php echo $active_menu; ?>/delete/<?php echo $product['id_product']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar <?php echo htmlspecialchars($buttonAgg); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Imagen del Producto arriba del título -->
                    <a href="/admin/products/edit/<?php echo $product['id_product']; ?>" style="text-decoration: none;">
                        <div class="grid-item-card__image lvr-product-grid-image">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="<?php echo htmlspecialchars($smallSrc); ?>" <?php echo !$isDrive ? 'type="image/webp"' : ''; ?>>
                                <?php endif; ?>
                                <img src="<?php echo htmlspecialchars($fullSrc); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name'] ?? 'Producto'); ?>" 
                                     loading="lazy"
                                     referrerpolicy="no-referrer">
                            </picture>
                        </div>
                    </a>

                    <!-- Contenido de la tarjeta -->
                    <div class="grid-item-card__content">
                        <a href="/admin/products/edit/<?php echo $product['id_product']; ?>" style="text-decoration: none;">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($product['name']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars(ucfirst(mb_strtolower($product['name']))); ?></span>
                                <?php if ($isHidden): ?>
                                    <span class="lvr-hidden-tag">(Oculto)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line">SKU: <?php echo htmlspecialchars($product['sku']); ?></div>
                            <div class="lvr-date-line">
                                <?php echo htmlspecialchars($product['category_name']); ?> · <?php echo htmlspecialchars($product['brand_name']); ?>
                            </div>
                        </a>

                        <!-- Píldoras de Estado (Stock y Tipo) -->
                        <div class="product-item-ml__status2 lvr-card-badges" style="margin-top: 0.65rem; margin-bottom: 0.4rem;">
                            <div class="status-toggle-container">
                                <button type="button" class="status-pill <?php echo ($product['stock'] > 0) ? 'active' : 'inactive'; ?> stock-toggle-btn" data-product-id="<?php echo $product['id_product']; ?>">
                                    <i class='bx <?php echo ($product['stock'] > 0) ? 'bx-check-circle' : 'bx-x-circle'; ?>'></i>
                                    <span><?php echo ($product['stock'] > 0) ? 'Activo' : 'Agotado'; ?></span>
                                </button>
                                <div class="status-dropdown">
                                    <a href="#" class="stock-option" data-stock-value="1"><i class='bx bx-check-circle'></i> Disponible</a>
                                    <a href="#" class="stock-option" data-stock-value="0"><i class='bx bx-x-circle'></i> Agotado</a>
                                </div>
                            </div>

                            <div class="type-toggle-container">
                                <button type="button" class="status-pill <?php echo $hasOffer ? 'offer' : ($isNew ? 'new' : 'none'); ?> type-toggle-btn" data-product-id="<?php echo $product['id_product']; ?>">
                                    <i class='bx <?php echo $hasOffer ? 'bx-purchase-tag-alt' : ($isNew ? 'bx-star' : 'bx-minus-circle'); ?>'></i>
                                    <span><?php echo $hasOffer ? 'Oferta' : ($isNew ? 'Nuevo' : 'Ninguno'); ?></span>
                                </button>
                                <div class="type-dropdown">
                                    <a href="#" class="type-option" data-type="offer"><i class='bx bx-purchase-tag-alt'></i> Oferta</a>
                                    <a href="#" class="type-option" data-type="new"><i class='bx bx-star'></i> Nuevo</a>
                                    <a href="#" class="type-option" data-type="none"><i class='bx bx-minus-circle'></i> Ninguno</a>
                                </div>
                            </div>
                        </div>

                        <!-- Pie de Tarjeta: Precio y Stock -->
                        <a href="/admin/products/edit/<?php echo $product['id_product']; ?>" class="lvr-card-footer-link">
                            <div class="lvr-card-price-footer">
                                <div class="pricing__offer-stock">
                                    <?php if ($hasOffer): ?>
                                        <span class="pricing__offer"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['offer_price'], 2)); ?></span>
                                    <?php endif; ?>
                                    <span class="pricing__original <?php echo $hasOffer ? 'has-offer' : ''; ?>">
                                        <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?>
                                    </span>
                                </div>
                                <span class="pricing__stock">
                                    <i class='bx bx-package'></i> <?php echo htmlspecialchars($product['stock']); ?> und.
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-search-alt'></i>
            <h2>No se encontraron productos</h2>
            <p>Prueba a cambiar los filtros o a restablecer la búsqueda.</p>
        </div>
    <?php endif; ?>
</section>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>