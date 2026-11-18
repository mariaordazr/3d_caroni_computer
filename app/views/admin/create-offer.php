<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $currencySymbol = htmlspecialchars($moneySymbol ?? '$');
    $listProducts = !empty($headerProducts) ? $headerProducts : ($products ?? []);
?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-product-page" method="POST" action="/admin/storeOffer" id="offer-form">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/offers">Productos en Oferta</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Nueva</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/offers" class="lvr-back-btn" title="Volver a Ofertas">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Agregar Nueva Oferta</h1>
                </div>
                <span class="lvr-draft-badge">OFERTA-BORRADOR</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. DETALLES DE LA OFERTA -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-gift'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Configuración de la promoción</h3>
                                <p>Selecciona el producto disponible y define su valor de oferta</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-fields-grid-2">
                        <div class="lvr-field-group lvr-field-full">
                            <label class="lvr-field-label" for="product"><i class='bx bx-package'></i> Producto</label>
                            <select id="product" name="product" class="lvr-select" required>
                                <option value="" disabled selected>Selecciona un producto del catálogo</option>
                                <?php foreach($listProducts as $prod): ?>
                                    <?php 
                                        $imgUrl = !empty($prod['file_name']) 
                                            ? ((strpos($prod['file_name'], 'http') === 0) ? $prod['file_name'] : '/assets/uploads/products/' . $prod['file_name']) 
                                            : '';
                                    ?>
                                    <option value="<?php echo $prod['id_product']; ?>"
                                            data-name="<?php echo htmlspecialchars($prod['name']); ?>"
                                            data-sku="<?php echo htmlspecialchars($prod['sku'] ?? '—'); ?>"
                                            data-price="<?php echo (float)($prod['original_price'] ?? 0); ?>"
                                            data-stock="<?php echo (int)($prod['stock'] ?? 0); ?>"
                                            data-image="<?php echo htmlspecialchars($imgUrl); ?>">
                                        <?php echo htmlspecialchars(ucfirst(mb_strtolower($prod['name']))); ?> (Stock: <?php echo (int)$prod['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="message"><i class='bx bx-message-square-detail'></i> Mensaje para la oferta</label>
                            <input type="text" id="message" name="message" class="lvr-input" value="OFERTA ESPECIAL" placeholder="Ej. OFERTA ESPECIAL" required>
                        </div>

                        <div class="lvr-field-group">
                            <label class="lvr-field-label" for="price"><i class='bx bx-dollar'></i> Precio de la oferta (<?php echo $currencySymbol; ?>)</label>
                            <div class="lvr-price-input-wrap">
                                <span class="lvr-currency-prefix"><?php echo $currencySymbol; ?></span>
                                <input type="number" id="price" name="price" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-thumb-box">
                        <i class='bx bx-image'></i>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Producto</span>
                        <strong id="summary-prod-name">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>SKU</span>
                        <strong id="summary-prod-sku">—</strong>
                    </div>
                    <div class="lvr-summary-row">
                        <span>Precio original</span>
                        <strong id="summary-orig-price"><?php echo $currencySymbol; ?>0.00</strong>
                    </div>

                    <div class="lvr-summary-pill definitive">
                        <span>Precio oferta</span>
                        <strong id="summary-offer-price"><?php echo $currencySymbol; ?>0.00</strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Campos obligatorios</span>
                            <strong id="summary-req-count">0/2</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-req-fill"></div>
                        </div>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar oferta
                    </button>

                    <a href="/admin/offers" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const CURRENCY = <?php echo json_encode($moneySymbol ?? '$'); ?>;
    const prodSelect = document.getElementById('product');
    const priceInput = document.getElementById('price');

    function updateSummary() {
        const opt = prodSelect && prodSelect.selectedIndex > 0 ? prodSelect.options[prodSelect.selectedIndex] : null;
        const pVal = priceInput ? (parseFloat(priceInput.value) || 0) : 0;

        document.getElementById('summary-prod-name').textContent  = opt ? opt.getAttribute('data-name') : '—';
        document.getElementById('summary-prod-sku').textContent   = opt ? opt.getAttribute('data-sku') : '—';
        const origPrice = opt ? (parseFloat(opt.getAttribute('data-price')) || 0) : 0;
        document.getElementById('summary-orig-price').textContent = `${CURRENCY}${origPrice.toFixed(2)}`;
        document.getElementById('summary-offer-price').textContent = `${CURRENCY}${pVal.toFixed(2)}`;

        const thumbBox = document.getElementById('summary-thumb-box');
        const imgUrl = opt ? opt.getAttribute('data-image') : '';
        if (imgUrl) {
            thumbBox.innerHTML = `<img src="${imgUrl}" alt="Producto">`;
        } else {
            thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
        }

        let req = 0;
        if (opt) req++;
        if (pVal > 0) req++;

        document.getElementById('summary-req-count').textContent = `${req}/2`;
        const fill = document.getElementById('summary-req-fill');
        fill.style.width = `${Math.round((req / 2) * 100)}%`;
        fill.style.backgroundColor = req === 2 ? '#16a34a' : '#2563eb';
    }

    if (prodSelect) prodSelect.addEventListener('change', updateSummary);
    if (priceInput) priceInput.addEventListener('input', updateSummary);
    updateSummary();
});
</script>