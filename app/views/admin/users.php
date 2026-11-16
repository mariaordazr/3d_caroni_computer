<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>

<section class="content-panel product-view-container view-list"> 
    <?php if (!empty($users)): ?>
        <!-- 1. VISTA DE LISTA -->
        <div class="table-list-ml lvr-entity-list">
            <?php foreach ($users as $user): ?>
                <?php
                    $isSuperAdmin = !empty($user['is_super_admin']) && $user['is_super_admin'] == 1;
                    $isCurrentUser = $_SESSION['idUser'] == $user['id_user'];
                    $isCurrentUserAdmin = !empty($userInfo['is_super_admin']) && $userInfo['is_super_admin'] == 1;
                    
                    $canEdit = !$isSuperAdmin || $isCurrentUserAdmin;
                    $canDelete = !$isSuperAdmin && !$isCurrentUser;

                    $originalFile = !empty($user['avatar']) ? $user['avatar'] : 'default.jpg'; 
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/users/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $roleRaw = $user['role'] ?? 'seller';
                    if ($roleRaw === 'admin') {
                        $roleLabel = 'Administrador';
                        $rolePillClass = 'new';
                        $roleIcon = 'bx-shield-quarter';
                    } elseif ($roleRaw === 'seller') {
                        $roleLabel = 'Vendedor';
                        $rolePillClass = 'active';
                        $roleIcon = 'bx-store-alt';
                    } else {
                        $roleLabel = 'Técnico';
                        $rolePillClass = 'offer';
                        $roleIcon = 'bx-wrench';
                    }
                ?>
                <div class="table-item-ml product-item-ml lvr-product-list-item lvr-entity-row">
                    <a href="<?php echo $canEdit ? '/admin/users/edit/' . $user['id_user'] : '#'; ?>" 
                       class="lvr-product-main-link" 
                       <?php echo !$canEdit ? 'onclick="return false;" style="cursor: default;"' : ''; ?>>
                        <div class="product-item-ml__image lvr-product-thumb lvr-thumb-cover">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/users/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/users/<?php echo rawurlencode($originalFile); ?>"
                                     alt="Avatar de <?php echo htmlspecialchars($user['name']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                        <div class="product-item-ml__info">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($user['name']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($user['name']); ?></span>
                                <?php if ($isCurrentUser): ?>
                                    <span class="lvr-models-inline">(Tú)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line">@<?php echo htmlspecialchars($user['username']); ?> · <?php echo htmlspecialchars($user['email']); ?></div>
                            <div class="lvr-date-line">
                                CREADO: <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                <?php if (!empty($user['updated_at']) && $user['created_at'] !== $user['updated_at']): ?>
                                     · MODIFICADO: <?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>

                    <div class="compact-row-details lvr-product-details-group">
                        <div class="product-item-ml__status lvr-product-badges" style="width: auto !important; min-width: 140px;">
                            <span class="status-pill <?php echo $rolePillClass; ?>" style="cursor: default; justify-content: center;">
                                <i class='bx <?php echo $roleIcon; ?>'></i>
                                <span><?php echo htmlspecialchars($roleLabel); ?></span>
                            </span>
                        </div>
                    </div>

                    <div class="table-item-ml__actions product-item-ml__actions">
                        <div class="actions-menu-container">
                            <?php if ($canEdit): ?>
                                <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                                <div class="actions-dropdown">
                                    <a href="/admin/users/edit/<?php echo $user['id_user']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                    <?php if ($canDelete): ?>
                                        <a href="/admin/users/delete/<?php echo $user['id_user']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 2. VISTA DE CUADRÍCULA -->
        <div class="generic-grid-container product-grid-container lvr-entity-grid">
            <?php foreach ($users as $user): ?>
                <?php
                    $isSuperAdmin = !empty($user['is_super_admin']) && $user['is_super_admin'] == 1;
                    $isCurrentUser = $_SESSION['idUser'] == $user['id_user'];
                    $isCurrentUserAdmin = !empty($userInfo['is_super_admin']) && $userInfo['is_super_admin'] == 1;

                    $canEdit = !$isSuperAdmin || $isCurrentUserAdmin;
                    $canDelete = !$isSuperAdmin && !$isCurrentUser;

                    $originalFile = !empty($user['avatar']) ? $user['avatar'] : 'default.jpg'; 
                    $showWebp = false;
                    $smallFile = '';

                    if ($originalFile !== 'default.jpg') {
                        $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                        $smallFile = 'small_' . $nameWithoutExt . '.webp';
                        $webpPath = ROOT_PATH . 'public/assets/uploads/users/' . $smallFile;
                        if (file_exists($webpPath)) {
                            $showWebp = true;
                        }
                    }

                    $roleRaw = $user['role'] ?? 'seller';
                    if ($roleRaw === 'admin') {
                        $roleLabel = 'Administrador';
                        $rolePillClass = 'new';
                        $roleIcon = 'bx-shield-quarter';
                    } elseif ($roleRaw === 'seller') {
                        $roleLabel = 'Vendedor';
                        $rolePillClass = 'active';
                        $roleIcon = 'bx-store-alt';
                    } else {
                        $roleLabel = 'Técnico';
                        $rolePillClass = 'offer';
                        $roleIcon = 'bx-wrench';
                    }
                ?>
                <div class="grid-item-card lvr-product-card lvr-entity-card">
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <?php if ($canEdit): ?>
                                <button type="button" class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                                <div class="actions-dropdown">
                                    <a href="/admin/users/edit/<?php echo $user['id_user']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                    <?php if ($canDelete): ?>
                                        <a href="/admin/users/delete/<?php echo $user['id_user']; ?>" class="btn-delete danger"><i class='bx bx-trash'></i> Eliminar</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="<?php echo $canEdit ? '/admin/users/edit/' . $user['id_user'] : '#'; ?>" 
                       style="text-decoration: none;"
                       <?php echo !$canEdit ? 'onclick="return false;"' : ''; ?>>
                        <div class="grid-item-card__image lvr-product-grid-image lvr-thumb-cover">
                            <picture>
                                <?php if ($showWebp): ?>
                                    <source srcset="/assets/uploads/users/<?php echo rawurlencode($smallFile); ?>" type="image/webp">
                                <?php endif; ?>
                                <img src="/assets/uploads/users/<?php echo rawurlencode($originalFile); ?>"
                                     alt="Avatar de <?php echo htmlspecialchars($user['name']); ?>"
                                     loading="lazy">
                            </picture>
                        </div>
                    </a>

                    <div class="grid-item-card__content">
                        <a href="<?php echo $canEdit ? '/admin/users/edit/' . $user['id_user'] : '#'; ?>" 
                           style="text-decoration: none;"
                           <?php echo !$canEdit ? 'onclick="return false;"' : ''; ?>>
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($user['name']); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($user['name']); ?></span>
                                <?php if ($isCurrentUser): ?>
                                    <span class="lvr-models-inline">(Tú)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line">@<?php echo htmlspecialchars($user['username']); ?></div>
                            <div class="lvr-date-line" style="text-transform: none !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <i class='bx bx-envelope'></i> <?php echo htmlspecialchars($user['email']); ?>
                            </div>
                        </a>

                        <div class="lvr-card-footer-link" style="margin-top: auto; padding-top: 0.6rem; border-top: 1px solid #f1f5f9;">
                            <span class="status-pill <?php echo $rolePillClass; ?>" style="cursor: default; justify-content: center; width: 100%;">
                                <i class='bx <?php echo $roleIcon; ?>'></i>
                                <span><?php echo htmlspecialchars($roleLabel); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-search-alt'></i>
            <h2>No se encontraron usuarios</h2>
            <p>Prueba a cambiar los filtros o a restablecer la búsqueda.</p>
        </div>
    <?php endif; ?>
</section>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>