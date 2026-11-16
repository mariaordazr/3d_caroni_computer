<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>

<section class="content-panel product-view-container view-list"> 
    <?php if (!empty($categories)): ?>
        <!-- 1. VISTA DE LISTA -->
        <div class="table-list-ml lvr-entity-list">
            <?php foreach ($categories as $category): ?>
                <?php 
                    $originalFile = !empty($category['image']) ? $category['image'] : 'default.jpg';
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/categories/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $isHidden = (!empty($category['hidden']) && $category['hidden'] == 1);
                ?>
                <div class="table-item-ml product-item-ml lvr-product-list-item lvr-entity-row <?php echo $isHidden ? 'is-hidden' : ''; ?>">
                    <a href="/admin/products?search=&sort=created_at-desc&category=<?php echo $category['id_category']; ?>&brand=&status=&type=" class="lvr-product-main-link">
                        <div class="product-item-ml__image lvr-product-thumb">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/categories/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/categories/<?php echo rawurlencode($originalFile); ?>"
                                     alt="<?php echo htmlspecialchars($category['name']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                        <div class="product-item-ml__info">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($category['name']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($category['name']); ?></span>
                                <?php if ($isHidden): ?>
                                    <span class="lvr-hidden-tag">(Oculto)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line">ID: #<?php echo htmlspecialchars($category['id_category']); ?></div>
                            <div class="lvr-date-line">
                                CREADO: <?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?>
                                <?php if (!empty($category['updated_at']) && $category['created_at'] !== $category['updated_at']): ?>
                                     · MODIFICADO: <?php echo date('d/m/Y H:i', strtotime($category['updated_at'])); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>

                    <div class="compact-row-details lvr-product-details-group">
                        <div class="product-item-ml__status lvr-product-badges">
                            <span class="status-pill <?php echo $isHidden ? 'inactive' : 'active'; ?>" style="cursor: default; justify-content: center;">
                                <i class='bx <?php echo $isHidden ? 'bx-hide' : 'bx-show'; ?>'></i>
                                <span><?php echo $isHidden ? 'Oculta' : 'Visible'; ?></span>
                            </span>
                        </div>
                    </div>

                    <div class="table-item-ml__actions product-item-ml__actions">
                        <div class="actions-menu-container">
                            <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/categories/edit/<?php echo $category['id_category']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/admin/products?search=&sort=created_at-desc&category=<?php echo $category['id_category']; ?>&brand=&status=&type="><i class='bx bx-package'></i> Ver productos</a>
                                <a href="#" 
                                   class="btn-visibility-toggle" 
                                   data-id="<?php echo $category['id_category']; ?>" 
                                   data-current-hidden="<?php echo $category['hidden']; ?>">
                                    <i class='bx <?php echo $isHidden ? 'bx-show' : 'bx-hide'; ?>'></i>
                                    <?php echo $isHidden ? 'Mostrar en web' : 'Ocultar de web'; ?>
                                </a>
                                <a href="/admin/categories/delete/<?php echo $category['id_category']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 2. VISTA DE CUADRÍCULA -->
        <div class="generic-grid-container product-grid-container lvr-entity-grid">
            <?php foreach ($categories as $category): ?>
                <?php 
                    $originalFile = !empty($category['image']) ? $category['image'] : 'default.jpg';
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/categories/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $isHidden = (!empty($category['hidden']) && $category['hidden'] == 1);
                ?>
                <div class="grid-item-card lvr-product-card lvr-entity-card <?php echo $isHidden ? 'is-hidden' : ''; ?>">
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/categories/edit/<?php echo $category['id_category']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/admin/products?search=&sort=created_at-desc&category=<?php echo $category['id_category']; ?>&brand=&status=&type="><i class='bx bx-package'></i> Ver productos</a>
                                <a href="#" 
                                   class="btn-visibility-toggle" 
                                   data-id="<?php echo $category['id_category']; ?>" 
                                   data-current-hidden="<?php echo $category['hidden']; ?>">
                                    <i class='bx <?php echo $isHidden ? 'bx-show' : 'bx-hide'; ?>'></i>
                                    <?php echo $isHidden ? 'Mostrar en web' : 'Ocultar de web'; ?>
                                </a>
                                <a href="/admin/categories/delete/<?php echo $category['id_category']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>

                    <a href="/admin/products?search=&sort=created_at-desc&category=<?php echo $category['id_category']; ?>&brand=&status=&type=" style="text-decoration: none;">
                        <div class="grid-item-card__image lvr-product-grid-image">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/categories/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/categories/<?php echo rawurlencode($originalFile); ?>"
                                     alt="<?php echo htmlspecialchars($category['name']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                    </a>

                    <div class="grid-item-card__content">
                        <a href="/admin/products?search=&sort=created_at-desc&category=<?php echo $category['id_category']; ?>&brand=&status=&type=" style="text-decoration: none;">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($category['name']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($category['name']); ?></span>
                                <?php if ($isHidden): ?>
                                    <span class="lvr-hidden-tag">(Oculto)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line">ID: #<?php echo htmlspecialchars($category['id_category']); ?></div>
                            <div class="lvr-date-line">
                                CREADO: <?php echo date('d/m/Y', strtotime($category['created_at'])); ?>
                            </div>
                        </a>

                        <div class="lvr-card-footer-link" style="margin-top: auto; padding-top: 0.6rem; border-top: 1px solid #f1f5f9;">
                            <span class="status-pill <?php echo $isHidden ? 'inactive' : 'active'; ?>" style="cursor: default; justify-content: center; width: 100%;">
                                <i class='bx <?php echo $isHidden ? 'bx-hide' : 'bx-show'; ?>'></i>
                                <span><?php echo $isHidden ? 'Oculta en web' : 'Visible en web'; ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-search-alt'></i>
            <h2>No se encontraron categorías</h2>
            <p>Prueba a cambiar los filtros o a restablecer la búsqueda.</p>
        </div>
    <?php endif; ?>
</section>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>