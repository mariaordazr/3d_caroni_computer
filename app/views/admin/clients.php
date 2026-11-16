<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>
<section class="content-panel product-view-container view-list"> 
    <?php if (!empty($clients)): ?>
        <!-- VISTA DE LISTA -->
        <div class="table-list-ml">
            <?php foreach($clients as $client): ?>
                <?php 
                    $stars = max(0, min(5, (int)($client['rating'] ?? 0)));
                    $noteBadge = trim($client['notes'] ?? '');
                ?>
                <div class="table-item-ml" style="grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr) auto !important;">
                    <a href="/admin/clients/edit/<?php echo $client['id_client']; ?>">
                        <div class="table-item-ml__info" style="min-width: 220px;">
                            <p class="info__meta">DNI / RIF / Pasaporte: <?php echo htmlspecialchars($client['client_id_number']); ?></p>
                            <p class="info__title" style="font-weight: bold; font-size: 1em;"><?php echo htmlspecialchars($client['client_name']); ?></p>
                        </div>

                        <!-- Columna de Estrellas (0 a 5) y Observación Interna -->
                        <div style="display: flex; flex-direction: column; gap: 4px; min-width: 180px;">
                            <div style="color: #f59e0b; font-size: 1rem; letter-spacing: 1px;" title="Calificación: <?php echo $stars; ?>/5">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class='bx <?php echo ($s <= $stars) ? 'bxs-star' : 'bx-star'; ?>' style="<?php echo ($s <= $stars) ? 'color:#f59e0b;' : 'color:#cbd5e1;'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if ($noteBadge !== ''): ?>
                                <span style="display: inline-block; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; width: fit-content;">
                                    <i class='bx bx-purchase-tag-alt'></i> <?php echo htmlspecialchars($noteBadge); ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.75rem;">Sin observación</span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="table-item-ml__actions" style="margin-left: auto; justify-self: end;">
                        <div class="actions-menu-container">
                            <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/clients/edit/<?php echo $client['id_client']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/admin/clients/delete/<?php echo $client['id_client']; ?>" class="danger" onclick="return confirm('¿Seguro que deseas eliminar este cliente?');"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- VISTA DE CUADRÍCULA -->
        <div class="generic-grid-container">
            <?php foreach($clients as $client): ?>
                <?php 
                    $stars = max(0, min(5, (int)($client['rating'] ?? 0)));
                    $noteBadge = trim($client['notes'] ?? '');
                ?>
                <div class="grid-item-card">
                    <a href="/admin/clients/edit/<?php echo $client['id_client']; ?>">
                        <div class="grid-item-card__image">
                            <picture>
                                <i class='bx bx-user-circle' style="font-size: 5.5em; color: #64748b;"></i>
                            </picture>
                        </div>
                        <div class="grid-item-card__content">
                            <p class="info__meta">DNI / RIF: <?php echo htmlspecialchars($client['client_id_number']); ?></p>
                            <p class="info__title" style="font-weight: bold; font-size: 0.95em;"><?php echo htmlspecialchars($client['client_name']); ?></p>
                            <div style="color: #f59e0b; font-size: 0.95rem; margin-top: 6px;">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class='bx <?php echo ($s <= $stars) ? 'bxs-star' : 'bx-star'; ?>' style="<?php echo ($s <= $stars) ? 'color:#f59e0b;' : 'color:#cbd5e1;'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <?php if ($noteBadge !== ''): ?>
                                <span style="margin-top: 4px; display: inline-block; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 2px 8px; border-radius: 12px; font-size: 0.72rem; font-weight: 600; width: fit-content;">
                                    <?php echo htmlspecialchars($noteBadge); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/clients/edit/<?php echo $client['id_client']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <a href="/admin/clients/delete/<?php echo $client['id_client']; ?>" class="danger" onclick="return confirm('¿Seguro que deseas eliminar este cliente?');"><i class='bx bx-trash'></i> Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-group'></i>
            <h2>No hay clientes registrados</h2>
            <p>Comienza a agregar clientes desde el botón superior.</p>
        </div>
    <?php endif; ?>
</section>
<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>