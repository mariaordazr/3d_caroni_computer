<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $logoName = $logoHeader['content_value'] ?? null; 
    $hasLogo = !empty($logoName);
    $initialPayCount = !empty($paymentLogos) ? count($paymentLogos) : 0;
?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-cms-page" method="POST" action="/admin/pages/general/update" id="general-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">General</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Personalización General</h1>
                </div>
                <span class="lvr-draft-badge">CONFIGURACIÓN GLOBAL</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS (Formulario a la izquierda, Resumen a la derecha) -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. IDENTIDAD DEL SITIO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-buildings'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Identidad del sitio</h3>
                                <p>Logo principal de la cabecera, símbolo de moneda y texto de copyright</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-brand-identity-grid">
                        <!-- Logo del sitio -->
                        <div class="lvr-field-group">
                            <label class="lvr-field-label"><i class='bx bx-image'></i> Logo principal</label>
                            <div class="image-manager">
                                <div class="image-slots-container">
                                    <div class="image-slot <?php echo $hasLogo ? 'has-preview' : ''; ?>" id="general-logo-slot">
                                        <input type="hidden" name="current_logo_name" value="<?php echo $hasLogo ? htmlspecialchars($logoName) : ''; ?>">
                                        <input type="hidden" name="delete_logo" value=""> 
                                        
                                        <label for="logo-input">
                                            <?php if ($hasLogo): ?>
                                                <div class="image-preview-slot-frame">
                                                    <img src="/assets/uploads/logo/<?php echo htmlspecialchars($logoName); ?>" class="image-preview-slot" style="object-fit: contain;">
                                                    <button type="button" class="remove-preview-btn"><i class='bx bx-x'></i></button> 
                                                </div>
                                            <?php else: ?>
                                                <div class="image-placeholder-slot">
                                                    <i class='bx bx-image-add'></i>
                                                    <span>Subir Logo</span>
                                                </div>
                                            <?php endif; ?>
                                        </label>
                                        <input type="file" name="image_logo" id="logo-input" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Moneda y Copyright -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div class="lvr-field-group">
                                <label for="money_symbol" class="lvr-field-label"><i class='bx bx-dollar-circle'></i> Tipo de moneda</label>
                                <input type="text" name="money_symbol" id="money_symbol" class="lvr-input form-control" value="<?php echo htmlspecialchars($moneySymbol ?? ''); ?>" placeholder="Ej: $ o S/">
                            </div>

                            <div class="lvr-field-group">
                                <label for="copyright" class="lvr-field-label"><i class='bx bx-copyright'></i> Texto Copyright (Footer)</label>
                                <input type="text" name="copyright" id="copyright" class="lvr-input form-control" value="<?php echo htmlspecialchars($copyrightText ?? ''); ?>" placeholder="Ej: CORPORACIÓN GRUPONET SAC">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CONTACTO DIRECTO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-phone-call'></i>
                                <span class="lvr-step-num">2</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Contacto directo</h3>
                                <p>Teléfono de atención por WhatsApp y ubicación física del local</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <div class="lvr-field-group">
                            <label for="phone" class="lvr-field-label"><i class='bx bxl-whatsapp'></i> Número de Celular / WhatsApp</label>
                            <input type="text" name="contact_number" id="phone" class="lvr-input form-control" value="<?php echo htmlspecialchars($contactNumber ?? ''); ?>" placeholder="Ej: 584121234567">
                        </div>

                        <div class="lvr-field-group">
                            <label for="address" class="lvr-field-label"><i class='bx bx-map'></i> Dirección Física</label>
                            <input type="text" name="location_address" id="address" class="lvr-input form-control" value="<?php echo htmlspecialchars($locationAddress ?? ''); ?>" placeholder="Ej: Puerto Ordaz, Estado Bolívar">
                        </div>
                    </div>
                </div>

                <!-- 3. REDES SOCIALES -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-share-alt'></i>
                                <span class="lvr-step-num">3</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Redes sociales</h3>
                                <p>Enlaces oficiales · Deja un campo en blanco para ocultar su icono en la web</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <div class="lvr-field-group">
                            <label for="fb_link" class="lvr-field-label"><i class='bx bxl-facebook-square'></i> Facebook URL</label>
                            <input type="text" name="facebook_link" id="fb_link" class="lvr-input form-control js-social-input" value="<?php echo htmlspecialchars($facebook ?? ''); ?>" placeholder="https://facebook.com/...">
                        </div>

                        <div class="lvr-field-group">
                            <label for="ig_link" class="lvr-field-label"><i class='bx bxl-instagram-alt'></i> Instagram URL</label>
                            <input type="text" name="instagram_link" id="ig_link" class="lvr-input form-control js-social-input" value="<?php echo htmlspecialchars($instagram ?? ''); ?>" placeholder="https://instagram.com/...">
                        </div>

                        <div class="lvr-field-group">
                            <label for="yt_link" class="lvr-field-label"><i class='bx bxl-youtube'></i> YouTube URL</label>
                            <input type="text" name="youtube_link" id="yt_link" class="lvr-input form-control js-social-input" value="<?php echo htmlspecialchars($youtube ?? ''); ?>" placeholder="https://youtube.com/...">
                        </div>

                        <div class="lvr-field-group">
                            <label for="tt_link" class="lvr-field-label"><i class='bx bxl-tiktok'></i> TikTok URL</label>
                            <input type="text" name="tiktok_link" id="tt_link" class="lvr-input form-control js-social-input" value="<?php echo htmlspecialchars($tiktok ?? ''); ?>" placeholder="https://tiktok.com/@...">
                        </div>
                    </div>
                </div>

                <!-- 4. MÉTODOS DE PAGO (LOGOS FOOTER) -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-credit-card'></i>
                                <span class="lvr-step-num">4</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Métodos de pago (Logos Footer)</h3>
                                <p>Sube o elimina los iconos de medios de pago que se muestran en el pie de página</p>
                            </div>
                        </div>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" id="payments-container">
                            <?php 
                            $currentIndex = 0; 
                            if (!empty($paymentLogos)): 
                                foreach ($paymentLogos as $pay):
                                    $imageName = $pay['content_value'];
                            ?>
                                    <div class="image-slot has-preview" data-index="<?php echo $currentIndex; ?>">
                                        <input type="hidden" class="slot-image-id" name="slot_image_id_<?php echo $currentIndex; ?>" value="<?php echo htmlspecialchars($imageName); ?>">
                                        <input type="hidden" class="delete-slot-flag" name="delete_image_<?php echo $currentIndex; ?>" value="">
                                    
                                        <label for="image-slot-<?php echo $currentIndex; ?>">
                                            <div class="image-preview-slot-frame">
                                                <img src="/assets/uploads/payments/<?php echo htmlspecialchars($imageName); ?>" class="image-preview-slot">
                                                <button type="button" class="remove-preview-btn" data-slot-index="<?php echo $currentIndex; ?>"><i class='bx bx-x'></i></button> 
                                            </div>
                                        </label>
                                        <input type="file" name="image_slot_<?php echo $currentIndex; ?>" id="image-slot-<?php echo $currentIndex; ?>" accept="image/*" style="display:none;">
                                    </div>
                            <?php 
                                    $currentIndex++;
                                endforeach; 
                            endif; 
                            ?>
                            <div class="image-slot" id="btn-add-payment">
                                <label>
                                    <div class="image-placeholder-slot">
                                        <i class='bx bx-image-add'></i>
                                        <span>Galería</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-general-logo-box">
                        <?php if ($hasLogo): ?>
                            <img src="/assets/uploads/logo/<?php echo htmlspecialchars($logoName); ?>" alt="Logo actual">
                        <?php else: ?>
                            <i class='bx bx-image'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Moneda</span>
                        <strong id="summary-general-currency"><?php echo !empty($moneySymbol) ? htmlspecialchars($moneySymbol) : '—'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>WhatsApp</span>
                        <strong id="summary-general-phone"><?php echo !empty($contactNumber) ? htmlspecialchars($contactNumber) : '—'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Dirección</span>
                        <strong id="summary-general-address"><?php echo !empty($locationAddress) ? htmlspecialchars($locationAddress) : '—'; ?></strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Redes activas</span>
                        <strong id="summary-general-socials">0 / 4</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Logos de pago</span>
                        <strong id="summary-general-payments"><?php echo $initialPayCount; ?></strong>
                    </div>

                    <button type="submit" class="lvr-btn-save-note" style="margin-top: 0.45rem;">
                        <i class='bx bx-check'></i> Guardar configuración
                    </button>

                    <a href="/admin/products" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const moneyInput   = document.getElementById('money_symbol');
        const phoneInput   = document.getElementById('phone');
        const addressInput = document.getElementById('address');
        const socialInputs = document.querySelectorAll('.js-social-input');
        const logoSlot     = document.getElementById('general-logo-slot');
        const payContainer = document.getElementById('payments-container');

        function updateGeneralSummary() {
            const currencyEl = document.getElementById('summary-general-currency');
            const phoneEl    = document.getElementById('summary-general-phone');
            const addressEl  = document.getElementById('summary-general-address');
            const socialsEl  = document.getElementById('summary-general-socials');
            const paymentsEl = document.getElementById('summary-general-payments');
            const logoBox    = document.getElementById('summary-general-logo-box');

            if (currencyEl && moneyInput) {
                currencyEl.textContent = moneyInput.value.trim() !== '' ? moneyInput.value.trim() : '—';
            }
            if (phoneEl && phoneInput) {
                phoneEl.textContent = phoneInput.value.trim() !== '' ? phoneInput.value.trim() : '—';
            }
            if (addressEl && addressInput) {
                addressEl.textContent = addressInput.value.trim() !== '' ? addressInput.value.trim() : '—';
            }

            if (socialsEl) {
                let activeSocials = 0;
                socialInputs.forEach(inp => {
                    if (inp.value.trim() !== '') activeSocials++;
                });
                socialsEl.textContent = `${activeSocials} / 4`;
            }

            if (paymentsEl && payContainer) {
                const activePaySlots = payContainer.querySelectorAll('.image-slot.has-preview');
                paymentsEl.textContent = activePaySlots.length;
            }

            if (logoBox && logoSlot) {
                const img = logoSlot.querySelector('img.image-preview-slot');
                if (img && img.src && logoSlot.classList.contains('has-preview')) {
                    logoBox.innerHTML = `<img src="${img.src}" alt="Logo">`;
                } else {
                    logoBox.innerHTML = `<i class='bx bx-image'></i>`;
                }
            }
        }

        [moneyInput, phoneInput, addressInput, ...socialInputs].forEach(el => {
            if (el) el.addEventListener('input', updateGeneralSummary);
        });

        [logoSlot, payContainer].forEach(container => {
            if (container) {
                const obs = new MutationObserver(() => setTimeout(updateGeneralSummary, 60));
                obs.observe(container, { childList: true, subtree: true, attributes: true });
                container.addEventListener('change', () => setTimeout(updateGeneralSummary, 120));
            }
        });

        updateGeneralSummary();
    });
</script>