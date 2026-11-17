<?php include ROOT_PATH . 'app/views/templates/header.php'; ?>

<main class="main-content lvr-home lvr-products">
    <?php
    // Spec 007 RF-4: N = productos realmente renderizados (misma expresión `hidden === 0` del bucle).
    $visibleCount = 0;
    foreach ((array) $products as $_p) {
        if ($_p['hidden'] === 0) {
            $visibleCount++;
        }
    }

    // Spec 007 RF-6: $filterSummary lo puebla la VISTA (ensamblado de presentación).
    $filterSummary = [];
    $chipSearch = $_GET['search'] ?? '';
    $chipFilter = $_GET['filter_choice'] ?? '';
    $chipType   = $_GET['type'] ?? '';

    if ($chipSearch !== '') {
        $filterSummary['search'] = $chipSearch;                    // texto literal (RF-6)
    }
    if ($chipFilter !== '') {
        // Resolver "cat-X" / "cat-X-brand-Y" con $categoriesWithData; formateo del sidebar.
        $chipResolved = null;
        if (preg_match('/^cat-(\d+)(?:-brand-(\d+))?$/', (string) $chipFilter, $_m)) {
            foreach ($categoriesWithData as $_cat) {
                if ((string) $_cat['id_category'] !== $_m[1]) continue;
                if (($_m[2] ?? '') === '') {
                    $chipResolved = $_cat['name'];
                } else {
                    foreach ($_cat['brands'] as $_br) {
                        if ((string) $_br['id_brand'] === $_m[2]) { $chipResolved = $_br['name']; break; }
                    }
                }
                break;
            }
        }
        $filterSummary['filter_choice'] = ucwords(strtolower($chipResolved !== null ? $chipResolved : (string) $chipFilter));
    }
    if ($chipType !== '') {
        if ($chipType === 'new')       $filterSummary['type'] = 'Recién llegados';  // §12(9) / E4d
        elseif ($chipType === 'offer') $filterSummary['type'] = 'Ofertas';
        else                           $filterSummary['type'] = $chipType;           // valor no estándar: crudo
    }
    // Textos se guardan CRUDOS; el escapado (E4a) ocurre al imprimir.
    ?>
    <div class="container">
        <div class="lvr-page-header">
            <nav class="lvr-breadcrumb" aria-label="migas de pan">
                <a href="/">Inicio</a>
                <span class="lvr-breadcrumb__sep" aria-hidden="true">›</span>
                <span class="lvr-breadcrumb__current" aria-current="page">Productos</span>
            </nav>
            <h1 class="lvr-page-title">Productos</h1>
        </div>
        <form id="filter-form" method="GET">
            <!-- Mantenemos la búsqueda activa -->
            <?php if (!empty($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
            <?php endif; ?>
            
            <!-- Mantenemos el tipo (Oferta/Nuevo) activo siempre que exista -->
            <?php if (!empty($_GET['type'])): ?>
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($_GET['type']); ?>">
            <?php endif; ?>
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort'] ?? 'name-asc'); ?>">
            <div class="products-page-layout">
                <div class="mobile-filters-container">
                <?php 
                    // Lógica auxiliar para saber qué categoría está seleccionada actualmente
                    $current_filter = $_GET['filter_choice'] ?? '';
                    $current_cat_id = null;
                    $current_brand_id = null;

                    // Analizamos el string tipo "cat-1" o "cat-1-brand-5"
                    if (strpos($current_filter, 'cat-') === 0) {
                        $parts = explode('-', $current_filter);
                        // parts[1] es el ID de categoría
                        if (isset($parts[1])) $current_cat_id = $parts[1];
                    }
                ?>

                <div class="mobile-select-group">
                    <label>Categoría:</label>
                    <select onchange="submitMobileFilter(this.value)" class="mobile-select">
                        <option value="" <?php echo $current_filter === '' ? 'selected' : ''; ?>>- Seleccionar Categoría -</option>
                        <?php foreach($categoriesWithData as $category): ?>
                            <?php 
                                $cat_val = "cat-" . $category['id_category']; 
                                $is_selected = ($current_cat_id == $category['id_category']);
                            ?>
                            <option value="<?php echo $cat_val; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucwords(strtolower($category['name']))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mobile-select-group">
                    <label>Material:</label>
                    <select onchange="submitMobileFilter(this.value)" class="mobile-select" <?php echo ($current_cat_id === null) ? 'disabled' : ''; ?>>
                        
                        <?php if ($current_cat_id === null): ?>
                            <option value="">Selecciona una categoría primero</option>
                        <?php else: ?>
                            <option value="cat-<?php echo $current_cat_id; ?>">Todos los materiales</option>
                            
                            <?php 
                            $selectedCategoryData = null;
                            foreach($categoriesWithData as $cat) {
                                if ($cat['id_category'] == $current_cat_id) {
                                    $selectedCategoryData = $cat;
                                    break;
                                }
                            }
                            ?>

                            <?php if ($selectedCategoryData): ?>
                                <?php foreach($selectedCategoryData['brands'] as $brand): ?>
                                    <?php 
                                        $brand_val = "cat-" . $current_cat_id . "-brand-" . $brand['id_brand'];
                                        $is_brand_selected = ($current_filter === $brand_val);
                                    ?>
                                    <option value="<?php echo $brand_val; ?>" <?php echo $is_brand_selected ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucwords(strtolower($brand['name']))); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        <?php endif; ?>
                    </select>
                </div>
                <div class="mobile-select-group">
                    <label for="sort-mobile">Ordenar por:</label>
                    <select name="sort" id="sort-mobile" class="mobile-select" >
                        <option value="name-asc" <?php echo ($_GET['sort'] ?? '') === 'name-asc' ? 'selected' : ''; ?>>Nombre (A-Z)</option>
                        <option value="name-desc" <?php echo ($_GET['sort'] ?? '') === 'name-desc' ? 'selected' : ''; ?>>Nombre (Z-A)</option>
                        <option value="price-asc" <?php echo ($_GET['sort'] ?? '') === 'price-asc' ? 'selected' : ''; ?>>Precio (Menor a Mayor)</option>
                        <option value="price-desc" <?php echo ($_GET['sort'] ?? '') === 'price-desc' ? 'selected' : ''; ?>>Precio (Mayor a Menor)</option>
                    </select>
                </div>

                <div class="mobile-select-group" style="width: 100%; align-items: center; justify-content: center;">
                    <a href="/productos" class="btn-clear-filters">Limpiar filtros</a>
                </div>
            </div>
                <aside class="filter-sidebar">

                    <?php 
                        // 1. Obtenemos el filtro actual
                        $current_filter = $_GET['filter_choice'] ?? ''; 
                    ?>
                    
                    <div class="filter-sidebar__header">
                        <h2>Filtros</h2>
                        <?php if (!empty($filterSummary)): // §12(10): "Limpiar" SÓLO con filtros activos ?>
                            <a href="/productos" class="filter-sidebar__clear">Limpiar</a>
                        <?php endif; ?>
                    </div>
                    <h3 class="filter-sidebar__title">Categorías</h3>
                    
                    <?php foreach($categoriesWithData as $category): ?>
                        <?php 
                            $category_value_prefix = "cat-" . $category['id_category'];

                            // Mantenemos el acordeón abierto si es la categoría activa O si contiene un material seleccionada
                            $is_open = ($current_filter === $category_value_prefix || strpos($current_filter, $category_value_prefix . '-') === 0);
                        ?>

                        <div class="filter-group accordion <?php echo $is_open ? 'is-open' : ''; ?>" id="category-<?php echo $category['id_category']; ?>">
                            
                            <?php 
                                // Valor del radio de CATEGORÍA
                                $category_value = "cat-" . $category['id_category'];
                                
                                // Check si la categoría está seleccionada (directamente o por sus hijos)
                                $is_category_checked = ($current_filter === $category_value || strpos($current_filter, $category_value . '-') === 0);
                            ?>
                            
                            <div class="filter-option-header">
                                <input 
                                    type="radio" 
                                    name="filter_choice" 
                                    value="<?php echo $category_value; ?>" 
                                    id="<?php echo $category_value; ?>"
                                    <?php echo $is_category_checked ? 'checked' : ''; ?>
                                    onchange="submitSidebarFilter()"> 

                                <span class="accordion__header" for="<?php echo $category_value; ?>">
                                    <h4><?php echo htmlspecialchars(ucwords(strtolower($category['name']))); ?></h4>
                                    <i class='bx bx-chevron-down accordion__icon'></i>
                                </span>
                            </div>

                            <div class="accordion__content">
                                <div class="filter-options-list nested-list">

                                    <div class="filter-option">
                                        <input 
                                            type="radio" 
                                            name="filter_choice" 
                                            value="<?php echo $category_value; ?>" 
                                            id="<?php echo $category_value; ?>-all"
                                            <?php echo ($current_filter === $category_value) ? 'checked' : ''; ?>
                                            onchange="submitSidebarFilter()"
                                        > 
                                        <label for="<?php echo $category_value; ?>-all">
                                            - Todos los materiales -
                                        </label>
                                    </div>

                                    <?php foreach($category['brands'] as $brand): ?>
                                        <?php 
                                            $brand_value = "cat-" . $category['id_category'] . "-brand-" . $brand['id_brand'];
                                            $is_brand_checked = ($current_filter === $brand_value);
                                        ?>
                                        <div class="filter-option">
                                            <input 
                                                type="radio" 
                                                name="filter_choice" 
                                                value="<?php echo $brand_value; ?>" 
                                                id="<?php echo $brand_value; ?>"
                                                <?php echo $is_brand_checked ? 'checked' : ''; ?>
                                                onchange="submitSidebarFilter()">
                                            
                                            <label for="<?php echo $brand_value; ?>">
                                                <?php echo htmlspecialchars(ucwords(strtolower($brand['name']))); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </aside>

                <div class="product-grid-container lvr-home">
                    <div class="product-grid-header">
                        <p class="toolbar-count"><strong><?php echo $visibleCount; ?></strong> productos encontrados</p>
                        <div class="toolbar-sort">
                            <span class="lvr-select-wrap">
                            <label for="sort">Ordenar por</label>
                            <select name="sort" id="sort" >
                                <option value="name-asc" <?php echo ($_GET['sort'] ?? '') === 'name-asc' ? 'selected' : ''; ?>>Nombre (A-Z)</option>
                                <option value="name-desc" <?php echo ($_GET['sort'] ?? '') === 'name-desc' ? 'selected' : ''; ?>>Nombre (Z-A)</option>
                                <option value="price-asc" <?php echo ($_GET['sort'] ?? '') === 'price-asc' ? 'selected' : ''; ?>>Precio (Menor a Mayor)</option>
                                <option value="price-desc" <?php echo ($_GET['sort'] ?? '') === 'price-desc' ? 'selected' : ''; ?>>Precio (Mayor a Menor)</option>
                            </select>
                            <i class='bx bx-chevron-down lvr-select-wrap__icon' aria-hidden="true"></i>
                            </span>

                            <a href="/productos" class="btn-clear-filters">Limpiar filtros</a>
                        </div>
                    </div>

                    <?php if (!empty($filterSummary)): ?>
                        <div class="lvr-chips">
                            <?php foreach ($filterSummary as $chipKey => $chipText): ?>
                                <?php $chipRemaining = http_build_query(array_diff_key($_GET, [$chipKey => ''])); // D-3: SOLO esa clave ?>
                                <span class="lvr-chip">
                                    <?php echo htmlspecialchars($chipText, ENT_QUOTES, 'UTF-8'); // E4a ?>
                                    <a class="lvr-chip__remove"
                                       href="/productos<?php echo $chipRemaining !== '' ? '?' . $chipRemaining : ''; ?>"
                                       aria-label="Quitar filtro <?php echo htmlspecialchars($chipText, ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class='bx bx-x' aria-hidden="true"></i> <!-- E4c: ícono único bx-x -->
                                    </a>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($products)): ?>
                        <div class="product-grid">
                            <?php foreach ($products as $product): ?>
                                <?php if ($product['hidden'] === 0): // Saltar productos ocultos y sin precios válidos ?>
                                    <article class="product-card">
                                        <div class="product-card__inner">
                                            <div class="product-card__image">
                                                <?php if (isset($product['certification']) && $product['certification'] === 'premium'): ?>
                                                    <img src="/assets/uploads/logo/premium-crown.png" class="premium-crown" alt="Producto Premium">
                                                <?php endif; ?>

                                                <?php 
                                                    $originalFile = !empty($product['file_name']) ? $product['file_name'] : 'default.jpg'; 
                                                    $isDrive = (strpos($originalFile, 'http://') === 0 || strpos($originalFile, 'https://') === 0);
                                                    $showWebp = false;

                                                    if ($isDrive) {
                                                        $fullSrc  = $originalFile;
                                                        $smallSrc = preg_replace('/=w\d+$/', '=w400', $originalFile);
                                                        $showWebp = true;
                                                    } else {
                                                        $fullSrc  = '/assets/uploads/products/' . rawurlencode($originalFile);
                                                        $smallSrc = $fullSrc;
                                                        if ($originalFile !== 'default.jpg') {
                                                            $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME);
                                                            $smallFile = 'small_' . $nameWithoutExt . '.webp';
                                                            $webpPath = ROOT_PATH . 'public/assets/uploads/products/' . $smallFile;
                                                            if (file_exists($webpPath)) {
                                                                $showWebp = true;
                                                                $smallSrc = '/assets/uploads/products/' . rawurlencode($smallFile);
                                                            }
                                                        }
                                                    }
                                                ?>
                                                <picture>
                                                    <?php if ($showWebp): ?>
                                                        <source srcset="<?php echo htmlspecialchars($smallSrc); ?>" <?php echo !$isDrive ? 'type="image/webp"' : ''; ?>>
                                                    <?php endif; ?>
                                                    
                                                    <img src="<?php echo htmlspecialchars($fullSrc); ?>"
                                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                        loading="lazy"
                                                        referrerpolicy="no-referrer">
                                                </picture>
                                            </div>
                                            <div class="product-card__content">
                                                <p class="info__meta">
                                                    <?php echo htmlspecialchars($product['category_name']); ?> - <?php echo htmlspecialchars($product['brand_name']); ?>
                                                </p>
                                                <p class="product-card__title"><a class="product-card__link" href="/producto/<?php echo $product['id_product']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                                    <?php
                                                        $prod_name = trim(mb_strtolower($product['name'], 'UTF-8'));
                                                        if ($prod_name !== '') {
                                                            $prod_name = mb_strtoupper(mb_substr($prod_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($prod_name, 1, null, 'UTF-8');
                                                        }
                                                        echo htmlspecialchars($prod_name);
                                                    ?>
                                                    <?php if(!empty($product['sku']) && $product['sku'] !== 0): ?>
                                                        <p class="info__meta">
                                                            SKU: <?php echo htmlspecialchars($product['sku']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </p>
                                                
                                                
                                                
                                                <?php if ($showPrice == '1' || $showStock == '1'): // RF-10: fila sólo si hay precio o pill ?>
                                                    <div class="product-card__price-row">
                                                <?php if ($showPrice=='1' && $product['original_price'] > 0): ?>
                                                    <div class="product-card__price">
                                                        <?php if ($product['offer_price'] !== null && $product['offer_price'] < $product['original_price']): ?>
                                                            <span class="original-price"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?></span>
                                                            <span class="product-card__tag discount--offer">OFERTA</span>
                                                            <div class="offer-line">
                                                                <span class="offer-price"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['offer_price'], 2)); ?></span>
                                                                <?php $discount = round((($product['original_price'] - $product['offer_price']) / $product['original_price']) * 100); ?>
                                                                <span class="product-card__discount">-<?php echo $discount; ?>%</span>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="price-line">
                                                                <span><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($product['original_price'], 2)); ?></span>
                                                                
                                                                <?php if ($product['id_new_product'] !== null): ?>
                                                                    <span class="product-card__tag discount--new">NUEVO</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                                
                                                <?php if ($showStock=='1'): ?>
                                                    <?php if ($product['stock'] > 0): ?>
                                                        <span class="lvr-badge lvr-badge--stock lvr-badge--available" aria-label="Disponible">DISPONIBLE</span>
                                                    <?php else: ?>
                                                        <span class="lvr-badge lvr-badge--stock lvr-badge--out-of-stock" aria-label="Agotado">AGOTADO</span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php $ctaText = !empty($product['name'])
                                                    ? 'Hola equipo de ' . htmlspecialchars($copyrightText) . ', quiero consultar por: ' . $product['name']  // §12(13)
                                                    : 'Hola equipo de ' . htmlspecialchars($copyrightText) . ', tengo una consulta'; // fallback patrón existente
                                                ?>
                                                <a class="product-card__cta"
                                                   href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone)); ?>?text=<?php echo urlencode($ctaText); ?>"
                                                   target="_blank" rel="noopener"
                                                   aria-label="Consultar por <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class='bx bxl-whatsapp' aria-hidden="true"></i>Consultar
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results-found">
                            <p class="no-results-found__message">No se encontraron productos</p>
                            <p>Intenta con otros términos de búsqueda o ajusta los filtros.</p>
                            <a href="/productos" class="no-results-found__clear">Limpiar filtros</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="/assets/js/products.js" defer></script>
<?php include ROOT_PATH . 'app/views/templates/footer.php'; ?>