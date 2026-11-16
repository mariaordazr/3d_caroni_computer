<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<section class="content-panel">
    <?php
        if (!function_exists('getImageUrl')) {
            function getImageUrl($img) {
                if (strpos($img, 'http') === 0) {
                    if (preg_match('/(d\/|id=)([a-zA-Z0-9_-]+)/', $img, $matches)) {
                        return 'https://drive.google.com/thumbnail?id=' . $matches[2] . '&sz=w1000';
                    }
                    return $img;
                }
                return '/assets/uploads/receipts/' . $img;
            }
        }
        $isPeru = isset($isPeru) ? $isPeru : (strpos($_SERVER['HTTP_HOST'], 'corporaciongruponet.com') !== false);
        $currencySymbol = htmlspecialchars($moneySymbol ?? '$');
    ?>

    <form class="edit-form lvr-receipt-page" method="POST" action="/admin/receipts/update" id="receipt-form" enctype="multipart/form-data">

        <input type="hidden" name="id_receipt" value="<?php echo $receipt['id_receipt']; ?>">
        <input type="hidden" name="receipt_code" id="receipt_code" value="<?php echo htmlspecialchars($receipt['receipt_code']); ?>">
        <input type="hidden" name="old_client_name" id="old-client-name" value="<?php echo htmlspecialchars($receipt['client_name']); ?>">
        <input type="hidden" name="old_client_id_number" id="old-client-id" value="<?php echo htmlspecialchars($receipt['client_id_number']); ?>">

        <!-- TOPBAR: Breadcrumb + Título + Badge con código de la nota -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/receipts">Notas de Recibo</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Editar</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/receipts" class="lvr-back-btn" title="Volver a Notas de Recibo">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Editar Nota de Entrega</h1>
                </div>
                <span class="lvr-draft-badge"><?php echo htmlspecialchars($receipt['receipt_code']); ?></span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. DATOS DEL CLIENTE -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-user'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Datos del cliente</h3>
                                <p>Busca un cliente existente o actualiza sus datos</p>
                            </div>
                        </div>
                        <button type="button" id="btn-quick-edit-client" class="lvr-btn-soft-blue" onclick="openClientEditModal()" title="Editar cliente, contacto, calificación y observaciones">
                            <i class='bx bx-pencil'></i> Editar cliente
                        </button>
                    </div>

                    <?php
                        $fullDni = $receipt['client_id_number'] ?? '';
                        $prefix = 'V';
                        $digits = preg_replace('/[^0-9]/', '', $fullDni);
                        if (preg_match('/^([VEJPGvejpg])/', $fullDni, $matches)) {
                            $prefix = strtoupper($matches[1]);
                        }
                    ?>

                    <div class="lvr-fields-grid-2">
                        <!-- Identificación -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label">Identificación (DNI / RIF / Pasaporte)</label>
                            <?php if ($isPeru): ?>
                                <input type="text" name="client_id_digits" id="input-dni-digits" class="lvr-input" value="<?php echo htmlspecialchars($digits); ?>" maxlength="20" placeholder="Ej: 71234568 o 20123456789" required>
                                <input type="hidden" name="client_id_prefix" id="input-dni-prefix" value="">
                            <?php else: ?>
                                <div class="lvr-dni-combo">
                                    <select name="client_id_prefix" id="input-dni-prefix" class="lvr-select lvr-dni-prefix">
                                        <option value="V" <?php echo $prefix === 'V' ? 'selected' : ''; ?>>V</option>
                                        <option value="E" <?php echo $prefix === 'E' ? 'selected' : ''; ?>>E</option>
                                        <option value="J" <?php echo $prefix === 'J' ? 'selected' : ''; ?>>J</option>
                                        <option value="P" <?php echo $prefix === 'P' ? 'selected' : ''; ?>>P</option>
                                        <option value="G" <?php echo $prefix === 'G' ? 'selected' : ''; ?>>G</option>
                                    </select>
                                    <input type="text" name="client_id_digits" id="input-dni-digits" class="lvr-input" value="<?php echo htmlspecialchars($digits); ?>" maxlength="15" pattern="[0-9]+" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="12345678" required style="flex: 1;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Teléfono WhatsApp -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-phone'></i> Teléfono WhatsApp</label>
                            <input type="tel" id="phone_input" name="phone_input_raw" class="lvr-input" value="<?php echo htmlspecialchars($receipt['client_phone']); ?>" required>
                            <input type="hidden" name="client_phone" id="client_phone_hidden" value="<?php echo htmlspecialchars($receipt['client_phone']); ?>">
                        </div>

                        <!-- Nombre o Razón Social -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label">Nombre o Razón Social</label>
                            <input type="text" name="client_name" id="input-name" class="lvr-input" list="clients-datalist" value="<?php echo htmlspecialchars($receipt['client_name'] ?? ''); ?>" autocomplete="off" placeholder="Escriba o seleccione un cliente..." required>

                            <!-- Barra informativa interna de Estrellas y Adjetivo del cliente -->
                            <div id="client-Quick-info" style="display: none; align-items: center; gap: 8px; margin-top: 4px; padding: 5px 10px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; font-size: 0.78rem;">
                                <span id="client-stars-display" style="color: #f59e0b; font-size: 0.95rem; display: inline-flex; gap: 2px;"></span>
                                <span id="client-notes-display" style="color: #92400e; font-weight: 600;"></span>
                            </div>

                            <datalist id="clients-datalist">
                                <?php if (!empty($clientsList)): foreach ($clientsList as $clientOption): ?>
                                    <option value="<?php echo htmlspecialchars($clientOption['client_name'] . ' - ' . $clientOption['client_id_number']); ?>"></option>
                                <?php endforeach; endif; ?>
                            </datalist>
                        </div>

                        <!-- Correo electrónico -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-envelope'></i> Correo electrónico</label>
                            <input type="email" name="client_email" id="input-email" class="lvr-input" value="<?php echo htmlspecialchars($receipt['client_email'] ?? ''); ?>" placeholder="cliente@correo.com">
                        </div>

                        <!-- Dirección -->
                        <div class="lvr-field-group lvr-field-full">
                            <label class="lvr-field-label"><i class='bx bx-map'></i> Dirección</label>
                            <input type="text" name="client_address" id="input-address" class="lvr-input" value="<?php echo htmlspecialchars(!empty(trim($receipt['client_address'] ?? '')) ? $receipt['client_address'] : 'Puerto Ordaz'); ?>" placeholder="Puerto Ordaz">
                        </div>
                    </div>
                </div>

                <!-- 2. ARTÍCULOS A RECIBIR -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-cube'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Artículos a recibir</h3>
                                <p id="lvr-items-subtitle">1 equipo registrado</p>
                            </div>
                        </div>
                        <button type="button" id="add-row-btn" class="lvr-btn-soft-blue js-add-item-btn">
                            <i class='bx bx-plus'></i> Añadir artículo
                        </button>
                    </div>

                    <div id="items-body" class="lvr-items-cards-list">
                        <?php if (!empty($items)): ?>
                            <?php foreach($items as $index => $item): ?>
                                <?php $isItemDeliv = !empty($item['delivered']); ?>
                                <div class="lvr-item-card item-row-entry">
                                    <div class="lvr-item-card-top">
                                        <div class="lvr-item-badge-group">
                                            <span class="lvr-item-badge">Artículo #<span class="item-index-num"><?php echo $index + 1; ?></span></span>
                                            <input type="hidden" name="item_delivered[]" class="item-delivered-hidden" value="<?php echo $isItemDeliv ? '1' : '0'; ?>">
                                            <div class="item-delivered-label <?php echo $isItemDeliv ? 'is-delivered' : ''; ?>" style="display: <?php echo $isItemDeliv ? 'inline-flex' : 'none'; ?>; color: #15803d; background: #dcfce7; border: 1px solid #86efac; padding: 2px 9px; border-radius: 999px; font-size: 0.74rem; font-weight: 700; align-items: center; gap: 4px;">
                                                <i class='bx bxs-check-circle'></i> Equipo entregado
                                            </div>
                                        </div>
                                        <div class="lvr-item-actions">
                                            <button type="button" class="lvr-icon-btn toggle-item-delivered <?php echo !empty($isItemDeliv) ? 'delivered-active' : ''; ?>" onclick="window.toggleItemDelivered(this, event)" title="Marcar/desmarcar como equipo entregado">
                                                <i class='bx <?php echo !empty($isItemDeliv) ? 'bxs-check-circle' : 'bx-package'; ?>'></i>
                                            </button>
                                            <button type="button" class="lvr-icon-btn duplicate-item-btn" title="Duplicar artículo">
                                                <i class='bx bx-copy'></i>
                                            </button>
                                            <button type="button" class="lvr-icon-btn danger remove-row" <?php echo (count($items) == 1 && $index == 0) ? 'disabled' : ''; ?> title="Eliminar artículo">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Fila superior: Und, Modelo y S/N -->
                                    <div class="lvr-item-grid-top">
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Und.</label>
                                            <input type="number" name="und[]" class="lvr-input und-input" value="<?php echo htmlspecialchars($item['und'] ?? '1'); ?>" min="1" required>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Modelo</label>
                                            <input type="text" name="model[]" class="lvr-input" value="<?php echo htmlspecialchars($item['model'] ?? ''); ?>" placeholder="Ej: CPU A520" required>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">S/N</label>
                                            <input type="text" name="serial[]" class="lvr-input" value="<?php echo htmlspecialchars($item['serial'] ?? ''); ?>" placeholder="Número de serie" style="font-family: monospace !important;">
                                        </div>
                                    </div>

                                    <!-- Filas individuales de 2 líneas de alto para Complementos, Detalles físicos y Características -->
                                    <div class="lvr-item-stack-bottom">
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Complementos</label>
                                            <textarea name="accessories[]" class="lvr-textarea-2lines" rows="2" placeholder="Cargador, cables, bolso..."><?php echo htmlspecialchars($item['accessories'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Detalles físicos</label>
                                            <textarea name="details[]" class="lvr-textarea-2lines" rows="2" placeholder="Rayones en carcasa, golpes, tornillos faltantes..."><?php echo htmlspecialchars($item['details'] ?? ''); ?></textarea>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Características y fallas</label>
                                            <textarea name="features[]" class="lvr-textarea-2lines" rows="2" placeholder="No enciende, pantalla parpadea, revisión general..."><?php echo htmlspecialchars($item['features'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="lvr-item-card item-row-entry">
                                <div class="lvr-item-card-top">
                                    <div class="lvr-item-badge-group">
                                        <span class="lvr-item-badge">Artículo #<span class="item-index-num">1</span></span>
                                        <input type="hidden" name="item_delivered[]" class="item-delivered-hidden" value="0">
                                        <div class="item-delivered-label" style="display: none; color: #0284c7; font-size: 0.74rem; font-weight: 700; align-items: center; gap: 3px;">
                                            <i class='bx bxs-check-circle'></i> Equipo entregado
                                        </div>
                                    </div>
                                    <div class="lvr-item-actions">
                                        <button type="button" class="lvr-icon-btn toggle-item-delivered" onclick="toggleItemDelivered(this)" title="Marcar este equipo como entregado">
                                            <i class='bx bx-package'></i>
                                        </button>
                                        <button type="button" class="lvr-icon-btn duplicate-item-btn" title="Duplicar artículo">
                                            <i class='bx bx-copy'></i>
                                        </button>
                                        <button type="button" class="lvr-icon-btn danger remove-row" disabled title="Eliminar artículo">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="lvr-item-grid-top">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Und.</label>
                                        <input type="number" name="und[]" class="lvr-input und-input" value="1" min="1" required>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Modelo</label>
                                        <input type="text" name="model[]" class="lvr-input" placeholder="Ej: CPU A520" required>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">S/N</label>
                                        <input type="text" name="serial[]" class="lvr-input" placeholder="Número de serie" style="font-family: monospace !important;">
                                    </div>
                                </div>
                                <div class="lvr-item-stack-bottom">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Complementos</label>
                                        <textarea name="accessories[]" class="lvr-textarea-2lines" rows="2" placeholder="Cargador, cables, bolso..."></textarea>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Detalles físicos</label>
                                        <textarea name="details[]" class="lvr-textarea-2lines" rows="2" placeholder="Rayones en carcasa, golpes, tornillos faltantes..."></textarea>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Características y fallas</label>
                                        <textarea name="features[]" class="lvr-textarea-2lines" rows="2" placeholder="No enciende, pantalla parpadea, revisión general..."></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botón inferior para añadir artículo -->
                    <div class="lvr-bottom-add-wrap">
                        <button type="button" class="lvr-btn-add-bottom js-add-item-btn">
                            <i class='bx bx-plus'></i> Añadir artículo
                        </button>
                    </div>

                    <!-- Tiempo estimado de diagnóstico -->
                    <?php $currentDelivTime = trim($receipt['delivery_time'] ?? ''); ?>
                    <div class="lvr-field-group" style="margin-top: 1.15rem;">
                        <label class="lvr-field-label"><i class='bx bx-time-five'></i> Tiempo estimado de diagnóstico</label>
                        <input type="text" name="delivery_time" id="delivery_time_input" class="lvr-input" placeholder="Ej: 24 a 48 horas hábiles / 3 días hábiles" value="<?php echo htmlspecialchars($currentDelivTime); ?>">
                        <div class="lvr-delivery-chips">
                            <button type="button" class="lvr-chip-btn <?php echo $currentDelivTime === '24 horas' ? 'is-active' : ''; ?>" data-time="24 horas">24 horas</button>
                            <button type="button" class="lvr-chip-btn <?php echo $currentDelivTime === '48 horas' ? 'is-active' : ''; ?>" data-time="48 horas">48 horas</button>
                            <button type="button" class="lvr-chip-btn <?php echo $currentDelivTime === '3 días hábiles' ? 'is-active' : ''; ?>" data-time="3 días hábiles">3 días hábiles</button>
                            <button type="button" class="lvr-chip-btn <?php echo $currentDelivTime === '1 semana' ? 'is-active' : ''; ?>" data-time="1 semana">1 semana</button>
                        </div>
                    </div>
                </div>

                <!-- 3. PRESUPUESTOS -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-calculator'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Presupuestos</h3>
                                <p>El tentativo se informa al recibir; el definitivo tras el diagnóstico</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3.1 Cuerpo de Presupuesto Tentativo -->
                    <div id="tentative-budget-body" class="lvr-budget-rows-list">
                        <?php if (!empty($tentativeBudget)): ?>
                            <?php foreach($tentativeBudget as $index => $item): ?>
                                <?php 
                                    $isRange = strpos($item['amount'] ?? '', '-') !== false;
                                    $minVal = ''; $maxVal = ''; $singleVal = '';
                                    if ($isRange) {
                                        $parts = explode('-', $item['amount']);
                                        $minVal = trim($parts[0]);
                                        $maxVal = trim($parts[1] ?? '');
                                    } else {
                                        $singleVal = trim($item['amount'] ?? '');
                                    }
                                ?>
                                <div class="lvr-budget-item-card budget-row-entry">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Técnico (Interno)</label>
                                        <input type="text" name="tentative_tech[]" class="lvr-input" value="<?php echo htmlspecialchars($item['tech'] ?? ''); ?>" placeholder="Ej: Samantha">
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Descripción del servicio</label>
                                        <textarea name="tentative_description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="lvr-budget-row-grid">
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Tipo</label>
                                            <select class="lvr-select budget-type">
                                                <option value="single" <?php echo !$isRange ? 'selected' : ''; ?>>Valor Único</option>
                                                <option value="range" <?php echo $isRange ? 'selected' : ''; ?>>Rango</option>
                                            </select>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Monto (<?php echo $currencySymbol; ?>)</label>
                                            <div class="single-container" style="display: <?php echo $isRange ? 'none' : 'block'; ?>;">
                                                <input type="text" class="lvr-input amount-input amount-single" value="<?php echo htmlspecialchars($singleVal); ?>" placeholder="0.00" style="text-align: right;">
                                            </div>
                                            <div class="range-container" style="display: <?php echo $isRange ? 'flex' : 'none'; ?>; align-items: center; gap: 6px;">
                                                <input type="text" class="lvr-input amount-input amount-min" value="<?php echo htmlspecialchars($minVal); ?>" placeholder="0.00" style="text-align: right; flex: 1;">
                                                <span style="color: #64748b; font-weight: 700;">-</span>
                                                <input type="text" class="lvr-input amount-input amount-max" value="<?php echo htmlspecialchars($maxVal); ?>" placeholder="0.00" style="text-align: right; flex: 1;">
                                            </div>
                                            <input type="hidden" name="tentative_amount[]" class="hidden-amount" value="<?php echo htmlspecialchars($item['amount'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="lvr-budget-item-actions">
                                        <button type="button" class="lvr-icon-btn danger remove-budget-row" <?php echo (count($tentativeBudget) <= 1) ? 'disabled' : ''; ?> title="Eliminar línea">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="lvr-budget-item-card budget-row-entry">
                                <div class="lvr-field-group">
                                    <label class="lvr-field-label">Técnico (Interno)</label>
                                    <input type="text" name="tentative_tech[]" class="lvr-input" placeholder="Ej: Samantha">
                                </div>
                                <div class="lvr-field-group">
                                    <label class="lvr-field-label">Descripción del servicio</label>
                                    <textarea name="tentative_description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."></textarea>
                                </div>
                                <div class="lvr-budget-row-grid">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Tipo</label>
                                        <select class="lvr-select budget-type">
                                            <option value="single">Valor Único</option>
                                            <option value="range">Rango</option>
                                        </select>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Monto (<?php echo $currencySymbol; ?>)</label>
                                        <div class="single-container">
                                            <input type="text" class="lvr-input amount-input amount-single" placeholder="0.00" style="text-align: right;">
                                        </div>
                                        <div class="range-container" style="display: none; align-items: center; gap: 6px;">
                                            <input type="text" class="lvr-input amount-input amount-min" placeholder="0.00" style="text-align: right; flex: 1;">
                                            <span style="color: #64748b; font-weight: 700;">-</span>
                                            <input type="text" class="lvr-input amount-input amount-max" placeholder="0.00" style="text-align: right; flex: 1;">
                                        </div>
                                        <input type="hidden" name="tentative_amount[]" class="hidden-amount" value="">
                                    </div>
                                </div>
                                <div class="lvr-budget-item-actions">
                                    <button type="button" class="lvr-icon-btn danger remove-budget-row" disabled title="Eliminar línea">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3.2 Cuerpo de Presupuesto Definitivo -->
                    <div id="budget-body" class="lvr-budget-rows-list">
                        <?php if (!empty($budget)): ?>
                            <?php foreach($budget as $index => $item): ?>
                                <?php 
                                    $isRange = strpos($item['amount'] ?? '', '-') !== false;
                                    $minVal = ''; $maxVal = ''; $singleVal = '';
                                    if ($isRange) {
                                        $parts = explode('-', $item['amount']);
                                        $minVal = trim($parts[0]);
                                        $maxVal = trim($parts[1] ?? '');
                                    } else {
                                        $singleVal = trim($item['amount'] ?? '');
                                    }
                                    $cChoice = $item['client_choice'] ?? '';
                                    $askActive = !empty($item['ask_client']);
                                ?>
                                <div class="lvr-budget-item-card budget-row-entry">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Técnico (Interno)</label>
                                        <input type="text" name="budget_tech[]" class="lvr-input" value="<?php echo htmlspecialchars($item['tech'] ?? ''); ?>" placeholder="Ej: Samantha">
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Descripción del servicio</label>
                                        <textarea name="description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="lvr-budget-row-grid">
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Tipo</label>
                                            <select class="lvr-select budget-type">
                                                <option value="single" <?php echo !$isRange ? 'selected' : ''; ?>>Valor Único</option>
                                                <option value="range" <?php echo $isRange ? 'selected' : ''; ?>>Rango</option>
                                            </select>
                                        </div>
                                        <div class="lvr-field-group">
                                            <label class="lvr-field-label">Monto (<?php echo $currencySymbol; ?>)</label>
                                            <div class="single-container" style="display: <?php echo $isRange ? 'none' : 'block'; ?>;">
                                                <input type="text" class="lvr-input amount-input amount-single" value="<?php echo htmlspecialchars($singleVal); ?>" placeholder="0.00" style="text-align: right;">
                                            </div>
                                            <div class="range-container" style="display: <?php echo $isRange ? 'flex' : 'none'; ?>; align-items: center; gap: 6px;">
                                                <input type="text" class="lvr-input amount-input amount-min" value="<?php echo htmlspecialchars($minVal); ?>" placeholder="0.00" style="text-align: right; flex: 1;">
                                                <span style="color: #64748b; font-weight: 700;">-</span>
                                                <input type="text" class="lvr-input amount-input amount-max" value="<?php echo htmlspecialchars($maxVal); ?>" placeholder="0.00" style="text-align: right; flex: 1;">
                                            </div>
                                            <input type="hidden" name="amount[]" class="hidden-amount" value="<?php echo htmlspecialchars($item['amount'] ?? '0.00'); ?>">
                                        </div>
                                    </div>
                                    <div class="lvr-budget-item-actions">
                                        <input type="hidden" name="budget_client_choice[]" value="<?php echo htmlspecialchars($cChoice); ?>">
                                        <label class="lvr-ask-pill-btn <?php echo $askActive ? 'is-active' : ''; ?>">
                                            <input type="hidden" name="budget_ask[]" class="ask-hidden" value="<?php echo $askActive ? '1' : '0'; ?>">
                                            <input type="checkbox" class="ask-checkbox" style="display: none;" <?php echo $askActive ? 'checked' : ''; ?> onchange="this.previousElementSibling.value = this.checked ? '1' : '0'; this.closest('.lvr-ask-pill-btn').classList.toggle('is-active', this.checked);">
                                            <i class='bx bx-message-rounded-dots'></i>
                                            <span>Preguntar</span>
                                        </label>
                                        <?php if ($askActive): ?>
                                            <span style="font-size: 0.75rem; font-weight: 700;">
                                                <?php if ($cChoice === 'accepted'): ?>
                                                    <span style="color: #16a34a;">✓ Aceptado</span>
                                                <?php elseif ($cChoice === 'rejected'): ?>
                                                    <span style="color: #dc2626;">✕ Rechazado</span>
                                                <?php else: ?>
                                                    <span style="color: #64748b;">⏳ Sin respuesta</span>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <button type="button" class="lvr-icon-btn danger remove-budget-row" <?php echo (count($budget) <= 1) ? 'disabled' : ''; ?> title="Eliminar línea">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="lvr-budget-item-card budget-row-entry">
                                <div class="lvr-field-group">
                                    <label class="lvr-field-label">Técnico (Interno)</label>
                                    <input type="text" name="budget_tech[]" class="lvr-input" placeholder="Ej: Samantha">
                                </div>
                                <div class="lvr-field-group">
                                    <label class="lvr-field-label">Descripción del servicio</label>
                                    <textarea name="description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."></textarea>
                                </div>
                                <div class="lvr-budget-row-grid">
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Tipo</label>
                                        <select class="lvr-select budget-type">
                                            <option value="single">Valor Único</option>
                                            <option value="range">Rango</option>
                                        </select>
                                    </div>
                                    <div class="lvr-field-group">
                                        <label class="lvr-field-label">Monto (<?php echo $currencySymbol; ?>)</label>
                                        <div class="single-container">
                                            <input type="text" class="lvr-input amount-input amount-single" placeholder="0.00" style="text-align: right;">
                                        </div>
                                        <div class="range-container" style="display: none; align-items: center; gap: 6px;">
                                            <input type="text" class="lvr-input amount-input amount-min" placeholder="0.00" style="text-align: right; flex: 1;">
                                            <span style="color: #64748b; font-weight: 700;">-</span>
                                            <input type="text" class="lvr-input amount-input amount-max" placeholder="0.00" style="text-align: right; flex: 1;">
                                        </div>
                                        <input type="hidden" name="amount[]" class="hidden-amount" value="0.00">
                                    </div>
                                </div>
                                <div class="lvr-budget-item-actions">
                                    <input type="hidden" name="budget_client_choice[]" value="">
                                    <label class="lvr-ask-pill-btn">
                                        <input type="hidden" name="budget_ask[]" class="ask-hidden" value="0">
                                        <input type="checkbox" class="ask-checkbox" style="display: none;" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'; this.closest('.lvr-ask-pill-btn').classList.toggle('is-active', this.checked);">
                                        <i class='bx bx-message-rounded-dots'></i>
                                        <span>Preguntar</span>
                                    </label>
                                    <button type="button" class="lvr-icon-btn danger remove-budget-row" disabled title="Eliminar línea">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 4. IMÁGENES DEL EQUIPO (Recepción + Proceso) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-image-add'></i>
                                <span class="lvr-step-num">4</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Imágenes del equipo</h3>
                                <p>Opcional · Gestiona las fotos de recepción y del proceso de reparación</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-photos-dual-wrapper">
                        <!-- 4.1 Fotos Iniciales (Recepción) -->
                        <div class="lvr-photo-category-block" id="initial-images-container">
                            <div class="lvr-photo-category-header">
                                <div class="lvr-photo-category-title">
                                    <i class='bx bx-camera'></i>
                                    <span>Fotos Iniciales (Recepción)</span>
                                </div>
                                <span class="lvr-photo-category-badge initial">Al recibir</span>
                            </div>

                            <input type="hidden" name="initial_image_order" id="initial_image_order" value="">
                            <div class="image-manager">
                                <div class="image-slots-container dynamic-slots-container receipt-gallery" 
                                     id="receipt-slots-initial"
                                     data-input-name="receipt_images[]" 
                                     data-delete-name="delete_receipt_images[]" 
                                     data-order-input="initial_image_order">
                                    
                                    <?php $existingImages = json_decode($receipt['images_json'] ?? '[]', true) ?: []; ?>
                                    <?php foreach ($existingImages as $img): ?>
                                        <div class="image-slot has-preview dynamic-slot" draggable="true">
                                            <label style="width: 100%; height: 100%; display: block; margin: 0;">
                                                <div class="image-preview-frame">
                                                    <img src="<?php echo htmlspecialchars(getImageUrl($img)); ?>" class="image-preview">
                                                    <button type="button" class="remove-preview-btn existing-image" data-image-name="<?php echo htmlspecialchars($img); ?>"><i class='bx bx-x'></i></button>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="image-slot dynamic-slot">
                                        <label style="width: 100%; height: 100%; display: block; margin: 0; cursor: pointer;">
                                            <div class="image-placeholder desktop-view lvr-upload-placeholder-content">
                                                <i class='bx bx-plus'></i>
                                                <span>Subir</span>
                                            </div>
                                            <div class="image-placeholder mobile-view mobile-split">
                                                <div class="upload-btn camera-btn" data-capture="environment"><i class='bx bx-camera'></i><span class="btn-text">Cámara</span></div>
                                                <div class="upload-btn gallery-btn" data-capture=""><i class='bx bx-image-add'></i><span class="btn-text">Galería</span></div>
                                            </div>
                                            <input type="file" name="receipt_images[]" accept="image/*" multiple style="display:none;">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4.2 Fotos del Proceso / Reparación -->
                        <div class="lvr-photo-category-block" id="process-images-container">
                            <div class="lvr-photo-category-header">
                                <div class="lvr-photo-category-title">
                                    <i class='bx bx-wrench'></i>
                                    <span>Fotos del Proceso / Reparación</span>
                                </div>
                                <span class="lvr-photo-category-badge process">Durante el servicio</span>
                            </div>

                            <input type="hidden" name="process_image_order" id="process_image_order" value="">
                            <div class="image-manager">
                                <div class="image-slots-container dynamic-slots-container receipt-gallery" 
                                     id="receipt-slots-process"
                                     data-input-name="process_images[]" 
                                     data-delete-name="delete_process_images[]" 
                                     data-order-input="process_image_order">
                                    
                                    <?php $processImages = json_decode($receipt['process_images_json'] ?? '[]', true) ?: []; ?>
                                    <?php foreach ($processImages as $img): ?>
                                        <div class="image-slot has-preview dynamic-slot" draggable="true">
                                            <label style="width: 100%; height: 100%; display: block; margin: 0;">
                                                <div class="image-preview-frame">
                                                    <img src="<?php echo htmlspecialchars(getImageUrl($img)); ?>" class="image-preview">
                                                    <button type="button" class="remove-preview-btn existing-image" data-image-name="<?php echo htmlspecialchars($img); ?>"><i class='bx bx-x'></i></button>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="image-slot dynamic-slot">
                                        <label style="width: 100%; height: 100%; display: block; margin: 0; cursor: pointer;">
                                            <div class="image-placeholder desktop-view lvr-upload-placeholder-content">
                                                <i class='bx bx-plus'></i>
                                                <span>Subir</span>
                                            </div>
                                            <div class="image-placeholder mobile-view mobile-split">
                                                <div class="upload-btn camera-btn" data-capture="environment"><i class='bx bx-camera'></i><span class="btn-text">Cámara</span></div>
                                                <div class="upload-btn gallery-btn" data-capture=""><i class='bx bx-image-add'></i><span class="btn-text">Galería</span></div>
                                            </div>
                                            <input type="file" name="process_images[]" accept="image/*" multiple style="display:none;">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. OBSERVACIONES ADICIONALES -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-edit'></i>
                                <span class="lvr-step-num">5</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Observaciones adicionales</h3>
                                <p>Opcional · Visible para el cliente en la nota</p>
                            </div>
                        </div>
                    </div>

                    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
                    <div class="lvr-quill-wrapper">
                        <div id="notes-quill-editor"><?php echo $receipt['notes'] ?? ''; ?></div>
                    </div>
                    <input type="hidden" name="notes" id="notes-hidden-input" value="<?php echo htmlspecialchars($receipt['notes'] ?? ''); ?>">
                </div>

                <!-- ZONA PRIVADA -->
                <div class="lvr-private-card">
                    <div class="lvr-step-header" style="margin-bottom: 0.9rem;">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Zona privada</h3>
                                <p>Solo uso interno · no se muestra al cliente</p>
                            </div>
                        </div>
                    </div>
                    <textarea name="private_notes" class="lvr-textarea" rows="3" placeholder="Contraseñas, PIN, diagnósticos internos, etc."><?php echo htmlspecialchars($receipt['private_notes'] ?? ''); ?></textarea>

                    <div style="margin-top: 1rem; font-size: 0.78rem; color: #991b1b; border-top: 1px dashed #fca5a5; padding-top: 10px; display: flex; flex-wrap: wrap; gap: 1rem;">
                        <span><strong>Creado por:</strong> <?php echo htmlspecialchars($creator['name'] ?? 'Desconocido'); ?></span>
                        <span><strong>Última edición por:</strong> <?php echo htmlspecialchars($updater['name'] ?? 'No ha sido editado'); ?></span>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-row">
                        <span>Cliente</span>
                        <strong id="summary-client-name"><?php echo !empty($receipt['client_name']) ? htmlspecialchars($receipt['client_name']) : '—'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Artículos</span>
                        <strong id="summary-items-count"><?php echo !empty($items) ? count($items) : 1; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Imágenes</span>
                        <strong id="summary-images-count"><?php echo count($existingImages) + count($processImages); ?></strong>
                    </div>

                    <div class="lvr-summary-pill tentative">
                        <span>Tentativo</span>
                        <strong id="summary-tentative-total"><?php echo $currencySymbol; ?> 0.00</strong>
                    </div>

                    <div class="lvr-summary-pill definitive">
                        <span>Monto final</span>
                        <strong id="summary-final-total"><?php echo $currencySymbol; ?> 0.00</strong>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar nota
                    </button>

                    <a href="/admin/receipts" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>


<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    const CURRENCY_SYMBOL = <?php echo json_encode($moneySymbol ?? '$'); ?>;

    // ============================================================================
    // SINCRONIZACIÓN EN TIEMPO REAL DEL PANEL RESUMEN Y ARTÍCULOS
    // ============================================================================
    function updateSummaryPanel() {
        const nameInput = document.getElementById('input-name');
        const summaryClient = document.getElementById('summary-client-name');
        if (nameInput && summaryClient) {
            const val = nameInput.value.trim();
            summaryClient.textContent = val !== '' ? val : '—';
        }

        const itemsContainer = document.getElementById('items-body');
        if (itemsContainer) {
            const cards = itemsContainer.querySelectorAll('.item-row-entry');
            let totalUnits = 0;
            cards.forEach((card, idx) => {
                const badgeNum = card.querySelector('.item-index-num');
                if (badgeNum) badgeNum.textContent = idx + 1;

                const removeBtn = card.querySelector('.remove-row');
                if (removeBtn) removeBtn.disabled = (cards.length <= 1);

                const undInput = card.querySelector('.und-input');
                totalUnits += parseInt(undInput ? undInput.value : 1) || 1;
            });

            const summaryItems = document.getElementById('summary-items-count');
            if (summaryItems) summaryItems.textContent = totalUnits;

            const subtitle = document.getElementById('lvr-items-subtitle');
            if (subtitle) {
                subtitle.textContent = cards.length === 1 ? '1 equipo registrado' : `${cards.length} equipos registrados`;
            }
        }

        const previewSlots = document.querySelectorAll('.lvr-receipt-page .image-slot.has-preview');
        const summaryImages = document.getElementById('summary-images-count');
        if (summaryImages) summaryImages.textContent = previewSlots.length;
    }

    // ============================================================================
    // GESTIÓN DE TARJETAS DE ARTÍCULOS (AÑADIR, DUPLICAR, ELIMINAR, ENTREGADO)
    // ============================================================================
    window.toggleItemDelivered = function(btn, evt) {
        if (evt) {
            evt.preventDefault();
            evt.stopPropagation();
            if (typeof evt.stopImmediatePropagation === 'function') {
                evt.stopImmediatePropagation();
            }
        }

        if (!btn) return;

        // Candado anti-doble disparo (evita que onclick + listener inviertan el estado 2 veces)
        const now = Date.now();
        if (btn.dataset.lastToggle && (now - parseInt(btn.dataset.lastToggle, 10)) < 80) {
            return;
        }
        btn.dataset.lastToggle = String(now);

        const card = btn.closest('.item-row-entry, .lvr-item-card, tr');
        if (!card) return;

        const hiddenInput = card.querySelector('.item-delivered-hidden, input[name="item_delivered[]"]');
        const label = card.querySelector('.item-delivered-label');
        const icon = btn.querySelector('i');
        if (!hiddenInput) return;

        const isDelivered = String(hiddenInput.value).trim() === '1';

        if (isDelivered) {
            // Pasar a NO ENTREGADO (0)
            hiddenInput.value = '0';
            if (label) {
                label.classList.remove('is-delivered');
                label.style.setProperty('display', 'none', 'important');
            }
            card.classList.remove('is-delivered-card');
            card.style.removeProperty('border-color');
            card.style.removeProperty('background-color');
            btn.classList.remove('delivered-active');
            btn.style.removeProperty('color');
            btn.style.removeProperty('background-color');
            btn.style.removeProperty('border-color');
            if (icon) icon.className = 'bx bx-package';
        } else {
            // Pasar a EQUIPO ENTREGADO (1)
            hiddenInput.value = '1';
            if (label) {
                label.classList.add('is-delivered');
                label.style.setProperty('display', 'inline-flex', 'important');
                label.style.setProperty('align-items', 'center', 'important');
                label.style.setProperty('gap', '4px', 'important');
                label.style.setProperty('color', '#15803d', 'important');
                label.style.setProperty('background', '#dcfce7', 'important');
                label.style.setProperty('border', '1px solid #86efac', 'important');
                label.style.setProperty('padding', '2px 9px', 'important');
                label.style.setProperty('border-radius', '999px', 'important');
                label.style.setProperty('font-size', '0.74rem', 'important');
                label.style.setProperty('font-weight', '700', 'important');
            }
            card.classList.add('is-delivered-card');
            card.style.setProperty('border-color', '#86efac', 'important');
            card.style.setProperty('background-color', '#f0fdf4', 'important');
            btn.classList.add('delivered-active');
            btn.style.setProperty('color', '#15803d', 'important');
            btn.style.setProperty('background-color', '#dcfce7', 'important');
            btn.style.setProperty('border-color', '#86efac', 'important');
            if (icon) icon.className = 'bx bxs-check-circle';
        }
    };

    function createItemCardElement(values = {}) {
        const isDeliv = String(values.delivered || '0') === '1';
        const div = document.createElement('div');
        div.className = 'lvr-item-card item-row-entry' + (isDeliv ? ' is-delivered-card' : '');
        if (isDeliv) {
            div.style.setProperty('border-color', '#86efac', 'important');
            div.style.setProperty('background-color', '#f0fdf4', 'important');
        }
        div.innerHTML = `
            <div class="lvr-item-card-top">
                <div class="lvr-item-badge-group">
                    <span class="lvr-item-badge">Artículo #<span class="item-index-num">1</span></span>
                    <input type="hidden" name="item_delivered[]" class="item-delivered-hidden" value="${isDeliv ? '1' : '0'}">
                    <div class="item-delivered-label ${isDeliv ? 'is-delivered' : ''}" style="display: ${isDeliv ? 'inline-flex' : 'none'} !important; color: #15803d; background: #dcfce7; border: 1px solid #86efac; padding: 2px 9px; border-radius: 999px; font-size: 0.74rem; font-weight: 700; align-items: center; gap: 4px;">
                        <i class='bx bxs-check-circle'></i> Equipo entregado
                    </div>
                </div>
                <div class="lvr-item-actions">
                    <button type="button" class="lvr-icon-btn toggle-item-delivered ${isDeliv ? 'delivered-active' : ''}" onclick="window.toggleItemDelivered(this, event)" style="${isDeliv ? 'color: #15803d !important; background-color: #dcfce7 !important; border-color: #86efac !important;' : ''}" title="Marcar este equipo como entregado">
                        <i class='bx ${isDeliv ? 'bxs-check-circle' : 'bx-package'}'></i>
                    </button>
                    <button type="button" class="lvr-icon-btn duplicate-item-btn" title="Duplicar artículo">
                        <i class='bx bx-copy'></i>
                    </button>
                    <button type="button" class="lvr-icon-btn danger remove-row" title="Eliminar artículo">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </div>
            <div class="lvr-item-grid-top">
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Und.</label>
                    <input type="number" name="und[]" class="lvr-input und-input" value="${values.und || '1'}" min="1" required>
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Modelo</label>
                    <input type="text" name="model[]" class="lvr-input" placeholder="Ej: CPU A520" value="${values.model || ''}" required>
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">S/N</label>
                    <input type="text" name="serial[]" class="lvr-input" placeholder="Número de serie" value="${values.serial || ''}" style="font-family: monospace !important;">
                </div>
            </div>
            <div class="lvr-item-stack-bottom">
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Complementos</label>
                    <textarea name="accessories[]" class="lvr-textarea-2lines" rows="2" placeholder="Cargador, cables, bolso...">${values.accessories || ''}</textarea>
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Detalles físicos</label>
                    <textarea name="details[]" class="lvr-textarea-2lines" rows="2" placeholder="Rayones en carcasa, golpes, tornillos faltantes...">${values.details || ''}</textarea>
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Características y fallas</label>
                    <textarea name="features[]" class="lvr-textarea-2lines" rows="2" placeholder="No enciende, pantalla parpadea, revisión general...">${values.features || ''}</textarea>
                </div>
            </div>
        `;
        return div;
    }

    // Delegación global en document (fase de captura) para que ni crud.js ni PJAX puedan romper los botones
    if (!window._lvrItemsDelegationBound) {
        window._lvrItemsDelegationBound = true;

        document.addEventListener('click', function(e) {
            const itemsBody = document.getElementById('items-body');
            if (!itemsBody) return;

            // 1. Botón de Equipo Entregado (Paquete)
            const delivBtn = e.target.closest('.toggle-item-delivered');
            if (delivBtn && itemsBody.contains(delivBtn)) {
                window.toggleItemDelivered(delivBtn, e);
                return;
            }

            // 2. Botón de Añadir Artículo (Superior e Inferior)
            const addBtn = e.target.closest('.js-add-item-btn');
            if (addBtn) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
                const newCard = createItemCardElement();
                itemsBody.appendChild(newCard);
                updateSummaryPanel();
                return;
            }

            // 3. Botón de Eliminar Artículo
            const removeBtn = e.target.closest('.remove-row');
            if (removeBtn && itemsBody.contains(removeBtn)) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
                const cards = itemsBody.querySelectorAll('.item-row-entry');
                if (cards.length > 1) {
                    removeBtn.closest('.item-row-entry').remove();
                    updateSummaryPanel();
                }
                return;
            }

            // 4. Botón de Duplicar Artículo
            const dupBtn = e.target.closest('.duplicate-item-btn');
            if (dupBtn && itemsBody.contains(dupBtn)) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
                const srcCard = dupBtn.closest('.item-row-entry');
                if (!srcCard) return;
                const values = {
                    und: srcCard.querySelector('[name="und[]"]')?.value || '1',
                    model: srcCard.querySelector('[name="model[]"]')?.value || '',
                    serial: '',
                    accessories: srcCard.querySelector('[name="accessories[]"]')?.value || '',
                    details: srcCard.querySelector('[name="details[]"]')?.value || '',
                    features: srcCard.querySelector('[name="features[]"]')?.value || '',
                    delivered: srcCard.querySelector('.item-delivered-hidden')?.value || '0'
                };
                const newCard = createItemCardElement(values);
                srcCard.insertAdjacentElement('afterend', newCard);
                updateSummaryPanel();
                return;
            }
        }, true);

        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList && e.target.classList.contains('und-input')) {
                updateSummaryPanel();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // En edit-receipt, asegurar que los artículos que ya vienen marcados como entregados pinten su tarjeta en verde al cargar
        document.querySelectorAll('#items-body .item-row-entry').forEach(card => {
            const hiddenInput = card.querySelector('.item-delivered-hidden');
            const btn = card.querySelector('.toggle-item-delivered');
            if (hiddenInput && String(hiddenInput.value) === '1') {
                card.classList.add('is-delivered-card');
                card.style.setProperty('border-color', '#86efac', 'important');
                card.style.setProperty('background-color', '#f0fdf4', 'important');
                if (btn) {
                    btn.style.setProperty('color', '#15803d', 'important');
                    btn.style.setProperty('background-color', '#dcfce7', 'important');
                    btn.style.setProperty('border-color', '#86efac', 'important');
                }
            }
        });

        // Chips de Tiempo estimado de diagnóstico
        const deliveryInput = document.getElementById('delivery_time_input');
        const chipBtns = document.querySelectorAll('.lvr-chip-btn');
        chipBtns.forEach(chip => {
            chip.addEventListener('click', function() {
                const timeVal = this.getAttribute('data-time');
                if (deliveryInput) {
                    deliveryInput.value = timeVal;
                    chipBtns.forEach(c => c.classList.remove('is-active'));
                    this.classList.add('is-active');
                }
            });
        });
        if (deliveryInput) {
            deliveryInput.addEventListener('input', function() {
                chipBtns.forEach(c => {
                    c.classList.toggle('is-active', c.getAttribute('data-time') === this.value.trim());
                });
            });
        }

        ['receipt-dynamic-slots', 'receipt-slots-initial', 'receipt-slots-process'].forEach(id => {
            const container = document.getElementById(id);
            if (container) {
                const observer = new MutationObserver(() => updateSummaryPanel());
                observer.observe(container, { childList: true, subtree: true });
                container.addEventListener('change', () => setTimeout(updateSummaryPanel, 150));
            }
        });

        updateSummaryPanel();
    });

    // ============================================================================
    // LÓGICA DE BÚSQUEDA Y AUTOCOMPLETADO DE CLIENTE
    // ============================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const dniPrefix = document.getElementById('input-dni-prefix');
        const dniDigits = document.getElementById('input-dni-digits');
        const nameInput = document.getElementById('input-name');

        if (dniPrefix && dniDigits && nameInput) {
            const checkClient = function() {
                const dni = dniPrefix.value + dniDigits.value;
                if (dniDigits.value.length > 0) {
                    nameInput.placeholder = "Buscando cliente...";
                    fetch('/admin/receipts/check-client?dni=' + dni)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                nameInput.value = data.name;
                                nameInput.readOnly = true;
                                nameInput.style.backgroundColor = '#f1f5f9';
                            } else {
                                if(nameInput.readOnly) { nameInput.value = ''; }
                                nameInput.readOnly = false;
                                nameInput.style.backgroundColor = '';
                                nameInput.placeholder = "Escriba o seleccione un cliente...";
                            }
                            updateSummaryPanel();
                        })
                        .catch(err => console.error("Error buscando cliente:", err));
                } else {
                    nameInput.readOnly = false;
                    nameInput.style.backgroundColor = '';
                    nameInput.placeholder = "Escriba o seleccione un cliente...";
                    updateSummaryPanel();
                }
            };
            dniDigits.addEventListener('blur', checkClient);
            dniPrefix.addEventListener('change', function() {
                if(dniDigits.value.length > 0) checkClient();
            });

            dniDigits.addEventListener('input', function() {
                if(this.value.length === 0) {
                    nameInput.readOnly = false;
                    nameInput.style.backgroundColor = '';
                    nameInput.value = '';
                    updateSummaryPanel();
                }
            });

            nameInput.addEventListener('input', function() {
                const val = this.value;
                const datalist = document.getElementById('clients-datalist');
                if (datalist) {
                    let isFromList = false;
                    for (let i = 0; i < datalist.options.length; i++) {
                        if (datalist.options[i].value === val) {
                            isFromList = true;
                            break;
                        }
                    }

                    if (isFromList) {
                        const parts = val.split(' - ');
                        const dniPart = parts.pop().trim(); 
                        const namePart = parts.join(' - ').trim(); 
                        
                        this.value = namePart;
                        
                        const match = dniPart.match(/^([A-Za-z])(\d+)$/);
                        if (match && dniPrefix.tagName.toLowerCase() === 'select') {
                            dniPrefix.value = match[1].toUpperCase();
                            dniDigits.value = match[2];
                        } else {
                            dniDigits.value = dniPart.replace(/[^0-9A-Za-z]/g, '');
                        }
                        
                        this.readOnly = true;
                        this.style.backgroundColor = '#f1f5f9';
                    }
                }
                updateSummaryPanel();
            });
            
            nameInput.addEventListener('dblclick', function() {
                if (this.readOnly) {
                    this.readOnly = false;
                    this.style.backgroundColor = '';
                    this.value = '';
                    dniDigits.value = '';
                    this.focus();
                    updateSummaryPanel();
                }
            });
        }
    });

    // ============================================================================
    // MOTOR DE SEGUNDO PLANO Y NAVEGACIÓN ASÍNCRONA (PJAX)
    // ============================================================================
    let isUploadingBackground = false;

    window.addEventListener('beforeunload', function (e) {
        if (isUploadingBackground) {
            e.preventDefault();
            e.returnValue = 'Hay archivos subiéndose en segundo plano. ¿Estás seguro de que deseas salir y cancelar la carga?';
            return e.returnValue;
        }
    });

    document.addEventListener('click', async function(e) {
        const link = e.target.closest('a');
        if (!link || !isUploadingBackground) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || link.getAttribute('target') === '_blank' || href.includes('?action=')) return;

        if (!href.startsWith('/admin/')) {
            if (!confirm('⚠️ Hay imágenes subiéndose en segundo plano a Google Drive.\n\nSi sales de la administración ahora, la carga se interrumpirá.\n¿Deseas cancelar la subida y salir?')) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                isUploadingBackground = false;
            }
            return;
        }

        e.preventDefault();
        navigateToAdminPage(href);
    }, true);

    async function navigateToAdminPage(url) {
        try {
            document.body.style.cursor = 'wait';
            const response = await fetch(url);
            const html = await response.text();
            
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            
            const currentMain = document.querySelector('.main-content');
            const newMain = newDoc.querySelector('.main-content');
            if (currentMain && newMain) {
                currentMain.innerHTML = newMain.innerHTML;
            }

            const currentNav = document.querySelector('.sidebar__nav');
            const newNav = newDoc.querySelector('.sidebar__nav');
            if (currentNav && newNav) {
                currentNav.innerHTML = newNav.innerHTML;
            }

            document.title = newDoc.title;
            history.pushState(null, '', url);
            window.scrollTo(0, 0);

            window.dispatchEvent(new Event('DOMContentLoaded'));
        } catch (err) {
            console.error("Error en navegación asíncrona:", err);
            window.location.href = url;
        } finally {
            document.body.style.cursor = 'default';
        }
    }

    // ============================================================================
    // PROCESO DE SUBIDA EN SEGUNDO PLANO Y ACTUALIZACIÓN DE NOTA
    // ============================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('receipt-form');
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const initialFileInputs = form.querySelectorAll('input[name="receipt_images[]"]');
                const processFileInputs = form.querySelectorAll('input[name="process_images[]"]');
                let filesToUpload = [];

                initialFileInputs.forEach(input => {
                    if(input.files && input.files.length > 0) {
                        for(let i = 0; i < input.files.length; i++) filesToUpload.push({file: input.files[i], type: 'iniciales'});
                    }
                });
                processFileInputs.forEach(input => {
                    if(input.files && input.files.length > 0) {
                        for(let i = 0; i < input.files.length; i++) filesToUpload.push({file: input.files[i], type: 'proceso'});
                    }
                });

                form.querySelectorAll(':disabled').forEach(el => el.disabled = false);

                if (filesToUpload.length === 0) { 
                    Swal.fire({
                        title: 'Generando Nota...',
                        text: 'Creando PDF y guardando datos de forma segura. Por favor espera.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit(); 
                    return; 
                }

                const finalFormData = new FormData(form);
                finalFormData.delete('receipt_images[]');
                finalFormData.delete('process_images[]');

                const storeUrl = form.getAttribute('action');
                const clientName = document.getElementById('input-name').value || 'Cliente';
                const receiptCode = document.getElementById('receipt_code').value || 'Nota de Recibo';
                
                const oldClientName = document.getElementById('old-client-name') ? document.getElementById('old-client-name').value : clientName;
                const dniPrefix = document.getElementById('input-dni-prefix') ? document.getElementById('input-dni-prefix').value : 'V';
                const dniDigits = document.getElementById('input-dni-digits') ? document.getElementById('input-dni-digits').value : '';
                const fullDni = dniPrefix + dniDigits;
                const oldDni = document.getElementById('old-client-id') ? document.getElementById('old-client-id').value : fullDni;

                isUploadingBackground = true;

                const widget = document.createElement('div');
                widget.id = 'bg-upload-widget';
                widget.style.cssText = `
                    position: fixed;
                    bottom: 25px;
                    right: 25px;
                    width: 330px;
                    background: #ffffff;
                    border-radius: 12px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.22);
                    border: 1px solid #e9ecef;
                    border-left: 6px solid #0d6efd;
                    padding: 16px;
                    z-index: 999999;
                    font-family: 'Inter', sans-serif;
                    transition: all 0.3s ease;
                `;

                widget.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 700; font-size: 0.85rem; color: #0d6efd; display: flex; align-items: center; gap: 6px;">
                            <i class='bx bx-cloud-upload bx-flashing' style='font-size: 1.25rem;'></i> Subiendo a Drive...
                        </span>
                        <span id="widget-counter" style="font-size: 0.75rem; font-weight: 700; background: #e9ecef; padding: 3px 9px; border-radius: 20px; color: #495057;">
                            0 / ${filesToUpload.length}
                        </span>
                    </div>
                    <div style="font-size: 0.9rem; font-weight: 700; color: #212529; white-space: nowrap; overflow: hidden; text-ellipsis: ellipsis; margin-bottom: 2px;">
                        ${receiptCode}
                    </div>
                    <div style="font-size: 0.8rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-ellipsis: ellipsis; margin-bottom: 12px;">
                        <i class='bx bx-user'></i> ${clientName}
                    </div>
                    <div style="width: 100%; height: 7px; background: #e9ecef; border-radius: 10px; overflow: hidden;">
                        <div id="widget-progress-bar" style="width: 0%; height: 100%; background: #0d6efd; transition: width 0.3s ease;"></div>
                    </div>
                    <div id="widget-status-text" style="font-size: 0.72rem; color: #6c757d; margin-top: 8px; text-align: right; font-style: italic;">
                        Puedes seguir navegando en el panel...
                    </div>
                `;
                document.body.appendChild(widget);

                navigateToAdminPage('/admin/receipts');

                let uploadedCount = 0;
                for (let item of filesToUpload) {
                    let uploadData = new FormData();
                    uploadData.append('file', item.file);
                    uploadData.append('folderType', item.type);
                    uploadData.append('receiptCode', receiptCode);
                    uploadData.append('clientName', clientName);
                    uploadData.append('client_id_number', fullDni);
                    uploadData.append('oldClientName', oldClientName);
                    uploadData.append('old_client_id_number', oldDni);

                    try {
                        let response = await fetch('/admin/receipts/upload-async', {
                            method: 'POST',
                            body: uploadData
                        });
                        let result = await response.json();
                        
                        if (result.success) {
                            const fieldName = item.type === 'iniciales' ? 'preuploaded_initial[]' : 'preuploaded_process[]';
                            finalFormData.append(fieldName, result.link);
                        }
                    } catch (e) {
                        console.error("Error subiendo archivo en segundo plano:", e);
                    }

                    uploadedCount++;
                    const percentage = Math.round((uploadedCount / filesToUpload.length) * 100);
                    const counterEl = document.getElementById('widget-counter');
                    const barEl = document.getElementById('widget-progress-bar');
                    if (counterEl) counterEl.innerText = `${uploadedCount} / ${filesToUpload.length}`;
                    if (barEl) barEl.style.width = `${percentage}%`;
                }

                const statusEl = document.getElementById('widget-status-text');
                const barEl = document.getElementById('widget-progress-bar');
                if (statusEl) {
                    statusEl.style.color = '#fd7e14';
                    statusEl.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Generando PDF y guardando nota...";
                }
                if (barEl) barEl.style.background = '#fd7e14';

                try {
                    await fetch(storeUrl, {
                        method: 'POST',
                        body: finalFormData
                    });

                    isUploadingBackground = false;

                    widget.style.borderLeftColor = '#198754';
                    widget.innerHTML = `
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-weight: 700; font-size: 0.9rem; color: #198754; display: flex; align-items: center; gap: 6px;">
                                <i class='bx bxs-check-circle' style='font-size: 1.3rem;'></i> ¡Nota guardada con éxito!
                            </span>
                            <button onclick="this.closest('#bg-upload-widget').remove()" style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:#6c757d;">&times;</button>
                        </div>
                        <div style="font-size: 0.85rem; color: #212529; margin-top: 6px;">
                            <strong>${receiptCode}</strong> subida a Drive correctamente.
                        </div>
                    `;

                    if (window.location.pathname === '/admin/receipts') {
                        navigateToAdminPage('/admin/receipts');
                    }

                    setTimeout(() => { if(widget && widget.parentNode) widget.remove(); }, 6000);

                } catch (err) {
                    isUploadingBackground = false;
                    if (widget) {
                        widget.style.borderLeftColor = '#dc3545';
                        widget.innerHTML = `
                            <div style="color: #dc3545; font-weight: 700; font-size: 0.85rem;">
                                <i class='bx bxs-error-circle'></i> Error al guardar la nota en BD.
                            </div>
                        `;
                    }
                }
            });
        }
    });

    // 🛡️ CAPTURADOR DE IMÁGENES ELIMINADAS (Evita la resurrección de fotos)
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-preview-btn');
        if (removeBtn) {
            const frame = removeBtn.closest('.image-preview-frame, .image-slot');
            if (frame) {
                const img = frame.querySelector('img');
                if (img && img.src && img.src.includes('drive')) {
                    const isProcess = Boolean(frame.closest('#process-images-container, #receipt-slots-process'));
                    
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = isProcess ? 'delete_process_images[]' : 'delete_receipt_images[]';
                    hiddenInput.value = img.src;
                    document.getElementById('receipt-form').appendChild(hiddenInput);
                }
            }
            setTimeout(updateSummaryPanel, 100);
        }
    });

    // ==========================================================
    // LÓGICA DINÁMICA DE PRESUPUESTO (Definitivo y Tentativo)
    // ==========================================================
    function initBudgetLogic() {
        const budgetBody = document.getElementById('budget-body');
        const addBudgetBtns = document.querySelectorAll('.js-add-definitive-btn');
        if (!budgetBody) return;
        
        function calculateTotal() {
            let totalMin = 0;
            let totalMax = 0;
            let hasRange = false;

            const rows = budgetBody.querySelectorAll('.budget-row-entry');
            rows.forEach(row => {
                const type = row.querySelector('.budget-type').value;
                const hiddenAmount = row.querySelector('.hidden-amount');
                
                if (type === 'single') {
                    const val = parseFloat(row.querySelector('.amount-single').value) || 0;
                    totalMin += val;
                    totalMax += val;
                    hiddenAmount.value = val.toFixed(2);
                } else {
                    hasRange = true;
                    const min = parseFloat(row.querySelector('.amount-min').value) || 0;
                    const max = parseFloat(row.querySelector('.amount-max').value) || 0;
                    totalMin += min;
                    totalMax += max;
                    hiddenAmount.value = min.toFixed(2) + ' - ' + max.toFixed(2);
                }

                const rmBtn = row.querySelector('.remove-budget-row');
                if (rmBtn) rmBtn.disabled = (rows.length <= 1);
            });

            const formatted = hasRange ? (totalMin.toFixed(2) + ' - ' + totalMax.toFixed(2)) : totalMin.toFixed(2);
            const finalInput = document.getElementById('final_amount');
            const finalDisplay = document.getElementById('final_amount_display');
            const summaryFinal = document.getElementById('summary-final-total');

            if (finalInput) finalInput.value = formatted;
            if (finalDisplay) finalDisplay.textContent = formatted;
            if (summaryFinal) summaryFinal.textContent = `${CURRENCY_SYMBOL} ${formatted}`;
        }

        function validateRealNumber(e) {
            if (['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) return;
            if (!/^[0-9.]$/.test(e.key)) e.preventDefault();
            if (e.key === '.' && e.target.value.includes('.')) e.preventDefault();
        }

        function formatEmptyZero(e) {
            let val = e.target.value.trim();
            if (val === '' || val === '.') {
                e.target.value = '0.00';
            } else {
                let num = parseFloat(val);
                e.target.value = !isNaN(num) ? num.toFixed(2) : '0.00';
            }
            calculateTotal();
        }

        function handleTypeChange(e) {
            const row = e.target.closest('.budget-row-entry');
            const singleContainer = row.querySelector('.single-container');
            const rangeContainer = row.querySelector('.range-container');
            
            if (e.target.value === 'single') {
                singleContainer.style.display = 'block';
                rangeContainer.style.display = 'none';
            } else {
                singleContainer.style.display = 'none';
                rangeContainer.style.display = 'flex';
            }
            calculateTotal();
        }

        function attachEvents(row) {
            row.querySelectorAll('.amount-input').forEach(input => {
                input.addEventListener('keydown', validateRealNumber);
                input.addEventListener('blur', formatEmptyZero);
                input.addEventListener('input', calculateTotal);
            });
            row.querySelector('.budget-type').addEventListener('change', handleTypeChange);
            
            const removeBtn = row.querySelector('.remove-budget-row');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (budgetBody.querySelectorAll('.budget-row-entry').length > 1) {
                        row.remove();
                        calculateTotal();
                    }
                });
            }
        }

        function addDefinitiveRow() {
            const div = document.createElement('div');
            div.className = 'lvr-budget-item-card budget-row-entry';
            div.innerHTML = `
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Técnico (Interno)</label>
                    <input type="text" name="budget_tech[]" class="lvr-input" placeholder="Ej: Samantha">
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Descripción del servicio</label>
                    <textarea name="description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."></textarea>
                </div>
                <div class="lvr-budget-row-grid">
                    <div class="lvr-field-group">
                        <label class="lvr-field-label">Tipo</label>
                        <select class="lvr-select budget-type">
                            <option value="single">Valor Único</option>
                            <option value="range">Rango</option>
                        </select>
                    </div>
                    <div class="lvr-field-group">
                        <label class="lvr-field-label">Monto (${CURRENCY_SYMBOL})</label>
                        <div class="single-container">
                            <input type="text" class="lvr-input amount-input amount-single" placeholder="0.00" style="text-align: right;">
                        </div>
                        <div class="range-container" style="display: none; align-items: center; gap: 6px;">
                            <input type="text" class="lvr-input amount-input amount-min" placeholder="0.00" style="text-align: right; flex: 1;">
                            <span style="color: #64748b; font-weight: 700;">-</span>
                            <input type="text" class="lvr-input amount-input amount-max" placeholder="0.00" style="text-align: right; flex: 1;">
                        </div>
                        <input type="hidden" name="amount[]" class="hidden-amount" value="0.00">
                    </div>
                </div>
                <div class="lvr-budget-item-actions">
                    <input type="hidden" name="budget_client_choice[]" value="">
                    <label class="lvr-ask-pill-btn">
                        <input type="hidden" name="budget_ask[]" class="ask-hidden" value="0">
                        <input type="checkbox" class="ask-checkbox" style="display: none;" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'; this.closest('.lvr-ask-pill-btn').classList.toggle('is-active', this.checked);">
                        <i class='bx bx-message-rounded-dots'></i>
                        <span>Preguntar</span>
                    </label>
                    <button type="button" class="lvr-icon-btn danger remove-budget-row" title="Eliminar línea">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            `;
            budgetBody.appendChild(div);
            attachEvents(div);
            calculateTotal();
        }

        addBudgetBtns.forEach(btn => {
            const cleanBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(cleanBtn, btn);
            cleanBtn.addEventListener('click', addDefinitiveRow);
        });

        budgetBody.querySelectorAll('.budget-row-entry').forEach(attachEvents);
        calculateTotal();
    }

    function initTentativeBudgetLogic() {
        const tentativeBody = document.getElementById('tentative-budget-body');
        const addTentativeBtns = document.querySelectorAll('.js-add-tentative-btn');
        if (!tentativeBody) return;

        function calculateTotalTentative() {
            let totalMin = 0;
            let totalMax = 0;
            let hasRange = false;

            const rows = tentativeBody.querySelectorAll('.budget-row-entry');
            rows.forEach(row => {
                const type = row.querySelector('.budget-type').value;
                const hiddenAmount = row.querySelector('.hidden-amount');
                
                if (type === 'single') {
                    const val = parseFloat(row.querySelector('.amount-single').value) || 0;
                    totalMin += val;
                    totalMax += val;
                    hiddenAmount.value = val.toFixed(2);
                } else {
                    hasRange = true;
                    const min = parseFloat(row.querySelector('.amount-min').value) || 0;
                    const max = parseFloat(row.querySelector('.amount-max').value) || 0;
                    totalMin += min;
                    totalMax += max;
                    hiddenAmount.value = min.toFixed(2) + ' - ' + max.toFixed(2);
                }

                const rmBtn = row.querySelector('.remove-budget-row');
                if (rmBtn) rmBtn.disabled = (rows.length <= 1);
            });

            const formatted = hasRange ? (totalMin.toFixed(2) + ' - ' + totalMax.toFixed(2)) : totalMin.toFixed(2);
            const finalInput = document.getElementById('tentative_final_amount');
            const finalDisplay = document.getElementById('tentative_final_display');
            const summaryTentative = document.getElementById('summary-tentative-total');

            if (finalInput) finalInput.value = formatted;
            if (finalDisplay) finalDisplay.textContent = formatted;
            if (summaryTentative) summaryTentative.textContent = `${CURRENCY_SYMBOL} ${formatted}`;
        }

        function validateRealNumber(e) {
            if (['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) return;
            if (!/^[0-9.]$/.test(e.key)) e.preventDefault();
            if (e.key === '.' && e.target.value.includes('.')) e.preventDefault();
        }

        function formatEmptyZero(e) {
            let val = e.target.value.trim();
            if (val === '' || val === '.') {
                e.target.value = '0.00';
            } else {
                let num = parseFloat(val);
                e.target.value = !isNaN(num) ? num.toFixed(2) : '0.00';
            }
            calculateTotalTentative();
        }

        function handleTypeChange(e) {
            const row = e.target.closest('.budget-row-entry');
            const singleContainer = row.querySelector('.single-container');
            const rangeContainer = row.querySelector('.range-container');
            if (e.target.value === 'single') {
                singleContainer.style.display = 'block'; rangeContainer.style.display = 'none';
            } else {
                singleContainer.style.display = 'none'; rangeContainer.style.display = 'flex';
            }
            calculateTotalTentative();
        }

        function attachEvents(row) {
            row.querySelectorAll('.amount-input').forEach(input => {
                input.addEventListener('keydown', validateRealNumber);
                input.addEventListener('blur', formatEmptyZero);
                input.addEventListener('input', calculateTotalTentative);
            });
            row.querySelector('.budget-type').addEventListener('change', handleTypeChange);
            const removeBtn = row.querySelector('.remove-budget-row');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => { 
                    if (tentativeBody.querySelectorAll('.budget-row-entry').length > 1) {
                        row.remove();
                        calculateTotalTentative();
                    }
                });
            }
        }

        function addTentativeRow() {
            const div = document.createElement('div');
            div.className = 'lvr-budget-item-card budget-row-entry';
            div.innerHTML = `
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Técnico (Interno)</label>
                    <input type="text" name="tentative_tech[]" class="lvr-input" placeholder="Ej: Samantha">
                </div>
                <div class="lvr-field-group">
                    <label class="lvr-field-label">Descripción del servicio</label>
                    <textarea name="tentative_description[]" class="lvr-textarea-2lines" rows="2" placeholder="Descripción detallada del servicio..."></textarea>
                </div>
                <div class="lvr-budget-row-grid">
                    <div class="lvr-field-group">
                        <label class="lvr-field-label">Tipo</label>
                        <select class="lvr-select budget-type">
                            <option value="single">Valor Único</option>
                            <option value="range">Rango</option>
                        </select>
                    </div>
                    <div class="lvr-field-group">
                        <label class="lvr-field-label">Monto (${CURRENCY_SYMBOL})</label>
                        <div class="single-container">
                            <input type="text" class="lvr-input amount-input amount-single" placeholder="0.00" style="text-align: right;">
                        </div>
                        <div class="range-container" style="display: none; align-items: center; gap: 6px;">
                            <input type="text" class="lvr-input amount-input amount-min" placeholder="0.00" style="text-align: right; flex: 1;">
                            <span style="color: #64748b; font-weight: 700;">-</span>
                            <input type="text" class="lvr-input amount-input amount-max" placeholder="0.00" style="text-align: right; flex: 1;">
                        </div>
                        <input type="hidden" name="tentative_amount[]" class="hidden-amount" value="0.00">
                    </div>
                </div>
                <div class="lvr-budget-item-actions">
                    <button type="button" class="lvr-icon-btn danger remove-budget-row" title="Eliminar línea">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            `;
            tentativeBody.appendChild(div);
            attachEvents(div);
            calculateTotalTentative();
        }

        addTentativeBtns.forEach(btn => {
            const cleanBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(cleanBtn, btn);
            cleanBtn.addEventListener('click', addTentativeRow);
        });

        tentativeBody.querySelectorAll('.budget-row-entry').forEach(attachEvents);
        calculateTotalTentative();
    }

    initBudgetLogic();
    initTentativeBudgetLogic();

    // ==========================================================
    // EDITOR QUILL DE OBSERVACIONES
    // ==========================================================
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('notes-quill-editor')) {
            var quillNotes = new Quill('#notes-quill-editor', {
                theme: 'snow',
                placeholder: 'Escribe las observaciones aquí...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            var hiddenNotes = document.getElementById('notes-hidden-input');
            quillNotes.on('text-change', function() {
                var html = quillNotes.root.innerHTML;
                hiddenNotes.value = (html === '<p><br></p>') ? '' : html;
            });

            var form = document.getElementById('notes-quill-editor').closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    var html = quillNotes.root.innerHTML;
                    hiddenNotes.value = (html === '<p><br></p>') ? '' : html;
                });
            }
        }
    });

    // ============================================================================
    // LÓGICA DE BÚSQUEDA, AUTOCOMPLETADO Y MODAL DE CLIENTE
    // ============================================================================
    const clientsCatalog = <?php echo json_encode($clientsList ?? []); ?>;
    let currentSelectedClient = {
        id_client: 0,
        client_id_number: '',
        name: '',
        client_phone: '',
        client_email: '',
        client_address: 'Puerto Ordaz',
        rating: 5,
        notes: ''
    };

    function fillClientContactInputs(clientData, overwriteExisting = true) {
        const phoneInput  = document.getElementById('phone_input');
        const phoneHidden = document.getElementById('client_phone_hidden');
        const emailInput  = document.getElementById('input-email') || document.querySelector('input[name="client_email"]');
        const addrInput   = document.getElementById('input-address') || document.querySelector('input[name="client_address"]');

        const savedPhone = (clientData.client_phone || '').trim();
        if (overwriteExisting || (phoneInput && !phoneInput.value.trim())) {
            if (phoneInput) {
                phoneInput.value = savedPhone;
                if (window.itiInstance && typeof window.itiInstance.setNumber === 'function') {
                    window.itiInstance.setNumber(savedPhone);
                }
                phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
                phoneInput.dispatchEvent(new Event('keyup', { bubbles: true }));
            }
            if (phoneHidden) {
                phoneHidden.value = savedPhone;
            }
        }

        if (emailInput && (overwriteExisting || !emailInput.value.trim())) {
            emailInput.value = (clientData.client_email || '').trim();
        }

        if (addrInput) {
            const addrVal = (clientData.client_address && clientData.client_address.trim() !== '')
                ? clientData.client_address.trim()
                : 'Puerto Ordaz';
            if (overwriteExisting || !addrInput.value.trim()) {
                addrInput.value = addrVal;
            }
        }
    }

    function renderClientQuickInfo(rating, notes) {
        const box = document.getElementById('client-Quick-info');
        const starsEl = document.getElementById('client-stars-display');
        const notesEl = document.getElementById('client-notes-display');
        if (!box || !starsEl || !notesEl) return;

        const parsedRating = parseInt(rating);
        const r = !isNaN(parsedRating) ? Math.max(0, Math.min(5, parsedRating)) : 5;
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            starsHtml += `<i class='bx ${i <= r ? 'bxs-star' : 'bx-star'}'></i>`;
        }
        starsEl.innerHTML = starsHtml;
        notesEl.textContent = notes ? `• ${notes}` : `• ${r}/5 estrellas`;
        box.style.display = 'inline-flex';
    }

    function syncClientInfoByDni(shouldFillContact = true) {
        const prefixEl = document.getElementById('input-dni-prefix');
        const digitsEl = document.getElementById('input-dni-digits');
        const nameEl   = document.getElementById('input-name');
        if (!digitsEl || !digitsEl.value.trim()) {
            const box = document.getElementById('client-Quick-info');
            if (box) box.style.display = 'none';
            return;
        }
        const fullDni = ((prefixEl ? prefixEl.value : '') + digitsEl.value.trim()).toUpperCase();

        const found = clientsCatalog.find(c => (c.client_id_number || '').toUpperCase() === fullDni);
        if (found) {
            const parsedFoundRating = parseInt(found.rating);
            currentSelectedClient = {
                id_client: parseInt(found.id_client) || 0,
                client_id_number: found.client_id_number,
                name: found.client_name,
                client_phone: found.client_phone || '',
                client_email: found.client_email || '',
                client_address: (found.client_address && found.client_address.trim() !== '') ? found.client_address : 'Puerto Ordaz',
                rating: !isNaN(parsedFoundRating) ? parsedFoundRating : 5,
                notes: found.notes || ''
            };
            if (nameEl && found.client_name) {
                nameEl.value = found.client_name;
                nameEl.readOnly = true;
                nameEl.style.backgroundColor = '#f1f5f9';
            }
            if (shouldFillContact) fillClientContactInputs(currentSelectedClient, true);
            renderClientQuickInfo(currentSelectedClient.rating, currentSelectedClient.notes);
            updateSummaryPanel();
            return;
        }

        fetch('/admin/receipts/check-client?dni=' + encodeURIComponent(fullDni))
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const parsedDataRating = parseInt(data.rating);
                    currentSelectedClient = {
                        id_client: data.id_client || 0,
                        client_id_number: fullDni,
                        name: data.name || (nameEl ? nameEl.value : ''),
                        client_phone: data.client_phone || '',
                        client_email: data.client_email || '',
                        client_address: (data.client_address && data.client_address.trim() !== '') ? data.client_address : 'Puerto Ordaz',
                        rating: !isNaN(parsedDataRating) ? parsedDataRating : 0,
                        notes: data.notes || ''
                    };
                    if (nameEl && data.name) {
                        nameEl.value = data.name;
                        nameEl.readOnly = true;
                        nameEl.style.backgroundColor = '#f1f5f9';
                    }
                    if (shouldFillContact) fillClientContactInputs(currentSelectedClient, true);
                    renderClientQuickInfo(currentSelectedClient.rating, currentSelectedClient.notes);
                } else {
                    if (nameEl && nameEl.readOnly) nameEl.value = '';
                    if (nameEl) {
                        nameEl.readOnly = false;
                        nameEl.style.backgroundColor = '';
                        nameEl.placeholder = "Escriba o seleccione un cliente...";
                    }
                    currentSelectedClient = {
                        id_client: 0,
                        client_id_number: fullDni,
                        name: nameEl ? nameEl.value : '',
                        client_phone: '',
                        client_email: '',
                        client_address: 'Puerto Ordaz',
                        rating: 0,
                        notes: ''
                    };
                    renderClientQuickInfo(0, 'Cliente nuevo (0 estrellas por defecto)');
                }
                updateSummaryPanel();
            }).catch(err => console.error("Error buscando cliente:", err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dniPrefix = document.getElementById('input-dni-prefix');
        const dniDigits = document.getElementById('input-dni-digits');
        const nameInput = document.getElementById('input-name');
        const addrInput = document.getElementById('input-address') || document.querySelector('input[name="client_address"]');

        // Si el campo dirección se deja vacío al salir del input, poner "Puerto Ordaz" por defecto
        if (addrInput) {
            if (!addrInput.value.trim()) addrInput.value = 'Puerto Ordaz';
            addrInput.addEventListener('blur', function() {
                if (!this.value.trim()) this.value = 'Puerto Ordaz';
            });
        }

        if (dniPrefix && dniDigits && nameInput) {
            dniDigits.addEventListener('blur', () => syncClientInfoByDni(true));
            dniPrefix.addEventListener('change', function() {
                if (dniDigits.value.length > 0) syncClientInfoByDni(true);
            });

            dniDigits.addEventListener('input', function() {
                if (this.value.length === 0) {
                    nameInput.readOnly = false;
                    nameInput.style.backgroundColor = '';
                    nameInput.value = '';
                    const box = document.getElementById('client-Quick-info');
                    if (box) box.style.display = 'none';
                    updateSummaryPanel();
                }
            });

            nameInput.addEventListener('input', function() {
                const val = this.value;
                const datalist = document.getElementById('clients-datalist');
                if (datalist) {
                    let isFromList = false;
                    for (let i = 0; i < datalist.options.length; i++) {
                        if (datalist.options[i].value === val) {
                            isFromList = true;
                            break;
                        }
                    }

                    if (isFromList) {
                        const parts = val.split(' - ');
                        const dniPart = parts.pop().trim(); 
                        const namePart = parts.join(' - ').trim(); 
                        
                        this.value = namePart;
                        
                        const match = dniPart.match(/^([A-Za-z])(\d+)$/);
                        if (match && dniPrefix.tagName.toLowerCase() === 'select') {
                            dniPrefix.value = match[1].toUpperCase();
                            dniDigits.value = match[2];
                        } else {
                            dniDigits.value = dniPart.replace(/[^0-9A-Za-z]/g, '');
                        }
                        
                        this.readOnly = true;
                        this.style.backgroundColor = '#f1f5f9';
                        syncClientInfoByDni(true);
                    }
                }
                updateSummaryPanel();
            });
            
            nameInput.addEventListener('dblclick', function() {
                if (this.readOnly) {
                    this.readOnly = false;
                    this.style.backgroundColor = '';
                    this.value = '';
                    dniDigits.value = '';
                    this.focus();
                    updateSummaryPanel();
                }
            });
        }

        // Al abrir la página (ej. en edit-receipt), solo carga las estrellas sin pisar los datos guardados de la nota
        setTimeout(() => syncClientInfoByDni(false), 250);
    });

    window.openClientEditModal = function() {
        const prefixEl    = document.getElementById('input-dni-prefix');
        const digitsEl    = document.getElementById('input-dni-digits');
        const nameEl      = document.getElementById('input-name');
        const phoneInput  = document.getElementById('phone_input');
        const phoneHidden = document.getElementById('client_phone_hidden');
        const emailInput  = document.getElementById('input-email') || document.querySelector('input[name="client_email"]');
        const addrInput   = document.getElementById('input-address') || document.querySelector('input[name="client_address"]');

        const currentDni   = ((prefixEl ? prefixEl.value : '') + (digitsEl ? digitsEl.value.trim() : '')).toUpperCase();
        const currentName  = nameEl ? nameEl.value.trim() : '';
        const currentPhone = (phoneInput && phoneInput.value.trim()) ? phoneInput.value.trim() : (currentSelectedClient.client_phone || '');
        const currentEmail = (emailInput && emailInput.value.trim()) ? emailInput.value.trim() : (currentSelectedClient.client_email || '');
        const currentAddr  = (addrInput && addrInput.value.trim()) ? addrInput.value.trim() : (currentSelectedClient.client_address || 'Puerto Ordaz');
        const parsedInit   = parseInt(currentSelectedClient.rating);
        const initRating   = !isNaN(parsedInit) ? parsedInit : 5;
        const initNotes    = currentSelectedClient.notes || '';

        Swal.fire({
            width: '540px',
            title: '<h3 style="margin:0; font-size:1.25rem; color:#0f172a;"><i class="bx bx-user-check"></i> Editar Datos del Cliente</h3>',
            html: `
                <div style="text-align: left; font-family: 'Inter', sans-serif; font-size: 0.88rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-weight:600; display:block; margin-top:4px;">Identificación (DNI / RIF): *</label>
                            <input type="text" id="swal-client-dni" class="swal2-input" style="margin:4px 0 8px 0; width:100%; height:36px; font-size:0.88rem;" value="${currentDni.replace(/"/g, '&quot;')}">
                        </div>
                        <div>
                            <label style="font-weight:600; display:block; margin-top:4px;">Teléfono WhatsApp: *</label>
                            <input type="text" id="swal-client-phone" class="swal2-input" placeholder="Ej: 0412-1234567" style="margin:4px 0 8px 0; width:100%; height:36px; font-size:0.88rem;" value="${currentPhone.replace(/"/g, '&quot;')}">
                        </div>
                    </div>

                    <label style="font-weight:600; display:block; margin-top:4px;">Nombre o Razón Social: *</label>
                    <input type="text" id="swal-client-name" class="swal2-input" style="margin:4px 0 8px 0; width:100%; height:36px; font-size:0.88rem;" value="${currentName.replace(/"/g, '&quot;')}">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-weight:600; display:block; margin-top:4px;">Correo electrónico:</label>
                            <input type="email" id="swal-client-email" class="swal2-input" placeholder="cliente@correo.com" style="margin:4px 0 8px 0; width:100%; height:36px; font-size:0.88rem;" value="${currentEmail.replace(/"/g, '&quot;')}">
                        </div>
                        <div>
                            <label style="font-weight:600; display:block; margin-top:4px;">Dirección:</label>
                            <input type="text" id="swal-client-address" class="swal2-input" placeholder="Puerto Ordaz" style="margin:4px 0 8px 0; width:100%; height:36px; font-size:0.88rem;" value="${currentAddr.replace(/"/g, '&quot;')}">
                        </div>
                    </div>

                    <label style="font-weight:600; display:block; margin-top:4px;">Calificación (0 al 5 - Uso Interno):</label>
                    <input type="hidden" id="swal-client-rating" value="${initRating}">
                    <div id="swal-star-container" style="display:flex; align-items:center; gap:8px; font-size:1.75rem; color:#f59e0b; cursor:pointer; margin:4px 0 10px 0;">
                        ${[1,2,3,4,5].map(n => `<i class="bx ${n <= initRating ? 'bxs-star' : 'bx-star'}" data-star="${n}" title="${n} estrella(s) (clic de nuevo para 0)"></i>`).join('')}
                        <button type="button" id="swal-clear-stars" style="margin-left:6px; font-size:0.75rem; padding:3px 8px; border-radius:6px; border:1px solid #cbd5e1; background:#f8fafc; color:#64748b; cursor:pointer;">0 ★</button>
                    </div>

                    <label style="font-weight:600; display:block;">Observación / Adjetivo (Uso Interno):</label>
                    <input type="text" id="swal-client-notes" class="swal2-input" maxlength="120" placeholder="Ej: Amable, Puntual, Exigente..." style="margin:4px 0 0 0; width:100%; height:36px; font-size:0.88rem;" value="${initNotes.replace(/"/g, '&quot;')}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Guardar Cliente',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#2563eb',
            didOpen: () => {
                const stars = Swal.getPopup().querySelectorAll('#swal-star-container i');
                const clearBtn = Swal.getPopup().querySelector('#swal-clear-stars');

                const updateStarsUI = (newVal) => {
                    document.getElementById('swal-client-rating').value = newVal;
                    stars.forEach(s => {
                        s.className = (parseInt(s.getAttribute('data-star')) <= newVal) ? 'bx bxs-star' : 'bx bx-star';
                    });
                };

                stars.forEach(st => {
                    st.addEventListener('click', function() {
                        const clickedVal = parseInt(this.getAttribute('data-star'));
                        const currentVal = parseInt(document.getElementById('swal-client-rating').value) || 0;
                        const newVal = (clickedVal === currentVal) ? 0 : clickedVal;
                        updateStarsUI(newVal);
                    });
                });

                if (clearBtn) {
                    clearBtn.addEventListener('click', () => updateStarsUI(0));
                }
            },
            preConfirm: () => {
                const dni     = document.getElementById('swal-client-dni').value.trim();
                const name    = document.getElementById('swal-client-name').value.trim();
                const phone   = document.getElementById('swal-client-phone').value.trim();
                const email   = document.getElementById('swal-client-email').value.trim();
                const address = document.getElementById('swal-client-address').value.trim() || 'Puerto Ordaz';
                const ratingRaw = parseInt(document.getElementById('swal-client-rating').value);
                const rating  = !isNaN(ratingRaw) ? ratingRaw : 5;
                const notes   = document.getElementById('swal-client-notes').value.trim();

                if (!dni || !name || !phone) {
                    Swal.showValidationMessage('El DNI, el Nombre y el Teléfono del cliente son obligatorios');
                    return false;
                }
                return { dni, name, phone, email, address, rating, notes };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/admin/clients/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        ajax_modal: 1,
                        id_client: currentSelectedClient.id_client || 0,
                        client_id_number: result.value.dni,
                        client_name: result.value.name,
                        client_phone: result.value.phone,
                        client_email: result.value.email,
                        client_address: result.value.address,
                        rating: result.value.rating,
                        notes: result.value.notes
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        currentSelectedClient = {
                            id_client: res.id_client,
                            client_id_number: res.client_id_number,
                            name: res.name,
                            client_phone: res.client_phone,
                            client_email: res.client_email,
                            client_address: res.client_address,
                            rating: res.rating,
                            notes: res.notes
                        };
                        if (nameEl) nameEl.value = res.name;
                        const match = res.client_id_number.match(/^([A-Za-z])(\d+)$/);
                        if (match && prefixEl && prefixEl.tagName.toLowerCase() === 'select') {
                            prefixEl.value = match[1].toUpperCase();
                            digitsEl.value = match[2];
                        } else if (digitsEl) {
                            digitsEl.value = res.client_id_number;
                        }

                        fillClientContactInputs(currentSelectedClient, true);

                        const idx = clientsCatalog.findIndex(c => parseInt(c.id_client) === parseInt(res.id_client));
                        if (idx !== -1) {
                            clientsCatalog[idx].client_id_number = res.client_id_number;
                            clientsCatalog[idx].client_name = res.name;
                            clientsCatalog[idx].client_phone = res.client_phone;
                            clientsCatalog[idx].client_email = res.client_email;
                            clientsCatalog[idx].client_address = res.client_address;
                            clientsCatalog[idx].rating = res.rating;
                            clientsCatalog[idx].notes = res.notes;
                        } else {
                            clientsCatalog.push({
                                id_client: res.id_client,
                                client_id_number: res.client_id_number,
                                client_name: res.name,
                                client_phone: res.client_phone,
                                client_email: res.client_email,
                                client_address: res.client_address,
                                rating: res.rating,
                                notes: res.notes
                            });
                        }
                        renderClientQuickInfo(res.rating, res.notes);
                        updateSummaryPanel();
                        Swal.fire({ icon: 'success', title: 'Cliente actualizado', timer: 1200, showConfirmButton: false });
                    } else {
                        Swal.fire('Error', res.message || 'No se pudo actualizar el cliente.', 'error');
                    }
                });
            }
        });
    };
</script>