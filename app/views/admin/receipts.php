<?php include ROOT_PATH . 'app/views/admin/templates/filterbar.php'; ?>

<?php $isCurrentUserAdmin = $userInfo['is_super_admin'] == 1; ?>
<?php
if (!function_exists('getReceiptThumbUrl')) {
    function getReceiptThumbUrl($img) {
        if (empty($img)) return '';
        if (strpos($img, 'http') === 0) {
            if (preg_match('/(d\/|id=)([a-zA-Z0-9_-]+)/', $img, $matches)) {
                return 'https://drive.google.com/thumbnail?id=' . $matches[2] . '&sz=w400';
            }
            return $img;
        }
        return '/assets/uploads/receipts/' . ltrim($img, '/');
    }
}
?>
<section class="content-panel product-view-container view-list">    
    <?php if (!empty($receipts)): ?>
        <!-- =========================================================
             1. VISTA DE LISTA (CONTINUIDAD ESTÉTICA LOVABLE)
             ========================================================= -->
        <div class="table-list-ml">
            <?php foreach($receipts as $receipt): ?>
                <?php
                    $receiptItemsArr = json_decode($receipt['items_json'] ?? '[]', true) ?: [];
                    $modelsArr = [];
                    foreach ($receiptItemsArr as $rIt) {
                        $mName = trim($rIt['model'] ?? '');
                        if ($mName !== '') $modelsArr[] = $mName;
                    }
                    $modelsText = implode(', ', $modelsArr);

                    // 1. Estado Garantía
                    $wStatusRaw = $receipt['warranty_status'] ?? 'normal';
                    $warrantyCount = 1;
                    if (strpos($wStatusRaw, 'warranty:') === 0) {
                        $wStatus = 'warranty';
                        $warrantyCount = (int)substr($wStatusRaw, 9) ?: 1;
                    } else {
                        $wStatus = 'normal';
                    }
                    if ($wStatus === 'warranty') {
                        $wMessage = 'Garantía (' . $warrantyCount . ')';
                        $wClass = 'danger';
                        $wIcon = 'bx-shield-quarter';
                    } else {
                        $wMessage = 'Servicio normal';
                        $wClass = 'default';
                        $wIcon = 'bx-file';
                    }

                    // 2. Estado Servicio
                    $sStatus = $receipt['service_status'] ?? 'in_progress'; 
                    if ($sStatus === 'delivered') {
                        $sMessage = 'Entregado'; $sClass = 'success'; $sIcon = 'bx-check-double'; $sCustomStyle = '';
                    } elseif ($sStatus === 'ready') {
                        $sMessage = 'Servicio listo'; $sClass = 'blue'; $sIcon = 'bx-check'; $sCustomStyle = '';
                    } elseif ($sStatus === 'cancelled') {
                        $sMessage = 'Cancelado'; $sClass = 'danger'; $sIcon = 'bx-x-circle'; $sCustomStyle = 'style="background-color:#fee2e2 !important; color:#dc2626 !important;"';
                    } else {
                        $sMessage = 'En proceso'; $sClass = 'default'; $sIcon = 'bx-time-five'; $sCustomStyle = '';
                    }

                    // 3. Estado Pago
                    $pStatusRaw = $receipt['payment_status'] ?? 'unpaid';
                    $abonoMonto = '';
                    if (strpos($pStatusRaw, 'partial:') === 0) {
                        $pStatus = 'partial';
                        $abonoMonto = substr($pStatusRaw, 8);
                    } else {
                        $pStatus = $pStatusRaw;
                    }
                    if ($pStatus === 'paid') {
                        $pMessage = 'Pagado'; $pClass = 'success'; $pIcon = 'bx-dollar';
                    } elseif ($pStatus === 'partial') {
                        $pMessage = 'Abonado - ' . $this->siteContentModel->getValue('money_symbol') . ' ' . htmlspecialchars($abonoMonto);
                        $pClass = 'blue'; $pIcon = 'bx-wallet';
                    } else {
                        $pMessage = 'Pago pendiente'; $pClass = 'warning'; $pIcon = 'bx-time-five';
                    }
                ?>
                <div class="table-item-ml lvr-receipt-list-item">
                    <!-- Columna 1: Icono de hoja + Datos principales -->
                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank" class="lvr-list-main-link">
                        <div class="table-item-ml__info">
                            <div class="lvr-client-heading" title="<?php echo htmlspecialchars($receipt['client_name'] . ($modelsText !== '' ? ' (' . $modelsText . ')' : '')); ?>">
                                <span class="lvr-client-name-text"><?php echo htmlspecialchars($receipt['client_name']); ?></span>
                            </div>
                            <div class="lvr-date-line">
                                <?php if ($modelsText !== ''): ?>
                                    <span class="lvr-models-inline">(<?php echo htmlspecialchars($modelsText); ?>)</span>
                                <?php endif; ?>
                            </div>
                            <div class="lvr-code-line"><?php echo htmlspecialchars($receipt['receipt_code']); ?></div>
                            <div class="lvr-date-line">
                                CREADO: <strong><?php echo date('d/m/Y H:i', strtotime($receipt['created_at'])); ?></strong>
                                <?php if ($receipt['created_at'] !== $receipt['updated_at']): ?>
                                     · EDITADO: <strong><?php echo date('d/m/Y H:i', strtotime($receipt['updated_at'])); ?></strong>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($receipt['technician_name'])): ?>
                                <div class="lvr-tech-line">(<?php echo htmlspecialchars($receipt['technician_name']); ?>)</div>
                            <?php endif; ?>
                        </div>
                    </a>

                    <!-- Columna 2: Botones de Estado -->
                    <div class="table-item-ml__status-column">
                        <div class="status-toggle-container receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $wClass; ?> btn-dropdown-toggle" data-dropdown="warranty-<?php echo $receipt['id_receipt']; ?>" <?php echo ($wStatus === 'warranty') ? 'style="background-color:#fee2e2 !important; color:#dc2626 !important;"' : ''; ?>>
                                <i class='bx <?php echo $wIcon; ?>'></i> <?php echo $wMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="warranty-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'warranty', 'normal', '', 1, this);"><i class='bx bx-file'></i> Servicio normal</div>
                                <div class="dropdown-option" onclick="promptWarrantyCount(<?php echo $receipt['id_receipt']; ?>, <?php echo $warrantyCount; ?>);"><i class='bx bx-shield-quarter'></i> Por garantía</div>
                            </div>
                        </div>
                    
                        <div class="status-toggle-container receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $sClass; ?> btn-dropdown-toggle" data-dropdown="service-<?php echo $receipt['id_receipt']; ?>" <?php echo $sCustomStyle; ?>>
                                <i class='bx <?php echo $sIcon; ?>'></i> <?php echo $sMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="service-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'in_progress', '', 1, this);"><i class='bx bx-time-five'></i> En proceso</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'ready', '', 1, this);"><i class='bx bx-check'></i> Servicio listo</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'delivered', '', 1, this);"><i class='bx bx-check-double'></i> Entregado</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'cancelled', '', 1, this);" style="color: #dc2626;"><i class='bx bx-x-circle'></i> Cancelado</div>
                            </div>
                        </div>

                        <div class="status-toggle-container receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $pClass; ?> btn-dropdown-toggle" data-dropdown="payment-<?php echo $receipt['id_receipt']; ?>">
                                <i class='bx <?php echo $pIcon; ?>'></i> <?php echo $pMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="payment-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'payment', 'unpaid', '', 1, this);"><i class='bx bx-time-five'></i> Pago pendiente</div>
                                <div class="dropdown-option" onclick="promptPartialPayment(<?php echo $receipt['id_receipt']; ?>, '<?php echo htmlspecialchars(addslashes($abonoMonto)); ?>');"><i class='bx bx-wallet'></i> Abonado</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'payment', 'paid', '', 1, this);"><i class='bx bx-dollar-circle'></i> Pagado</div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 3: Contacto -->
                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank" class="lvr-list-contact-link">
                        <div class="table-item-ml__contact">
                            <?php if (!empty($receipt['client_email'])): ?>
                                <div class="lvr-sent-row <?php echo $receipt['sent_email'] ? 'is-sent' : 'is-pending'; ?>">
                                    <i class='bx bx-envelope'></i>
                                    <span><?php echo htmlspecialchars(strlen($receipt['client_email']) > 20 ? substr($receipt['client_email'], 0, 20) . '...' : $receipt['client_email']); ?></span>
                                    <i class='bx <?php echo $receipt['sent_email'] ? 'bxs-check-circle' : 'bx-minus-circle'; ?>' ></i>
                                    <span><?php echo $receipt['sent_email'] ? 'Enviado' : 'No enviado'; ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="lvr-sent-row <?php echo $receipt['sent_whatsapp'] ? 'is-sent' : 'is-pending'; ?>">
                                <i class='bx bxl-whatsapp'></i>
                                <span><?php echo htmlspecialchars($receipt['client_phone']); ?></span>
                                <i class='bx <?php echo $receipt['sent_whatsapp'] ? 'bxs-check-circle' : 'bx-minus-circle'; ?>'></i>
                                <span><?php echo $receipt['sent_whatsapp'] ? 'Enviado' : 'No enviado'; ?></span>
                            </div>
                        </div>
                    </a>

                    <!-- Columna 5: Menú de 3 puntos -->
                    <div class="table-item-ml__actions">
                        <div class="actions-menu-container">
                            <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank"><i class='bx bxs-file-pdf'></i> Ver PDF</a>
                                <a href="#" class="btn-private-info"
                                   data-receipt="<?php echo htmlspecialchars(json_encode($receipt, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                                   data-tech="<?php echo htmlspecialchars($receipt['technician_name'] ?? ''); ?>" 
                                   data-notes="<?php echo htmlspecialchars($receipt['private_notes'] ?? ''); ?>">
                                    <i class='bx bx-lock-alt'></i> Ver Info Privada
                                </a>
                                <a href="/admin/receipts/edit/<?php echo $receipt['id_receipt']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <?php if (!empty(trim($receipt['client_email'] ?? ''))): ?>
                                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>?action=email" target="_blank"><i class='bx bx-envelope'></i> Enviar por Correo</a>
                                <?php endif; ?>
                                <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>?action=whatsapp" target="_blank"><i class='bx bxl-whatsapp'></i> Enviar por WhatsApp</a>
                                <?php if ($isCurrentUserAdmin): ?>
                                    <a href="#" class="danger" onclick="deleteReceipt(<?php echo $receipt['id_receipt']; ?>); return false;">
                                        <i class='bx bx-trash'></i> Eliminar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- =========================================================
             2. VISTA DE CUADRÍCULA (CON ICONO DE HOJA ARRIBA MÁS PEQUEÑO)
             ========================================================= -->
        <div class="generic-grid-container lvr-receipts-grid">
            <?php foreach($receipts as $receipt): ?>
                <?php
                    $receiptItemsArr = json_decode($receipt['items_json'] ?? '[]', true) ?: [];
                    $modelsArr = [];
                    foreach ($receiptItemsArr as $rIt) {
                        $mName = trim($rIt['model'] ?? '');
                        if ($mName !== '') $modelsArr[] = $mName;
                    }
                    $modelsText = implode(', ', $modelsArr);

                    // 1. Estado Garantía
                    $wStatusRaw = $receipt['warranty_status'] ?? 'normal';
                    $warrantyCount = 1;
                    if (strpos($wStatusRaw, 'warranty:') === 0) {
                        $wStatus = 'warranty';
                        $warrantyCount = (int)substr($wStatusRaw, 9) ?: 1;
                    } else {
                        $wStatus = 'normal';
                    }
                    if ($wStatus === 'warranty') {
                        $wMessage = 'Garantía (' . $warrantyCount . ')';
                        $wClass = 'danger';
                        $wIcon = 'bx-shield-quarter';
                    } else {
                        $wMessage = 'Servicio normal';
                        $wClass = 'default';
                        $wIcon = 'bx-file';
                    }

                    // 2. Estado Servicio
                    $sStatus = $receipt['service_status'] ?? 'in_progress'; 
                    if ($sStatus === 'delivered') {
                        $sMessage = 'Entregado'; $sClass = 'success'; $sIcon = 'bx-check-double'; $sCustomStyle = '';
                    } elseif ($sStatus === 'ready') {
                        $sMessage = 'Servicio listo'; $sClass = 'blue'; $sIcon = 'bx-check'; $sCustomStyle = '';
                    } elseif ($sStatus === 'cancelled') {
                        $sMessage = 'Cancelado'; $sClass = 'danger'; $sIcon = 'bx-x-circle'; $sCustomStyle = 'style="background-color:#fee2e2 !important; color:#dc2626 !important;"';
                    } else {
                        $sMessage = 'En proceso'; $sClass = 'default'; $sIcon = 'bx-time-five'; $sCustomStyle = '';
                    }

                    // 3. Estado Pago
                    $pStatusRaw = $receipt['payment_status'] ?? 'unpaid';
                    $abonoMonto = '';
                    if (strpos($pStatusRaw, 'partial:') === 0) {
                        $pStatus = 'partial';
                        $abonoMonto = substr($pStatusRaw, 8);
                    } else {
                        $pStatus = $pStatusRaw;
                    }
                    if ($pStatus === 'paid') {
                        $pMessage = 'Pagado'; $pClass = 'success'; $pIcon = 'bx-dollar';
                    } elseif ($pStatus === 'partial') {
                        $pMessage = 'Abonado - ' . $this->siteContentModel->getValue('money_symbol') . ' ' . htmlspecialchars($abonoMonto);
                        $pClass = 'blue'; $pIcon = 'bx-wallet';
                    } else {
                        $pMessage = 'Pago pendiente'; $pClass = 'warning'; $pIcon = 'bx-time-five';
                    }
                ?>
                <div class="grid-item-card lvr-receipt-card">
                    <!-- Botón de 3 puntos en la esquina superior derecha -->
                    <div class="grid-item-card__actions">
                        <div class="actions-menu-container">
                            <button class="actions-menu-toggle"><i class='bx bx-dots-vertical-rounded'></i></button>
                            <div class="actions-dropdown">
                                <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank"><i class='bx bxs-file-pdf'></i> Ver PDF</a>
                                <a href="#" class="btn-private-info"
                                   data-receipt="<?php echo htmlspecialchars(json_encode($receipt, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"
                                   data-tech="<?php echo htmlspecialchars($receipt['technician_name'] ?? ''); ?>" 
                                   data-notes="<?php echo htmlspecialchars($receipt['private_notes'] ?? ''); ?>">
                                    <i class='bx bx-lock-alt'></i> Ver Info Privada
                                </a>
                                <a href="/admin/receipts/edit/<?php echo $receipt['id_receipt']; ?>"><i class='bx bx-edit'></i> Editar</a>
                                <?php if (!empty(trim($receipt['client_email'] ?? ''))): ?>
                                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>?action=email" target="_blank"><i class='bx bx-envelope'></i> Enviar por Correo</a>
                                <?php endif; ?>
                                <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>?action=whatsapp" target="_blank"><i class='bx bxl-whatsapp'></i> Enviar por WhatsApp</a>
                                <?php if ($isCurrentUserAdmin): ?>
                                    <a href="#" class="danger" onclick="deleteReceipt(<?php echo $receipt['id_receipt']; ?>); return false;">
                                        <i class='bx bx-trash'></i> Eliminar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        // Obtener primera foto disponible (inicial o de proceso) ANTES del contenedor
                        $initialImgs = json_decode($receipt['images_json'] ?? '[]', true) ?: [];
                        $processImgs = json_decode($receipt['process_images_json'] ?? '[]', true) ?: [];
                        $allReceiptImgs = array_merge($initialImgs, $processImgs);$firstThumbUrl = !empty($allReceiptImgs) ? getReceiptThumbUrl($allReceiptImgs[0]) : '';
                    ?>

                    <!-- Miniatura de foto ocupando todo el recuadro, o ícono de archivo por defecto -->
                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank" style="text-decoration: none; display: block;">
                        <div class="grid-item-card__image lvr-receipt-icon-box <?php echo $firstThumbUrl !== '' ? 'has-thumb' : ''; ?>" 
                             style="overflow: hidden !important; position: relative !important; <?php echo $firstThumbUrl !== '' ? 'padding: 0 !important;' : ''; ?>">
                            <picture style="width: 100% !important; height: 100% !important; max-width: none !important; max-height: none !important; padding: 0 !important; margin: 0 !important; display: flex; align-items: center; justify-content: center;">
                                <?php if ($firstThumbUrl !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($firstThumbUrl); ?>" 
                                         alt="<?php echo htmlspecialchars($receipt['receipt_code']); ?>" 
                                         loading="lazy"
                                         style="position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; max-width: none !important; max-height: none !important; object-fit: cover !important; object-position: center; border-radius: inherit; display: block;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block'; this.closest('.lvr-receipt-icon-box').style.padding='';">
                                    <i class='bx bx-file' style="display: none;"></i>
                                <?php else: ?>
                                    <i class='bx bx-file'></i>
                                <?php endif; ?>
                            </picture>
                        </div>
                    </a>

                    <!-- Contenido Principal: Cliente grande arriba + Código + Fecha -->
                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank" style="text-decoration: none;">
                        <div class="grid-item-card__content">
                            <span class="lvr-client-name-text"><?php echo htmlspecialchars($receipt['client_name']); ?></span>
                            <?php if ($modelsText !== ''): ?>
                                <span class="lvr-models-inline">(<?php echo htmlspecialchars($modelsText); ?>)</span>
                            <?php endif; ?>
                            <div class="lvr-code-line"><?php echo htmlspecialchars($receipt['receipt_code']); ?></div>
                            <div class="lvr-date-line">
                                CREADO <strong><?php echo date('d/m/Y H:i', strtotime($receipt['created_at'])); ?></strong>
                                <?php if ($receipt['created_at'] !== $receipt['updated_at']): ?>
                                     · EDITADO
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($receipt['technician_name'])): ?>
                                <div class="lvr-tech-line">(<?php echo htmlspecialchars($receipt['technician_name']); ?>)</div>
                            <?php endif; ?>
                        </div>
                    </a>

                    <!-- Botones de Estado en Grid -->
                    <div class="lvr-card-badges">
                        <div class="receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $wClass; ?> btn-dropdown-toggle" data-dropdown="grid-warranty-<?php echo $receipt['id_receipt']; ?>" <?php echo ($wStatus === 'warranty') ? 'style="background-color:#fee2e2 !important; color:#dc2626 !important;"' : ''; ?>>
                                <i class='bx <?php echo $wIcon; ?>'></i> <?php echo $wMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="grid-warranty-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'warranty', 'normal', '', 1, this);"><i class='bx bx-file'></i> Servicio normal</div>
                                <div class="dropdown-option" onclick="promptWarrantyCount(<?php echo $receipt['id_receipt']; ?>, <?php echo $warrantyCount; ?>);"><i class='bx bx-shield-quarter'></i> Por garantía</div>
                            </div>
                        </div>
                        
                        <div class="receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $sClass; ?> btn-dropdown-toggle" data-dropdown="grid-service-<?php echo $receipt['id_receipt']; ?>" <?php echo $sCustomStyle; ?>>
                                <i class='bx <?php echo $sIcon; ?>'></i> <?php echo $sMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="grid-service-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'in_progress', '', 1, this);"><i class='bx bx-time-five'></i> En proceso</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'ready', '', 1, this);"><i class='bx bx-check'></i> Servicio listo</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'delivered', '', 1, this);"><i class='bx bx-check-double'></i> Entregado</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'service', 'cancelled', '', 1, this);" style="color: #dc2626;"><i class='bx bx-x-circle'></i> Cancelado</div>
                            </div>
                        </div>

                        <div class="receipt-dropdown-wrapper">
                            <button type="button" class="interactive-badge badge-<?php echo $pClass; ?> btn-dropdown-toggle" data-dropdown="grid-payment-<?php echo $receipt['id_receipt']; ?>">
                                <i class='bx <?php echo $pIcon; ?>'></i> <?php echo $pMessage; ?>
                            </button>
                            <div class="actions-dropdown" id="grid-payment-<?php echo $receipt['id_receipt']; ?>">
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'payment', 'unpaid', '', 1, this);"><i class='bx bx-time-five'></i> Pago pendiente</div>
                                <div class="dropdown-option" onclick="promptPartialPayment(<?php echo $receipt['id_receipt']; ?>, '<?php echo htmlspecialchars(addslashes($abonoMonto)); ?>');"><i class='bx bx-wallet'></i> Abonado</div>
                                <div class="dropdown-option" onclick="updateReceiptStatus(<?php echo $receipt['id_receipt']; ?>, 'payment', 'paid', '', 1, this);"><i class='bx bx-dollar-circle'></i> Pagado</div>
                            </div>
                        </div>
                    </div>

                    <!-- Pie de Tarjeta: Contacto y Estado de Envío -->
                    <a href="/admin/receipts/view/<?php echo $receipt['id_receipt']; ?>" target="_blank" class="lvr-card-footer-link">
                        <div class="lvr-card-contact-info">
                            <?php if (!empty($receipt['client_email'])): ?>
                                <div class="lvr-sent-row <?php echo $receipt['sent_email'] ? 'is-sent' : 'is-pending'; ?>">
                                    <i class='bx bx-envelope'></i>
                                    <span><?php echo htmlspecialchars(substr($receipt['client_email'], 0, 12)); ?>...</span>
                                    <i class='bx <?php echo $receipt['sent_email'] ? 'bxs-check-circle' : 'bx-minus-circle'; ?>'></i>
                                    <span><?php echo $receipt['sent_email'] ? 'Enviado' : 'No enviado'; ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($receipt['client_phone'])): ?>
                                <div class="lvr-sent-row <?php echo $receipt['sent_whatsapp'] ? 'is-sent' : 'is-pending'; ?>">
                                    <i class='bx bxl-whatsapp'></i>
                                    <span><?php echo htmlspecialchars($receipt['client_phone']); ?></span>
                                    <i class='bx <?php echo $receipt['sent_whatsapp'] ? 'bxs-check-circle' : 'bx-minus-circle'; ?>'></i>
                                    <span><?php echo $receipt['sent_whatsapp'] ? 'Enviado' : 'No enviado'; ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results-message">
            <i class='bx bx-receipt'></i>
            <h2>No hay notas generadas</h2>
            <p>Presiona el botón "Agregar" para crear la primera nota de entrega.</p>
        </div>
    <?php endif; ?>
</section>

<script>
    // Manejo de Dropdowns de Estado
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            
            const toggleBtn = e.target.closest('.btn-dropdown-toggle');
            
            if (toggleBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                const targetId = toggleBtn.getAttribute('data-dropdown');
                const targetMenu = document.getElementById(targetId);
                const isActive = targetMenu.classList.contains('is-active');

                // Cierra todos
                document.querySelectorAll('.receipt-dropdown-wrapper .actions-dropdown').forEach(m => m.classList.remove('is-active'));
                document.querySelectorAll('.grid-item-card').forEach(c => c.classList.remove('menu-active'));

                // Abre el actual si estaba cerrado
                if (!isActive) {
                    targetMenu.classList.add('is-active');
                    const card = toggleBtn.closest('.grid-item-card');
                    if(card) card.classList.add('menu-active');
                }
            } 
            else {
                // Cierra todo si se hace click fuera
                if (!e.target.closest('.actions-dropdown')) {
                    document.querySelectorAll('.receipt-dropdown-wrapper .actions-dropdown').forEach(m => m.classList.remove('is-active'));
                    document.querySelectorAll('.grid-item-card').forEach(c => c.classList.remove('menu-active'));
                }
            }
        });
    });

    // Ventana emergente para ingresar el monto cuando se selecciona "Abonado"
    function promptPartialPayment(id, currentAmount = '') {
        document.querySelectorAll('.actions-dropdown').forEach(m => m.classList.remove('is-active'));

        Swal.fire({
            title: 'Monto Abonado',
            text: 'Ingresa el monto del abono realizado por el cliente:',
            input: 'text',
            inputValue: currentAmount,
            inputPlaceholder: 'Ej: 10 o 100',
            showCancelButton: true,
            confirmButtonText: 'Guardar abono',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Por favor ingresa un monto válido';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateReceiptStatus(id, 'payment', 'partial', result.value.trim());
            }
        });
    }


    // Ventana emergente para preguntar cuántas veces ha vuelto por garantía
    function promptWarrantyCount(id, currentCount = 1) {
        document.querySelectorAll('.actions-dropdown').forEach(m => m.classList.remove('is-active'));

        Swal.fire({
            title: 'Ingreso por Garantía',
            text: '¿Cuántas veces ha ingresado este servicio por garantía?',
            input: 'number',
            inputValue: currentCount,
            inputAttributes: { min: 1, step: 1 },
            showCancelButton: true,
            confirmButtonText: 'Guardar garantía',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            inputValidator: (value) => {
                if (!value || parseInt(value) < 1) {
                    return 'Ingresa un número válido (mínimo 1)';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateReceiptStatus(id, 'warranty', 'warranty', '', parseInt(result.value));
            }
        });
    }

    // Petición POST al Servidor (Soporta Servicio, Pago y Garantía)
    function updateReceiptStatus(id, type, newStatus, amount = '', count = 1) {
        document.querySelectorAll('.actions-dropdown').forEach(m => m.classList.remove('is-active'));

        const endpoint = (type === 'payment') 
            ? '/admin/receipts/update-payment-status' 
            : '/admin/receipts/update-service-status';

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id, type: type, status: newStatus, amount: amount, count: count })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload(); 
            } else {
                alert('Error del servidor: ' + (data.message || 'No se pudo actualizar.'));
            }
        })
        .catch(error => {
            console.error('Error FETCH:', error);
            alert('Ocurrió un error de red al intentar actualizar el estado.');
        });
    }

    // ==========================================
    // 6. VENTANA EMERGENTE CON TODA LA INFORMACIÓN DE LA NOTA + NOTA PRIVADA
    // ==========================================
    window.showPrivateInfo = function(techName, notes, receiptJsonRaw = null) {
        const displayTech = techName ? techName : 'No asignado';
        const displayNotes = notes ? notes : 'No hay notas privadas.';

        if (!receiptJsonRaw) {
            Swal.fire({
                title: '<h3 style="color:#dc3545; margin-bottom:0; font-size: 1.4rem;"><i class="bx bx-lock-alt"></i> Información Privada</h3>',
                html: `
                    <div style="text-align: left; background: #fdf2f2; padding: 15px; border-left: 4px solid #dc3545; border-radius: 4px; margin-top: 10px; font-family: 'Inter', sans-serif;">
                        <p style="margin-bottom: 10px; font-size: 0.95rem;"><strong>Técnico Encargado:</strong><br> ${displayTech}</p>
                        <p style="margin-bottom: 0; font-size: 0.95rem;"><strong>Notas Privadas de Taller:</strong><br> <span style="white-space: pre-wrap; display: block; margin-top: 5px; color: #333;">${displayNotes}</span></p>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#6c757d'
            });
            return;
        }

        let r = {};
        try { r = JSON.parse(receiptJsonRaw); } catch (e) { r = {}; }

        let items = [];
        try { items = JSON.parse(r.items_json || '[]') || []; } catch (e) { items = []; }

        let budgetObj = {};
        try { budgetObj = JSON.parse(r.budget_json || '{}') || {}; } catch (e) { budgetObj = {}; }

        const tentativeItems = budgetObj.tentative_items || [];
        const tentativeFinal = budgetObj.tentative_final_amount || '';
        const definitiveItems = budgetObj.items || (Array.isArray(budgetObj) ? budgetObj : []);
        const definitiveFinal = budgetObj.final_amount || '';

        // Tabla de Equipos
        let itemsHtml = '';
        if (items.length > 0) {
            itemsHtml = `
                <table style="width:100%; border-collapse:collapse; font-size:0.8rem; margin-top:6px; margin-bottom:12px;">
                    <thead>
                        <tr style="background:#f1f5f9; text-align:left;">
                            <th style="padding:5px; border:1px solid #cbd5e1;">Und</th>
                            <th style="padding:5px; border:1px solid #cbd5e1;">Modelo</th>
                            <th style="padding:5px; border:1px solid #cbd5e1;">S/N</th>
                            <th style="padding:5px; border:1px solid #cbd5e1;">Accesorios</th>
                            <th style="padding:5px; border:1px solid #cbd5e1;">Detalles</th>
                            <th style="padding:5px; border:1px solid #cbd5e1;">Fallas</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(it => {
                            const isDeliv = (it.delivered == 1 || it.delivered === true || it.delivered === '1');
                            const delivBadge = isDeliv 
                                ? `<div style="margin-top:3px;"><span style="background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:2px 6px; border-radius:999px; display:inline-block;">✓ Equipo entregado</span></div>` 
                                : '';
                            return `
                                <tr>
                                    <td style="padding:5px; border:1px solid #cbd5e1; text-align:center;">${it.und || 1}</td>
                                    <td style="padding:5px; border:1px solid #cbd5e1;"><strong>${it.model || ''}</strong>${delivBadge}</td>
                                    <td style="padding:5px; border:1px solid #cbd5e1;">${it.serial || ''}</td>
                                    <td style="padding:5px; border:1px solid #cbd5e1;">${it.accessories || ''}</td>
                                    <td style="padding:5px; border:1px solid #cbd5e1;">${it.details || ''}</td>
                                    <td style="padding:5px; border:1px solid #cbd5e1;">${it.features || ''}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
        } else {
            itemsHtml = `<p style="font-size:0.82rem; color:#64748b;">Sin equipos registrados.</p>`;
        }

        // Tabla Presupuesto Tentativo
        let tentativeHtml = '';
        if (tentativeItems.length > 0) {
            tentativeHtml = `
                <div style="margin-top:10px;">
                    <strong style="font-size:0.85rem; color:#b45309;">Presupuesto Tentativo:</strong>
                    <table style="width:100%; border-collapse:collapse; font-size:0.8rem; margin-top:4px;">
                        <thead>
                            <tr style="background:#fef3c7; text-align:left;">
                                <th style="padding:5px; border:1px solid #fde68a;">Técnico</th>
                                <th style="padding:5px; border:1px solid #fde68a;">Descripción</th>
                                <th style="padding:5px; border:1px solid #fde68a;">Monto</th>
                                <th style="padding:5px; border:1px solid #fde68a;">Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tentativeItems.map(t => {
                                let choiceBadge = 'Incluido';
                                if (t.ask_client) {
                                    if (t.client_choice === 'accepted') choiceBadge = '<span style="color:#16a34a; font-weight:700;">✓ Aceptado</span>';
                                    else if (t.client_choice === 'rejected') choiceBadge = '<span style="color:#dc2626; font-weight:700;">✕ Rechazado</span>';
                                    else choiceBadge = '<span style="color:#64748b;">⏳ Sin respuesta</span>';
                                }
                                return `
                                    <tr>
                                        <td style="padding:5px; border:1px solid #fde68a;">${t.tech || '-'}</td>
                                        <td style="padding:5px; border:1px solid #fde68a;">${t.description || ''}</td>
                                        <td style="padding:5px; border:1px solid #fde68a;">${t.amount || '0.00'}</td>
                                        <td style="padding:5px; border:1px solid #fde68a;">${choiceBadge}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    ${tentativeFinal ? `<div style="text-align:right; font-size:0.82rem; font-weight:700; margin-top:4px;">Total Tentativo: ${tentativeFinal}</div>` : ''}
                </div>
            `;
        }

        // Tabla Presupuesto Definitivo
        let definitiveHtml = '';
        if (definitiveItems.length > 0) {
            definitiveHtml = `
                <div style="margin-top:10px;">
                    <strong style="font-size:0.85rem; color:#1d4ed8;">Presupuesto Definitivo:</strong>
                    <table style="width:100%; border-collapse:collapse; font-size:0.8rem; margin-top:4px;">
                        <thead>
                            <tr style="background:#dbeafe; text-align:left;">
                                <th style="padding:5px; border:1px solid #bfdbfe;">Técnico</th>
                                <th style="padding:5px; border:1px solid #bfdbfe;">Descripción</th>
                                <th style="padding:5px; border:1px solid #bfdbfe;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${definitiveItems.map(d => `
                                <tr>
                                    <td style="padding:5px; border:1px solid #bfdbfe;">${d.tech || '-'}</td>
                                    <td style="padding:5px; border:1px solid #bfdbfe;">${d.description || ''}</td>
                                    <td style="padding:5px; border:1px solid #bfdbfe;">${d.amount || '0.00'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    ${definitiveFinal ? `<div style="text-align:right; font-size:0.82rem; font-weight:700; margin-top:4px;">Total Definitivo: ${definitiveFinal}</div>` : ''}
                </div>
            `;
        }

        Swal.fire({
            width: '760px',
            title: `<h3 style="color:#0f172a; margin-bottom:0; font-size: 1.25rem;"><i class="bx bx-receipt"></i> Detalle Completo: ${r.receipt_code || ''}</h3>`,
            html: `
                <div style="text-align: left; font-family: 'Inter', sans-serif; font-size: 0.88rem; max-height: 70vh; overflow-y: auto; padding-right: 4px;">
                    <!-- Zona Privada Destacada Arriba -->
                    <div style="background: #fdf2f2; padding: 12px 15px; border-left: 4px solid #dc3545; border-radius: 6px; margin-bottom: 14px;">
                        <div style="color:#dc3545; font-weight:700; margin-bottom:6px; font-size:0.9rem;"><i class="bx bx-lock-alt"></i> Zona Privada de Taller</div>
                        <p style="margin: 0 0 6px 0;"><strong>Técnico(s) Encargado(s):</strong> ${displayTech}</p>
                        <p style="margin: 0;"><strong>Notas Privadas:</strong> <span style="white-space: pre-wrap; color: #333;">${displayNotes}</span></p>
                    </div>

                    <!-- Datos del Cliente -->
                    <div style="background: #f8fafc; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div><strong>Cliente:</strong> ${r.client_name || '-'}</div>
                        <div><strong>DNI / RIF:</strong> ${r.client_id_number || '-'}</div>
                        <div><strong>Teléfono:</strong> ${r.client_phone || '-'}</div>
                        <div><strong>Correo:</strong> ${r.client_email || 'No registrado'}</div>
                        <div style="grid-column: 1 / -1;"><strong>Dirección:</strong> ${r.client_address || '-'}</div>
                        ${r.delivery_time ? `<div style="grid-column: 1 / -1;"><strong>Tiempo estimado de diagnóstico:</strong> ${r.delivery_time}</div>` : ''}
                    </div>

                    <!-- Equipos Recibidos -->
                    <div>
                        <strong style="font-size:0.9rem; color:#0f172a;">Equipos Recibidos:</strong>
                        ${itemsHtml}
                    </div>

                    ${tentativeHtml}
                    ${definitiveHtml}

                    ${r.notes ? `
                        <div style="margin-top: 12px; background: #fffbeb; padding: 10px 12px; border-left: 4px solid #f59e0b; border-radius: 4px;">
                            <strong style="font-size:0.85rem;">Observaciones Públicas:</strong>
                            <div style="margin-top:4px; font-size:0.83rem;">${r.notes}</div>
                        </div>
                    ` : ''}
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Cerrar',
            confirmButtonColor: '#334155'
        });
    };

    document.addEventListener('click', function(e) {
        const privateBtn = e.target.closest('.btn-private-info');
        if (privateBtn) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.actions-dropdown').forEach(d => d.classList.remove('is-active'));
            
            const tech = privateBtn.getAttribute('data-tech');
            const notes = privateBtn.getAttribute('data-notes');
            const receiptRaw = privateBtn.getAttribute('data-receipt');
            
            window.showPrivateInfo(tech, notes, receiptRaw);
        }
    });
</script>

<?php include ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>