<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>

<section class="content-panel product-view-container view-list">    
    <?php if (!empty($blogs)): ?>
        <!-- 1. VISTA DE LISTA -->
        <div class="table-list-ml lvr-entity-list">
            <?php foreach ($blogs as $blog): ?>
                <?php 
                    $originalFile = !empty($blog['cover_image']) ? $blog['cover_image'] : 'default.jpg'; 
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/blog/posts/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $isPublished = (isset($blog['status']) && $blog['status'] === 'publicado');
                ?>
                <div class="table-item-ml product-item-ml lvr-product-list-item lvr-entity-row">
                    <a href="/admin/blogs/edit/<?php echo $blog['id_blog']; ?>" class="lvr-product-main-link">
                        <div class="product-item-ml__image lvr-product-thumb lvr-thumb-cover">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/blog/posts/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/blog/posts/<?php echo rawurlencode($originalFile); ?>"
                                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                        <div class="product-item-ml__info">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($blog['title']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($blog['title']); ?></span>
                            </div>
                            <div class="lvr-code-line">Autor: <?php echo htmlspecialchars(!empty($blog['author']) ? $blog['author'] : 'Admin'); ?></div>
                            <div class="lvr-date-line">
                                PUBLICADO: <?php echo date('d/m/Y', strtotime($blog['created_at'])); ?>
                            </div>
                        </div>
                    </a>

                    <div class="compact-row-details lvr-product-details-group">
                        <div class="product-item-ml__status lvr-product-badges">
                            <span class="status-pill <?php echo $isPublished ? 'active' : 'none'; ?>" style="cursor: default; justify-content: center;">
                                <i class='bx <?php echo $isPublished ? 'bx-check-circle' : 'bx-edit-alt'; ?>'></i>
                                <span><?php echo ucfirst($blog['status'] ?? 'Borrador'); ?></span>
                            </span>
                        </div>
                    </div>

                    <div class="table-item-ml__actions product-item-ml__actions">
                        <div class="actions-menu-container">
                            <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/blogs/edit/<?php echo $blog['id_blog']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/blog/<?php echo htmlspecialchars($blog['slug'] ?? ''); ?>" target="_blank"><i class='bx bx-link-external'></i> Ver artículo</a>
                                <a href="#" class="danger" onclick="deleteBlog(<?php echo $blog['id_blog']; ?>); return false;"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 2. VISTA DE CUADRÍCULA -->
        <div class="generic-grid-container product-grid-container lvr-entity-grid">
            <?php foreach ($blogs as $blog): ?>
                <?php 
                    $originalFile = !empty($blog['cover_image']) ? $blog['cover_image'] : 'default.jpg'; 
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/blog/posts/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $isPublished = (isset($blog['status']) && $blog['status'] === 'publicado');
                ?>
                <div class="grid-item-card lvr-product-card lvr-entity-card">
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/blogs/edit/<?php echo $blog['id_blog']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/blog/<?php echo htmlspecialchars($blog['slug'] ?? ''); ?>" target="_blank"><i class='bx bx-link-external'></i> Ver artículo</a>
                                <a href="#" class="danger" onclick="deleteBlog(<?php echo $blog['id_blog']; ?>); return false;"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>

                    <a href="/admin/blogs/edit/<?php echo $blog['id_blog']; ?>" style="text-decoration: none;">
                        <div class="grid-item-card__image lvr-product-grid-image lvr-thumb-cover">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/blog/posts/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/blog/posts/<?php echo rawurlencode($originalFile); ?>"
                                     alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                    </a>

                    <div class="grid-item-card__content">
                        <a href="/admin/blogs/edit/<?php echo $blog['id_blog']; ?>" style="text-decoration: none;">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($blog['title']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($blog['title']); ?></span>
                            </div>
                            <div class="lvr-code-line">Autor: <?php echo htmlspecialchars(!empty($blog['author']) ? $blog['author'] : 'Admin'); ?></div>
                            <div class="lvr-date-line">
                                <?php echo date('d/m/Y', strtotime($blog['created_at'])); ?>
                            </div>
                        </a>

                        <div class="lvr-card-footer-link" style="margin-top: auto; padding-top: 0.6rem; border-top: 1px solid #f1f5f9;">
                            <span class="status-pill <?php echo $isPublished ? 'active' : 'none'; ?>" style="cursor: default; justify-content: center; width: 100%;">
                                <i class='bx <?php echo $isPublished ? 'bx-check-circle' : 'bx-edit-alt'; ?>'></i>
                                <span><?php echo ucfirst($blog['status'] ?? 'Borrador'); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-book-content'></i>
            <h2>No hay artículos en el blog todavía</h2>
            <p>Comienza a escribir tu primer post o cambia los filtros de búsqueda.</p>
        </div>
    <?php endif; ?>
</section>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>