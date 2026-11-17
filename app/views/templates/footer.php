</main>
    <?php
        require_once ROOT_PATH . 'app/config/bcv_rate.php';
        $bcvData = getVenezuelaBcvRate();
        if ($bcvData !== null):
    ?>
        <style>
            .lvr-bcv-float {
                position: fixed !important;
                bottom: 22px !important;
                left: 22px !important;
                z-index: 99999 !important;
                display: inline-flex !important;
                align-items: center;
                gap: 10px;
                background: #0f172a;
                color: #ffffff;
                padding: 8px 14px 8px 10px;
                border-radius: 9999px;
                border: 1px solid rgba(255, 255, 255, 0.16);
                box-shadow: 0 10px 25px rgba(15, 23, 42, 0.32);
                font-family: 'Manrope', system-ui, -apple-system, sans-serif;
                text-decoration: none;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
                user-select: none;
            }
            .lvr-bcv-float:hover {
                transform: translateY(-2px);
                background: #1e293b;
                box-shadow: 0 14px 30px rgba(37, 99, 235, 0.32);
            }
            .lvr-bcv-float__icon {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #2563eb;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 0.92rem;
                flex-shrink: 0;
            }
            .lvr-bcv-float__info {
                display: flex;
                flex-direction: column;
                line-height: 1.15;
                text-align: left;
            }
            .lvr-bcv-float__label {
                font-size: 0.68rem;
                color: #94a3b8;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }
            .lvr-bcv-float__value {
                font-size: 0.88rem;
                font-weight: 700;
                color: #ffffff;
            }
            @media (max-width: 640px) {
                .lvr-bcv-float {
                    bottom: 16px !important;
                    left: 14px !important;
                    padding: 6px 12px 6px 8px;
                    gap: 8px;
                }
                .lvr-bcv-float__icon {
                    width: 28px;
                    height: 28px;
                    font-size: 0.8rem;
                }
                .lvr-bcv-float__value {
                    font-size: 0.82rem;
                }
            }
        </style>

        <div class="lvr-bcv-float" id="lvr-bcv-widget" title="Tasa oficial BCV vigente · Clic para copiar">
            <span class="lvr-bcv-float__icon">$</span>
            <div class="lvr-bcv-float__info">
                <span class="lvr-bcv-float__label" id="lvr-bcv-label">BCV · <?php echo htmlspecialchars($bcvData['date_text']); ?></span>
                <span class="lvr-bcv-float__value" id="lvr-bcv-value">Bs. <?php echo htmlspecialchars($bcvData['formatted']); ?></span>
            </div>
        </div>

        <script>
            (function() {
                const bcvBtn   = document.getElementById('lvr-bcv-widget');
                const labelEl  = document.getElementById('lvr-bcv-label');
                const valueEl  = document.getElementById('lvr-bcv-value');
                if (!bcvBtn || !labelEl || !valueEl) return;

                let currentRateText = <?php echo json_encode($bcvData['formatted']); ?>;
                const needsAjax     = <?php echo json_encode(!empty($bcvData['needs_ajax'])); ?>;

                // Si el servidor cPanel tenía bloqueadas las salidas cURL, el navegador obtiene la tasa oficial BCV y la guarda en caché local del día
                if (needsAjax || currentRateText === '...') {
                    const todayVzla = new Date().toLocaleDateString('en-CA', { timeZone: 'America/Caracas' }); // YYYY-MM-DD
                    const savedJson = localStorage.getItem('lvr_bcv_cache_v1');
                    let cacheObj = savedJson ? JSON.parse(savedJson) : {};

                    if (cacheObj.staged_rate && cacheObj.staged_date && cacheObj.staged_date <= todayVzla) {
                        cacheObj.active_rate = cacheObj.staged_rate;
                        cacheObj.active_date = cacheObj.staged_date;
                        delete cacheObj.staged_rate;
                        delete cacheObj.staged_date;
                        localStorage.setItem('lvr_bcv_cache_v1', JSON.stringify(cacheObj));
                    }

                    const renderClientRate = (rateNum, dateIso) => {
                        const formatted = Number(rateNum).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        const parts = (dateIso || todayVzla).split('-');
                        const dText = parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : todayVzla;
                        currentRateText = formatted;
                        labelEl.textContent = `BCV · ${dText}`;
                        valueEl.textContent = `Bs. ${formatted}`;
                    };

                    if (cacheObj.checked_day === todayVzla && cacheObj.active_rate) {
                        renderClientRate(cacheObj.active_rate, cacheObj.active_date);
                    } else {
                        fetch('https://ve.dolarapi.com/v1/dolares/oficial')
                            .then(r => r.json())
                            .then(data => {
                                if (data && data.promedio) {
                                    const apiDate = (data.fechaActualizacion || '').substring(0, 10) || todayVzla;
                                    if (apiDate <= todayVzla) {
                                        cacheObj.active_rate = data.promedio;
                                        cacheObj.active_date = apiDate;
                                    } else {
                                        cacheObj.staged_rate = data.promedio;
                                        cacheObj.staged_date = apiDate;
                                        if (!cacheObj.active_rate) {
                                            cacheObj.active_rate = data.promedio;
                                            cacheObj.active_date = todayVzla;
                                        }
                                    }
                                    cacheObj.checked_day = todayVzla;
                                    localStorage.setItem('lvr_bcv_cache_v1', JSON.stringify(cacheObj));
                                    renderClientRate(cacheObj.active_rate, cacheObj.active_date);
                                }
                            })
                            .catch(() => {});
                    }
                }

                bcvBtn.addEventListener('click', function() {
                    if (navigator.clipboard && currentRateText && currentRateText !== '...') {
                        navigator.clipboard.writeText(currentRateText);
                    }
                    const prevText = labelEl.textContent;
                    labelEl.textContent = '¡Tasa copiada!';
                    labelEl.style.color = '#4ade80';
                    setTimeout(() => {
                        labelEl.textContent = prevText;
                        labelEl.style.color = '';
                    }, 1800);
                });
            })();
        </script>
    <?php endif; ?>
    <section class="features-section lvr-footer">
        <div class="header-container">
            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-box__icon">
                        <i class='bx bx-shield-quarter' aria-hidden="true"></i>
                    </div>
                    <div class="feature-box__body">
                        <h3 class="feature-box__title">Compra con Seguridad</h3>
                        <p class="feature-box__text">Contamos productos de calidad garantizada.</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-box__icon">
                        <i class='bx bx-rocket' aria-hidden="true"></i>
                    </div>
                    <div class="feature-box__body">
                        <h3 class="feature-box__title">Envíos a Nivel Nacional</h3>
                        <p class="feature-box__text">Recibe tus productos en la puerta de tu casa.</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-box__icon">
                        <i class='bx bx-support' aria-hidden="true"></i>
                    </div>
                    <div class="feature-box__body">
                        <h3 class="feature-box__title">Soporte Especializado</h3>
                        <p class="feature-box__text">Tenemos un equipo capacitado para servirte.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer lvr-footer">
        <div class="header-container">
            <div class="footer-main-grid">
                <div class="footer-column">
                    <h3 class="footer-column__title">Información</h3>
                    <ul class="footer-column__list">
                        <li><?php echo htmlspecialchars($locationAddress);?></li>
                        <li>Celular: +<?php echo htmlspecialchars($contactPhone);?></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-column__title">Servicios</h3>
                    <ul class="footer-column__list">
                        <li><a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, me interesa el servicio electrónico" target="_blank">Servicio Electrónico</a></li>
                        <li><a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, me interesa el servicio técnico" target="_blank">Servicio Técnico</a></li>
                        <li><a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, me interesa la instalación y software" target="_blank">Instalación y Software</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-column__title">Sobre Nosotros</h3>
                    <ul class="footer-column__list">
                        <li><a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, tengo una consulta" target="_blank">Centro de Ayuda</a></li>
                        <li><a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, tengo una consulta" target="_blank">Atención al Cliente</a></li>
                        <li><a href="/about-us">Nosotros</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 class="footer-column__title">Métodos de Pago</h3>
                    <div class="footer-logos-container">

                        <?php foreach ($paymentMethods as $pay) { 
                            $originalFile = $pay; // ej: prod_123.jpg
                            $nameWithoutExt = pathinfo($originalFile, PATHINFO_FILENAME); // ej: prod_123
                            $smallFile = 'small_' . $nameWithoutExt . '.webp'; // ej: small_prod_123.webp
                        ?>
                            <picture>
                                <source srcset="/assets/uploads/payments/<?php echo $smallFile; ?>">
                                <img src="/assets/uploads/payments/<?php echo $originalFile; ?>"
                                alt="Método de pago"
                                loading="<?php echo $first ? 'eager' : 'lazy'; ?>"
                                <?php echo $first ? 'fetchpriority="high"' : ''; ?>
                                >
                            </picture>
                        <?php } ?>
                    </div>
                </div>
                <div class="footer-column">
                    <h3 class="footer-column__title">¡Contáctanos!</h3>
                    <ul class="footer-column__list">
                        <li>
                            <div class="footer-social-icons">
                                <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola equipo de <?php echo htmlspecialchars($copyrightText);?>, tengo una consulta" target="_blank" title="WhatsApp"><i class='bx bxl-whatsapp' aria-hidden="true"></i></a>
                                <a href="<?php echo htmlspecialchars($facebook); ?>" target="_blank" title="Facebook"><i class='bx bxl-facebook-square' aria-hidden="true"></i></a>
                                <a href="<?php echo htmlspecialchars($instagram); ?>" target="_blank" title="Instagram"><i class='bx bxl-instagram-alt' aria-hidden="true"></i></a>
                                <a href="<?php echo htmlspecialchars($youtube); ?>" target="_blank" title="YouTube"><i class='bx bxl-youtube' aria-hidden="true"></i></a>
                                <a href="<?php echo htmlspecialchars($tiktok); ?>" target="_blank" title="TikTok"><i class='bx bxl-tiktok' aria-hidden="true"></i></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-copyright">
            <div class="header-container">
                <span>
                    ©<?php echo date('Y'); ?> 
                    <?php echo htmlspecialchars($copyrightText);?><C class="A"></C>.
                    es un negocio independiente. Las materiales comerciales mencionadas pertenecen a sus respectivos fabricantes. | Sitio web creado por: 
                    <a href="https://mariaordaz.dev" target="_blank">mariaordaz.dev</a>
                </span>
            </div>
        </div>
    </footer>
    <div class="floating-buttons-container lvr-footer">
        <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/\D+/', '', $contactPhone));?>?text=Hola,%20quisiera%20más%20información." class="float-btn btn-whatsapp" target="_blank" title="Contáctanos por WhatsApp" aria-label="Contáctanos por WhatsApp">
            <i class='bx bxl-whatsapp' aria-hidden="true"></i>
        </a>
        
        <button id="scrollTopBtn" class="float-btn btn-scroll-top" title="Ir arriba" aria-label="Ir arriba">
            <i class='bx bx-up-arrow-alt' aria-hidden="true"></i>
        </button>
    </div>

    <script src="/assets/js/scrollreveal.min.js" defer></script>
    <script src="/assets/js/swiper-bundle.min.js" ></script>
    
    <script src="/assets/js/main.js" defer></script>
    
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>

</body>
</html>