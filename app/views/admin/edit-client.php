<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>
<section class="content-panel">
    <form class="edit-form" method="POST" action="/admin/clients/update">
        <div class="admin-header-row" style="margin-bottom: 1.5rem;">
            <h3>Editar Cliente</h3>
        </div>
        <input type="hidden" name="id_client" value="<?php echo $client['id_client']; ?>">
        <div class="form-grid" style="margin-bottom: 2rem;">
            <div class="form-column">
                <label>Identificación (DNI / RIF):</label>
                <input type="text" name="client_id_number" value="<?php echo htmlspecialchars($client['client_id_number']); ?>" required style="width: 100%;">

                <label>Teléfono WhatsApp (Obligatorio):</label>
                <input type="tel" name="client_phone" placeholder="Ej: 0412-1234567" value="<?php echo htmlspecialchars($client['client_phone'] ?? ''); ?>" required style="width: 100%;">

                <label>Dirección:</label>
                <input type="text" name="client_address" placeholder="Calle, ciudad, estado" value="<?php echo htmlspecialchars(!empty(trim($client['client_address'] ?? '')) ? $client['client_address'] : 'Puerto Ordaz'); ?>" style="width: 100%;">

                <label>Calificación del Cliente (0 a 5 Estrellas - Uso Interno):</label>
                <?php $currentRating = isset($client['rating']) ? max(0, min(5, (int)$client['rating'])) : 0; ?>
                <input type="hidden" name="rating" id="client-rating-input" value="<?php echo $currentRating; ?>">
                <div id="star-selector" style="display: flex; align-items: center; gap: 6px; font-size: 1.8rem; color: #f59e0b; cursor: pointer; margin-top: 4px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class='bx <?php echo ($i <= $currentRating) ? 'bxs-star' : 'bx-star'; ?>' data-val="<?php echo $i; ?>" onclick="setClientRating(<?php echo $i; ?>)" title="<?php echo $i; ?> estrella(s) (clic de nuevo para 0)"></i>
                    <?php endfor; ?>
                    <button type="button" onclick="setClientRating(0, true)" style="margin-left: 6px; font-size: 0.75rem; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #64748b; cursor: pointer;">0 ★</button>
                </div>
            </div>
            <div class="form-column">
                <label>Nombre y Apellido / Razón Social:</label>
                <input type="text" name="client_name" value="<?php echo htmlspecialchars($client['client_name']); ?>" required style="width: 100%;">

                <label>Correo Electrónico (Opcional):</label>
                <input type="email" name="client_email" placeholder="cliente@correo.com" value="<?php echo htmlspecialchars($client['client_email'] ?? ''); ?>" style="width: 100%;">

                <label>Observación Corta / Adjetivo (Uso Interno):</label>
                <input type="text" name="notes" maxlength="120" placeholder="Ej: Puntual, Cliente frecuente, Exigente..." value="<?php echo htmlspecialchars($client['notes'] ?? ''); ?>" style="width: 100%;">
            </div>
        </div>
        <script>
            function setClientRating(val, forceZero = false) {
                const input = document.getElementById('client-rating-input');
                const currentVal = parseInt(input.value) || 0;
                const newVal = forceZero ? 0 : (val === currentVal ? 0 : val);
                input.value = newVal;
                document.querySelectorAll('#star-selector i').forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-val'));
                    star.className = (sVal <= newVal) ? 'bx bxs-star' : 'bx bx-star';
                });
            }
        </script>
<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>