<?php include ROOT_PATH . 'app/views/templates/header.php'; ?>

<section class="home-section lvr-home" id="home">
    
    <div class="full-width-container lvr-container">
        <div class="lvr-hero">
            <div class="swiper home-slider">
                <div class="swiper-wrapper">
                    <?php $first = true;
                    foreach ($sliderImages as $imageName): ?>
                        <div class="swiper-slide">
                            <?php
                                $originalFile = $imageName; // ej: prod_123.jpg
                                $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME); // ej: prod_123
                                $smallFile = 'small_' . $nameWithoutExt . '.webp'; // ej: small_prod_123.webp
                            ?>
                            <picture>
                                <source media="(max-width: 768px)" srcset="/assets/uploads/home/slider/<?php echo rawurlencode($smallFile); ?>">
                                <img src="/assets/uploads/home/slider/<?php echo rawurlencode($originalFile); ?>"
                                alt="Slider de Grupo Caroní Computer"
                                loading="<?php echo $first ? 'eager' : 'lazy'; ?>"
                                <?php echo $first ? 'fetchpriority="high"' : ''; ?>
                                width="1200" height="400"
                                >
                            </picture>
                        </div>
                    <?php $first = false;
                    endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

            <!-- Overlay SIEMPRE visible sobre cualquier slide (N1) — textos literales de Lovable (N2) -->
            <div class="lvr-hero__overlay">
                <span class="lvr-hero__badge">+500 repuestos en stock</span>
                <h1 class="lvr-hero__title">Revive tu laptop con la pieza exacta.</h1>
                <p class="lvr-hero__subtitle">Teclados, pantallas, chips y herramientas certificadas. Si no sabes cuál necesitas, te asesoramos por WhatsApp.</p>
                <div class="lvr-hero__ctas">
                    <a class="lvr-hero__cta lvr-hero__cta--primary" href="/productos">Ver productos <i class='bx bx-chevron-right' aria-hidden="true"></i></a>
                    <a class="lvr-hero__cta lvr-hero__cta--outline" href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone)); ?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, quiero pedir asesoría" target="_blank" rel="noopener"><i class='bx bxl-whatsapp' aria-hidden="true"></i> Pedir asesoría</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container overlap-container">

        <h2 class="lvr-section-title">Compra por categoría</h2>
        <div class="category-grid" tabindex="0" role="region" aria-label="Categorías destacadas">
            <?php foreach ($featuredCategories as $category): ?>
                <a href="/productos?search=&filter_choice=cat-<?php echo $category['id_category']; ?>&sort=name-asc" class="category-card">
                    <div class="category-card__image">
                        <?php if (!empty($category['image'])): ?>
                            <?php 
                                $originalFile = $category['image']; // ej: prod_123.jpg
                                $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME); // ej: prod_123
                                $smallFile = 'small_' . $nameWithoutExt . '.webp'; // ej: small_prod_123.webp
                            ?>
                            <picture>
                                <source srcset="/assets/uploads/categories/<?php echo rawurlencode($smallFile); ?>">
                                <img src="/assets/uploads/categories/<?php echo rawurlencode($originalFile); ?>"
                                alt="<?php echo htmlspecialchars($category['name']); ?>"
                                loading="lazy"
                                width="300" height="300" 
                                >
                            </picture>
                        <?php else: ?>
                            <img src="/assets/categories/default.jpg"
                            alt="<?php echo htmlspecialchars($category['name']); ?>"
                            loading="lazy" 
                            width="500" height="500" 
                            >
                        <?php endif; ?>
                    </div>
                    <p class="category-card__title"><?php echo htmlspecialchars(ucwords(strtolower($category['name']))); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php if (!empty($newProducts)): ?>
    <section class="section lvr-home">
        <div class="container products-container">
            <div class="swiper product-page-carousel">
                <h2 class="section__subtitle product-carousel-title_new">Recién llegados</h2>
                <div class="swiper-wrapper">
                    <?php foreach ($newProducts as $newProduct): ?>
                        <div class="swiper-slide">
                            <span class="product-card__tag discount--new">NUEVO</span>
                            <a href="/producto/<?php echo $newProduct['id_product']; ?>" class="product-card">
                                <div class="product-card__inner">
                                    <div class="product-card__image swiper-no-dragging">
                                        <?php 
                                            $originalFile = !empty($newProduct['file_name']) ? $newProduct['file_name'] : 'default.jpg'; 
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
                                                alt="<?php echo htmlspecialchars($newProduct['name']); ?>"
                                                loading="lazy"
                                                referrerpolicy="no-referrer">
                                        </picture>
                                    </div>
                                    <div class="product-card__content">
                                        <p class="info__meta"><?php echo htmlspecialchars($newProduct['category_name']); ?> · <?php echo htmlspecialchars($newProduct['brand_name']); ?></p>
                                        <p class="product-card__title"><?php echo htmlspecialchars($newProduct['name']); ?></p>
                                        <div class="product-card__price">
                                            
                                            <?php if ($showPrice=='1'): ?>
                                                <div class="price-line">
                                                    <span><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($newProduct['original_price'], 2)); ?></span>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
<?php endif; ?>


<section class="section lvr-home">
    <div class="container overlap-container1">

        <div class="brand-grid" tabindex="0" role="region" aria-label="Materiales destacadas">
            <?php foreach ($featuredBrands as $brand): ?>
                <a href="/productos?search=&filter_choice=brand-<?php echo $brand['id_brand']; ?>" class="brand-card">
                    <div class="brand-card__image">
                        <?php if (!empty($brand['image'])): ?>
                            <?php 
                                $originalFile = $brand['image']; // ej: prod_123.jpg
                                $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME); // ej: prod_123
                                $smallFile = 'small_' . $nameWithoutExt . '.webp'; // ej: small_prod_123.webp
                            ?>
                            <picture>
                                <source srcset="/assets/uploads/brands/<?php echo rawurlencode($smallFile); ?>">
                                <img src="/assets/uploads/brands/<?php echo rawurlencode($originalFile); ?>"
                                alt="<?php echo htmlspecialchars($brand['name']); ?>"
                                loading="lazy" 
                                width="300" height="300"
                                >
                            </picture>
                        <?php else: ?>
                            <img src="/assets/brands/default.jpg"
                            alt="<?php echo htmlspecialchars($brand['name']); ?>"
                            loading="lazy" 
                            width="300" height="300" 
                            >
                        <?php endif; ?>
                    </div>
                    <p class="brand-card__title"><?php echo htmlspecialchars(ucwords(strtolower($brand['name']))); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($offers)): ?>
    <section class="section lvr-home">
        <div class="container products-container">
            <div class="swiper product-page-carousel">
                <h2 class="section__subtitle product-carousel-title_offer">Ofertas destacadas</h2>
                <div class="swiper-wrapper">
                    <?php foreach ($offers as $offer): ?>
                        <div class="swiper-slide">
                            <span class="product-card__tag discount--offer">OFERTA</span>
                            <a href="/producto/<?php echo $offer['id_product']; ?>" class="product-card">
                                <div class="product-card__inner">     
                                    <div class="product-card__image swiper-no-dragging">
                                        <?php 
                                            $originalFile = !empty($offer['file_name']) ? $offer['file_name'] : 'default.jpg'; 
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
                                                alt="<?php echo htmlspecialchars($offer['name']); ?>"
                                                loading="lazy"
                                                referrerpolicy="no-referrer">
                                        </picture>
                                    </div>
                                    <div class="product-card__content">
                                        <p class="info__meta"><?php echo htmlspecialchars($offer['category_name']); ?> · <?php echo htmlspecialchars($offer['brand_name']); ?></p>
                                        <p class="product-card__title"><?php echo htmlspecialchars($offer['name']); ?></p>
                                        
                                        <?php if ($showPrice=='1'): ?>
                                            <div class="product-card__price">
                                                <?php if ($offer['offer_price'] !== null && $offer['offer_price'] < $offer['original_price']): ?>
                                                    <span class="original-price"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($offer['original_price'], 2)); ?></span>
                                                    <div class="offer-line">
                                                        <span class="offer-price"><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($offer['offer_price'], 2)); ?></span>
                                                        <?php $discount = round((($offer['original_price'] - $offer['offer_price']) / $offer['original_price']) * 100); ?>
                                                        <span class="product-card__discount">-<?php echo $discount; ?>%</span>
                                                    </div>
                                                <?php else: ?>
                                                    <span><?php echo htmlspecialchars($moneySymbol); ?> <?php echo htmlspecialchars(number_format($offer['original_price'], 2)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>
    </section>
    
<?php endif; ?>

<script src="/assets/js/home.js" defer></script>

<?php include ROOT_PATH . 'app/views/templates/footer.php'; ?>