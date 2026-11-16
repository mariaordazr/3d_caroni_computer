<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Recibo - <?php echo htmlspecialchars($data['receipt_code']); ?></title>
    <style>
        /* CSS Clásico y Profesional (Gris/Negro) */
        body { font-family: 'Inter', Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        
        /* --- HEADER --- */
        .header-table { margin-top: 55px; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .company-info { text-align: right; line-height: 1.4; }
        .company-name { font-size: 16px; font-weight: bold; color: #000; margin-bottom: 5px; text-transform: uppercase;}
        
        /* --- META (Recibimos de / Nota de recibo) --- */
        .meta-table { margin-bottom: 20px; }
        .meta-table td { vertical-align: top; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9; }
        .box-title { font-weight: bold; font-size: 13px; color: #000; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 4px; text-transform: uppercase; }
        
        /* --- TABLAS DE EQUIPOS Y PRESUPUESTO --- */
        .items-table { margin-bottom: 20px; }
        .items-table th { background-color: #333; color: white; font-weight: bold; padding: 8px 5px; text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #333;}
        .items-table td { border: 1px solid #ccc; padding: 8px 5px; vertical-align: top; }
        
        /* --- NOTAS IMPORTANTES --- */
        .notes-section { margin-top: 15px; font-size: 10px; line-height: 1.4;}
        .notes-label { font-weight: bold; color: #000; }
        .notes-block { margin-bottom: 8px; }
        
        /* --- FOOTER INLINE (Pegado al contenido) --- */
        @page { margin: 40px 40px 40px 40px; } /* Márgenes uniformes */
        .pdf-footer-clean { margin-top: 40px; font-size: 9px; text-align: center; color: #777; border-top: 1px solid #ccc; padding-top: 5px; }
    </style>
</head>
<body>
    <main>
        <table class="header-table">
            <tr>
                <td style="width: 40%;">
                    <?php
                        $logoPath = ROOT_PATH . 'public/assets/uploads/logo/gruponet.webp';
                        if (file_exists($logoPath)) {
                            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $dataImg = file_get_contents($logoPath);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
                            echo '<img src="' . $base64 . '" style="max-width: 180px; max-height: 70px;">';
                        } else {
                            echo '<h2 style="color:#000; margin:0;">CARONI COMPUTER</h2>';
                        }
                    ?>
                </td>
                <td style="width: 60%;" class="company-info">
                    <div class="company-name"><?php echo htmlspecialchars($settings['company_name'] ?? 'GRUPO CARONI COMPUTER C.A'); ?></div>
                    RIF/ID: <?php echo htmlspecialchars($settings['company_id'] ?? ''); ?><br>
                    Teléfono: <?php echo htmlspecialchars($settings['company_phone_1'] ?? ''); ?> <?php echo !empty($settings['company_phone_2']) ? ' / ' . htmlspecialchars($settings['company_phone_2']) : ''; ?><br>
                    Email: <?php echo htmlspecialchars($settings['company_email'] ?? ''); ?><br>
                    Dirección: <?php echo htmlspecialchars($settings['company_address'] ?? ''); ?>
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td style="width: 60%; border-right: none;">
                    <div class="box-title">RECIBIMOS DE:</div>
                    <strong><?php echo htmlspecialchars($data['client_name']); ?></strong><br>
                    C.I./DNI: <?php echo htmlspecialchars($data['client_id_number']); ?><br>
                    Teléfono: <?php echo htmlspecialchars($data['client_phone']); ?><br>
                    Email: <?php echo htmlspecialchars($data['client_email']); ?><br>
                    Dirección: <?php echo htmlspecialchars($data['client_address']); ?>
                </td>
                <td style="width: 40%;">
                    <div class="box-title">NOTA DE RECIBO</div>
                    <strong>ID:</strong> <?php echo htmlspecialchars($data['receipt_code']); ?><br>
                    <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($data['created_at'])); ?>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Und.</th>
                    <th style="width: 15%;">Modelo</th>
                    <th style="width: 15%;">S/N</th>
                    <th style="width: 20%;">Complementos</th>
                    <th style="width: 20%;">Detalles físicos</th>
                    <th style="width: 25%;">Características y fallas</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // EXTRACCIÓN SEGURA DE EQUIPOS
                    $jsonItemsToDecode = '[]';
                    if (!empty($data['items_json'])) { $jsonItemsToDecode = $data['items_json']; }
                    elseif (!empty($itemsJson)) { $jsonItemsToDecode = $itemsJson; }
                    $safeItems = !empty($items) ? $items : (json_decode($jsonItemsToDecode, true) ?: []);
                ?>
                <?php if(!empty($safeItems)): foreach($safeItems as $item): ?>
                <tr>
                    <td style="text-align: center;"><?php echo htmlspecialchars($item['und'] ?? ''); ?></td>
                    <td>
                        <?php echo htmlspecialchars($item['model'] ?? ''); ?>
                        <?php if (!empty($item['delivered'])): ?>
                            <div style="margin-top: 3px;">
                                <span style="display: inline-block; background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 9px; font-weight: bold; padding: 2px 6px; border-radius: 8px; text-transform: uppercase;">
                                    ✓ Equipo entregado
                                </span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['serial'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['accessories'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['details'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($item['features'] ?? ''); ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No hay equipos registrados</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($data['delivery_time'])): ?>
            <div style="margin-bottom: 15px; font-size: 12px; color: #000;">
                <strong>Tiempo estimado de diagnóstico:</strong> <?php echo htmlspecialchars($data['delivery_time']); ?>
            </div>
        <?php endif; ?>

        <?php 
            // EXTRACCIÓN A PRUEBA DE BALAS DE PRESUPUESTOS
            $jsonToDecode = '{}';
            if (!empty($data['budget_json'])) {
                $jsonToDecode = $data['budget_json'];
            } elseif (!empty($budgetJson)) { // Variable inyectada en store/update
                $jsonToDecode = $budgetJson;
            }

            $budgetParsed = json_decode($jsonToDecode, true) ?: [];

            $budgetList = [];
            $finalAmountPdf = '00.00';
            $tentativeList = [];
            $tentativeFinalAmountPdf = '';

            // Detectamos la estructura JSON nueva o vieja directamente de la fuente cruda
            if (isset($budgetParsed['items']) || isset($budgetParsed['tentative_items'])) {
                $budgetList = $budgetParsed['items'] ?? [];
                $finalAmountPdf = $budgetParsed['final_amount'] ?? '00.00';
                $tentativeList = $budgetParsed['tentative_items'] ?? [];
                $tentativeFinalAmountPdf = $budgetParsed['tentative_final_amount'] ?? '';
            } elseif (is_array($budgetParsed) && !empty($budgetParsed)) {
                $budgetList = $budgetParsed;
                $finalAmountPdf = $data['final_amount'] ?? '00.00';
            }

            // VALIDACIÓN DEFINITIVA
            $hasDefinitive = false;
            if (is_array($budgetList)) {
                foreach($budgetList as $b) {
                    $desc = trim($b['description'] ?? '');
                    $amt = trim($b['amount'] ?? '');
                    if (!empty($desc) || !in_array($amt, ['', '0', '0.00', '0.00 - 0.00'])) {
                        $hasDefinitive = true; break;
                    }
                }
            }

            // VALIDACIÓN TENTATIVA
            $hasTentative = false;
            if (is_array($tentativeList)) {
                foreach($tentativeList as $t) {
                    $desc = trim($t['description'] ?? '');
                    $amt = trim($t['amount'] ?? '');
                    if (!empty($desc) || !in_array($amt, ['', '0', '0.00', '0.00 - 0.00'])) {
                        $hasTentative = true; break;
                    }
                }
            }
        ?>


        <?php if ($hasTentative): ?>
            <div style="font-weight: bold; font-size: 12px; color: #000;">PRESUPUESTO TENTATIVO:</div>
            <div style="font-size: 10px; color: #666; margin-bottom: 5px; font-style: italic;">* Este presupuesto inicial está sujeto a cambios</div>
            
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 65%;">Descripción</th>
                        <th style="width: 35%;">Monto (<?php echo htmlspecialchars($moneySymbol); ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tentativeList as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['amount'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!$hasDefinitive && ($data['payment_status'] !== 'unpaid' && $data['payment_status'] !== 'paid')): ?>
                <div style="text-align: right; margin-bottom: 15px; font-size: 12px; color: #0d6efd;">
                    <strong>ABONADO: <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(substr($data['payment_status'] ?? '', 8)); ?></strong>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($tentativeFinalAmountPdf)): ?>
                <div style="text-align: right; font-weight: bold; font-size: 14px; color: #000; margin-top: 8px; padding-right: 5px; margin-bottom: 20px;">
                    MONTO FINAL (TENTATIVO): <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars($tentativeFinalAmountPdf); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($hasDefinitive): ?>
            <?php
                $isPublicView = !empty($isPublic);
                $anyRowHasAsk = false;
                $askMinTotal = 0;
                $askMaxTotal = 0;
                $askHasRange = false;

                foreach ($budgetList as $bItem) {
                    if (!empty($bItem['ask_client'])) {
                        $anyRowHasAsk = true;
                        $rawAmt = trim($bItem['amount'] ?? '0');
                        if (strpos($rawAmt, '-') !== false) {
                            $askHasRange = true;
                            $parts = explode('-', $rawAmt);
                            $askMinTotal += (float)trim($parts[0]);
                            $askMaxTotal += (float)trim($parts[1] ?? $parts[0]);
                        } else {
                            $val = (float)$rawAmt;
                            $askMinTotal += $val;
                            $askMaxTotal += $val;
                        }
                    }
                }

                if ($askHasRange && $askMinTotal != $askMaxTotal) {
                    $defaultHalfDepositText = $moneySymbol . ' ' . number_format($askMinTotal / 2, 2, '.', '') . ' - ' . number_format($askMaxTotal / 2, 2, '.', '');
                } else {
                    $defaultHalfDepositText = $moneySymbol . ' ' . number_format($askMinTotal / 2, 2, '.', '');
                }

                $companyWaPhone = preg_replace('/\D+/', '', $settings['company_phone_1'] ?? '');
                if (strpos($companyWaPhone, '0') === 0) {
                    $companyWaPhone = '58' . substr($companyWaPhone, 1);
                }
                $defaultWaMsg = "Hola, deseo consultar los métodos de pago para realizar el abono del 50% ({$defaultHalfDepositText}) de la nota " . ($data['receipt_code'] ?? '') . ".";
                $defaultWaHref = "https://wa.me/{$companyWaPhone}?text=" . rawurlencode($defaultWaMsg);
            ?>

            <?php if ($anyRowHasAsk && $isPublicView): ?>
                <!-- CUADRO DE ABONO DEL 50% ANTES DEL PRESUPUESTO DEFINITIVO -->
                <div id="budget-confirmation-box" class="no-print" style="display: block; background-color: #ffbaba; border: 1px solid #fb6363; border-left: 4px solid #b60f0f; padding: 12px 15px; border-radius: 6px; margin-bottom: 14px; font-size: 12px; color: #5c0d0d; line-height: 1.5; transition: all 0.25s ease;">
                    Si desea hacer la confirmación del servicio se debe realizar un abono del <strong>50%</strong>, en este caso el monto es <strong id="half-deposit-amount" style="color: #a70707; font-size: 13px;"><?php echo htmlspecialchars($defaultHalfDepositText); ?></strong>. 
                    Puede consultar nuestros métodos de pago <a id="wa-deposit-link" href="<?php echo htmlspecialchars($defaultWaHref); ?>" target="_blank" style="color: #a70707; font-weight: bold; text-decoration: underline;">aquí</a>.
                </div>
            <?php endif; ?>

            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px; color: #0d6efd;">PRESUPUESTO DEFINITIVO:</div>
            
            <table class="items-table" id="public-definitive-table">
                <thead>
                    <tr>
                        <th style="width: <?php echo $anyRowHasAsk ? '50%' : '65%'; ?>; background-color: #0d6efd !important; border: 1px solid #0d6efd !important;">Descripción</th>
                        <th style="width: 25%; background-color: #0d6efd !important; border: 1px solid #0d6efd !important;">Monto (<?php echo htmlspecialchars($moneySymbol); ?>)</th>
                        <?php if ($anyRowHasAsk): ?>
                            <th style="width: 25%; text-align: center; background-color: #0d6efd !important; border: 1px solid #0d6efd !important;">
                                <?php echo $isPublicView ? '¿Acepta el presupuesto?' : 'Respuesta del cliente'; ?>
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($budgetList as $idx => $item): ?>
                        <?php 
                            $askThisRow = !empty($item['ask_client']); 
                            $savedChoice = $item['client_choice'] ?? ''; // 'accepted', 'rejected', o ''
                            $isAnswered = in_array($savedChoice, ['accepted', 'rejected']);
                        ?>
                        <tr class="definitive-item-row" 
                            data-row-index="<?php echo $idx; ?>"
                            data-amount="<?php echo htmlspecialchars($item['amount'] ?? '0'); ?>" 
                            data-ask="<?php echo $askThisRow ? '1' : '0'; ?>"
                            data-accepted="<?php echo ($savedChoice === 'accepted') ? '1' : '0'; ?>">
                            <td><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item['amount'] ?? ''); ?></td>
                            <?php if ($anyRowHasAsk): ?>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php if ($askThisRow): ?>
                                        <?php if ($isPublicView): ?>
                                            <div class="no-print-inline">
                                                <div style="display: inline-flex; gap: 6px; justify-content: center;">
                                                    <button type="button" 
                                                        class="btn-client-choice" 
                                                        data-choice="accepted" 
                                                        onclick="handleClientBudgetChoice(this, 'accepted')" 
                                                        <?php echo $isAnswered ? 'disabled' : ''; ?>
                                                        style="padding: 4px 10px; border-radius: 15px; border: 1px solid #16a34a; font-size: 11px; font-weight: 600; transition: all 0.2s; <?php echo ($savedChoice === 'accepted') ? 'background: #16a34a; color: #fff; cursor: not-allowed;' : 'background: #fff; color: #16a34a; ' . ($isAnswered ? 'opacity: 0.4; cursor: not-allowed;' : 'cursor: pointer;'); ?>">
                                                        <?php echo ($savedChoice === 'accepted') ? 'Aceptado' : 'Sí, acepto'; ?>
                                                    </button>
                                                    <button type="button" 
                                                        class="btn-client-choice" 
                                                        data-choice="rejected" 
                                                        onclick="handleClientBudgetChoice(this, 'rejected')" 
                                                        <?php echo $isAnswered ? 'disabled' : ''; ?>
                                                        style="padding: 4px 10px; border-radius: 15px; border: 1px solid #dc2626; font-size: 11px; font-weight: 600; transition: all 0.2s; <?php echo ($savedChoice === 'rejected') ? 'background: #dc2626; color: #fff; cursor: not-allowed;' : 'background: #fff; color: #dc2626; ' . ($isAnswered ? 'opacity: 0.4; cursor: not-allowed;' : 'cursor: pointer;'); ?>">
                                                        <?php echo ($savedChoice === 'rejected') ? 'Rechazado' : 'No'; ?>
                                                    </button>
                                                </div>
                                                <div class="undo-choice-wrapper" style="margin-top: 4px; <?php echo $isAnswered ? 'display: block;' : 'display: none;'; ?>">
                                                    <a href="#" onclick="handleClientBudgetChoice(this, ''); return false;" style="font-size: 10px; color: #64748b; text-decoration: underline;">
                                                        Deshacer respuesta
                                                    </a>
                                                </div>
                                            </div>

                                            <span class="only-print-inline print-status-label" style="font-weight: bold; font-size: 10px; color: <?php echo ($savedChoice === 'accepted') ? '#16a34a' : (($savedChoice === 'rejected') ? '#dc2626' : '#64748b'); ?>;">
                                                <?php 
                                                    if ($savedChoice === 'accepted') echo 'Aceptado';
                                                    elseif ($savedChoice === 'rejected') echo 'Rechazado';
                                                    else echo 'Sin respuesta';
                                                ?>
                                            </span>
                                        <?php else: ?>
                                            <?php if ($savedChoice === 'accepted'): ?>
                                                <span style="color: #16a34a; font-weight: bold; font-size: 11px;">✓ Aceptado</span>
                                            <?php elseif ($savedChoice === 'rejected'): ?>
                                                <span style="color: #dc2626; font-weight: bold; font-size: 11px;">✕ Rechazado</span>
                                            <?php else: ?>
                                                <span style="color: #64748b; font-weight: bold; font-size: 11px;">⏳ Sin respuesta</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #888; font-size: 10px;">Incluido</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (($data['payment_status'] !== 'unpaid' && $data['payment_status'] !== 'paid')): ?>
                <div style="text-align: right; margin-bottom: 15px; font-size: 12px; color: #0d6efd;">
                    <strong>ABONADO: <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(substr($data['payment_status'] ?? '', 8)); ?></strong>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($finalAmountPdf) && $finalAmountPdf !== '00.00'): ?>
                <div style="text-align: right; font-weight: bold; font-size: 14px; color: #000; margin-top: 8px; padding-right: 5px; margin-bottom: 20px;">
                    MONTO FINAL (DEFINITIVO): <?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars($finalAmountPdf); ?>
                </div>
            <?php endif; ?>

            <?php if ($anyRowHasAsk && $isPublicView): ?>
                <script>
                    async function handleClientBudgetChoice(el, choice) {
                        const row = el.closest('tr');
                        const rowIndex = parseInt(row.getAttribute('data-row-index'));
                        const btnYes = row.querySelector('.btn-client-choice[data-choice="accepted"]');
                        const btnNo = row.querySelector('.btn-client-choice[data-choice="rejected"]');
                        const undoWrapper = row.querySelector('.undo-choice-wrapper');
                        const printLabel = row.querySelector('.print-status-label');
                        const accessToken = <?php echo json_encode($data['access_token'] ?? ''); ?>;

                        if (choice === 'accepted') {
                            btnYes.style.background = '#16a34a';
                            btnYes.style.color = '#fff';
                            btnYes.style.opacity = '1';
                            btnYes.style.cursor = 'not-allowed';
                            btnYes.textContent = 'Aceptado';
                            btnYes.disabled = true;

                            btnNo.style.background = '#fff';
                            btnNo.style.color = '#dc2626';
                            btnNo.style.opacity = '0.4';
                            btnNo.style.cursor = 'not-allowed';
                            btnNo.textContent = 'No';
                            btnNo.disabled = true;

                            undoWrapper.style.display = 'block';
                            row.setAttribute('data-accepted', '1');
                            if (printLabel) { printLabel.textContent = 'Aceptado'; printLabel.style.color = '#16a34a'; }
                        } else if (choice === 'rejected') {
                            btnNo.style.background = '#dc2626';
                            btnNo.style.color = '#fff';
                            btnNo.style.opacity = '1';
                            btnNo.style.cursor = 'not-allowed';
                            btnNo.textContent = 'Rechazado';
                            btnNo.disabled = true;

                            btnYes.style.background = '#fff';
                            btnYes.style.color = '#16a34a';
                            btnYes.style.opacity = '0.4';
                            btnYes.style.cursor = 'not-allowed';
                            btnYes.textContent = 'Sí, acepto';
                            btnYes.disabled = true;

                            undoWrapper.style.display = 'block';
                            row.setAttribute('data-accepted', '0');
                            if (printLabel) { printLabel.textContent = 'Rechazado'; printLabel.style.color = '#dc2626'; }
                        } else {
                            btnYes.style.background = '#fff';
                            btnYes.style.color = '#16a34a';
                            btnYes.style.opacity = '1';
                            btnYes.style.cursor = 'pointer';
                            btnYes.textContent = 'Sí, acepto';
                            btnYes.disabled = false;

                            btnNo.style.background = '#fff';
                            btnNo.style.color = '#dc2626';
                            btnNo.style.opacity = '1';
                            btnNo.style.cursor = 'pointer';
                            btnNo.textContent = 'No';
                            btnNo.disabled = false;

                            undoWrapper.style.display = 'none';
                            row.setAttribute('data-accepted', '0');
                            if (printLabel) { printLabel.textContent = 'Sin respuesta'; printLabel.style.color = '#64748b'; }
                        }

                        recalculateClientDeposit();

                        if (accessToken) {
                            try {
                                await fetch('/nota/' + accessToken + '?save_choice=1', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({
                                        action: 'save_client_choice',
                                        row_index: rowIndex,
                                        choice: choice
                                    })
                                });
                            } catch (e) {
                                console.error('Error guardando respuesta del presupuesto:', e);
                            }
                        }
                    }

                    function recalculateClientDeposit() {
                        const rows = document.querySelectorAll('#public-definitive-table .definitive-item-row');
                        let minAcceptedSum = 0, maxAcceptedSum = 0, hasRangeAccepted = false, anyAccepted = false;
                        let minAllAskSum = 0, maxAllAskSum = 0, hasRangeAllAsk = false;

                        rows.forEach(r => {
                            const isAsk = r.getAttribute('data-ask') === '1';
                            const isAccepted = r.getAttribute('data-accepted') === '1';
                            const rawAmt = (r.getAttribute('data-amount') || '0').trim();

                            if (isAsk) {
                                if (rawAmt.includes('-')) {
                                    hasRangeAllAsk = true;
                                    const parts = rawAmt.split('-');
                                    minAllAskSum += parseFloat(parts[0]) || 0;
                                    maxAllAskSum += parseFloat(parts[1]) || 0;
                                } else {
                                    const val = parseFloat(rawAmt) || 0;
                                    minAllAskSum += val;
                                    maxAllAskSum += val;
                                }
                            }

                            if (isAsk && isAccepted) {
                                anyAccepted = true;
                                if (rawAmt.includes('-')) {
                                    hasRangeAccepted = true;
                                    const parts = rawAmt.split('-');
                                    minAcceptedSum += parseFloat(parts[0]) || 0;
                                    maxAcceptedSum += parseFloat(parts[1]) || 0;
                                } else {
                                    const val = parseFloat(rawAmt) || 0;
                                    minAcceptedSum += val;
                                    maxAcceptedSum += val;
                                }
                            }
                        });

                        const box = document.getElementById('budget-confirmation-box');
                        const amountSpan = document.getElementById('half-deposit-amount');
                        const waLink = document.getElementById('wa-deposit-link');
                        if (!box) return;

                        const moneySymbol = <?php echo json_encode($moneySymbol); ?>;
                        const receiptCode = <?php echo json_encode($data['receipt_code'] ?? ''); ?>;
                        const companyPhone = <?php echo json_encode($companyWaPhone); ?>;

                        let halfText = '';
                        if (anyAccepted) {
                            if (hasRangeAccepted && minAcceptedSum !== maxAcceptedSum) {
                                halfText = `${moneySymbol} ${(minAcceptedSum / 2).toFixed(2)} - ${(maxAcceptedSum / 2).toFixed(2)}`;
                            } else {
                                halfText = `${moneySymbol} ${(minAcceptedSum / 2).toFixed(2)}`;
                            }
                        } else {
                            if (hasRangeAllAsk && minAllAskSum !== maxAllAskSum) {
                                halfText = `${moneySymbol} ${(minAllAskSum / 2).toFixed(2)} - ${(maxAllAskSum / 2).toFixed(2)}`;
                            } else {
                                halfText = `${moneySymbol} ${(minAllAskSum / 2).toFixed(2)}`;
                            }
                        }

                        amountSpan.textContent = halfText;
                        const waMessage = `Hola, acepto el presupuesto de la nota ${receiptCode}. Deseo consultar los métodos de pago para realizar el abono del 50% correspondiente a ${halfText}.`;
                        waLink.href = `https://wa.me/${companyPhone}?text=${encodeURIComponent(waMessage)}`;
                        box.style.display = 'block';
                    }

                    document.addEventListener('DOMContentLoaded', recalculateClientDeposit);
                </script>
            <?php endif; ?>
        <?php endif; ?>


        <?php if(!empty($data['notes'])): ?>
            <div class="notes-section" style="font-size: 14px;">
                <span class="notes-label">OBSERVACIONES:</span><br>
                <?php 
                    // Si ya contiene etiquetas HTML del editor Quill, las imprimimos limpias; si es texto plano antiguo, usamos nl2br
                    if ($data['notes'] !== strip_tags($data['notes'])) {
                        echo strip_tags($data['notes'], '<p><br><strong><b><em><i><u><s><ul><ol><li><a><span><h1><h2><h3><h4>');
                    } else {
                        echo nl2br(htmlspecialchars($data['notes']));
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="notes-section" style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <?php if(!empty($settings['company_note'])): ?>
                <div class="notes-block">
                    <span class="notes-label">IMPORTANTE:</span> 
                    <?php echo nl2br(htmlspecialchars($settings['company_note'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($settings['company_condition'])): ?>
                <div class="notes-block">
                    <span class="notes-label">CONDICIÓN:</span> 
                    <?php echo nl2br(htmlspecialchars($settings['company_condition'])); ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($settings['company_warranty'])): ?>
                <div class="notes-block">
                    <span class="notes-label">GARANTÍA:</span> 
                    <?php echo nl2br(htmlspecialchars($settings['company_warranty'])); ?>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 20px; font-size: 9px; text-align: center; color: #777; border-top: 1px solid #ccc; padding-top: 5px;">
                Este documento constituye un control de mercancía recibida. | Generado el <?php echo date('d/m/Y H:i', strtotime($data['created_at'] ?? date('Y-m-d H:i:s'))); ?>
            </div>
        </div>
    </main>
</body>
</html>