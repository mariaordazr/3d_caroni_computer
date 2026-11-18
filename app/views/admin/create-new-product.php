<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $currencySymbol = htmlspecialchars($moneySymbol ?? '$');
    $listProducts = !empty($headerProducts) ? $headerProducts : ($products ?? []);
?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-product-page" method="POST" action="/admin/storeNewProduct" id="new-product-form">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <a href="/admin/new-products">Recién Llegados</a>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Nuevo</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <a href="/admin/new-products" class="lvr-back-btn" title="Volver a Recién Llegados">
                        <i class='bx bx-left-arrow-alt'></i>
                    </a>
                    <h1 class="lvr-page-title">Agregar Recién Llegado</h1>
                </div>
                <span class="lvr-draft-badge">RECIÉN-LLEGADO</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. SELECCIÓN DE PRODUCTO -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-star'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Destacar como recién llegado</h3>
                                <p>Selecciona el producto que aparecerá en el carrusel de novedades</p>
                            </div>
                        </div>
                    </div>

                    <div class="lvr-field-group">
                        <label class="lvr-field-label" for="product"><i class='bx bx-package'></i> Producto recién llegado</label>
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
                        <span>Stock</span>
                        <strong id="summary-prod-stock">0</strong>
                    </div>

                    <div class="lvr-summary-pill definitive">
                        <span>Precio</span>
                        <strong id="summary-prod-price"><?php echo $currencySymbol; ?>0.00</strong>
                    </div>

                    <div class="lvr-req-progress-box">
                        <div class="lvr-req-progress-top">
                            <span>Campos obligatorios</span>
                            <strong id="summary-req-count">0/1</strong>
                        </div>
                        <div class="lvr-req-progress-bar">
                            <div class="lvr-req-progress-fill" id="summary-req-fill"></div>
                        </div>
                    </div>

                    <button type="submit" class="lvr-btn-save-note">
                        <i class='bx bx-check'></i> Guardar recién llegado
                    </button>

                    <a href="/admin/new-products" class="lvr-btn-cancel-note">Cancelar</a>
                </div>
            </aside>

        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const CURRENCY = <?php echo json_encode($moneySymbol ?? '$'); ?>;
    const prodSelect = document.getElementById('product');

    function updateSummary() {
        const opt = prodSelect && prodSelect.selectedIndex > 0 ? prodSelect.options[prodSelect.selectedIndex] : null;

        document.getElementById('summary-prod-name').textContent  = opt ? opt.getAttribute('data-name') : '—';
        document.getElementById('summary-prod-sku').textContent   = opt ? opt.getAttribute('data-sku') : '—';
        document.getElementById('summary-prod-stock').textContent = opt ? opt.getAttribute('data-stock') : '0';
        const price = opt ? (parseFloat(opt.getAttribute('data-price')) || 0) : 0;
        document.getElementById('summary-prod-price').textContent = `${CURRENCY}${price.toFixed(2)}`;

        const thumbBox = document.getElementById('summary-thumb-box');
        const imgUrl = opt ? opt.getAttribute('data-image') : '';
        if (imgUrl) {
            thumbBox.innerHTML = `<img src="${imgUrl}" alt="Producto">`;
        } else {
            thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
        }

        const req = opt ? 1 : 0;
        document.getElementById('summary-req-count').textContent = `${req}/1`;
        const fill = document.getElementById('summary-req-fill');
        fill.style.width = `${req * 100}%`;
        fill.style.backgroundColor = req === 1 ? '#16a34a' : '#2563eb';
    }

    if (prodSelect) prodSelect.addEventListener('change', updateSummary);
    updateSummary();
});
</script>