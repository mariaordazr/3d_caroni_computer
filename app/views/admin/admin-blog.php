<?php include_once ROOT_PATH . 'app/views/admin/templates/header.php'; ?>

<?php 
    $item0 = $bannerImage ?? null; 
    $imageName0 = $item0['content_value'] ?? null; 
    $isExisting0 = !empty($imageName0);
?>

<section class="content-panel">
    <form class="edit-form lvr-receipt-page lvr-cms-page" method="POST" action="/admin/pages/blog/update" id="blog-page-form" enctype="multipart/form-data">

        <!-- TOPBAR: Breadcrumb + Título + Badge -->
        <div class="lvr-page-topbar">
            <div class="lvr-breadcrumb">
                <span>Gestión de Páginas</span>
                <i class='bx bx-chevron-right'></i>
                <span class="current">Blog</span>
            </div>
            <div class="lvr-title-row">
                <div class="lvr-title-left">
                    <h1 class="lvr-page-title">Gestión de Página: Blog</h1>
                </div>
                <span class="lvr-draft-badge">PORTADA DEL BLOG</span>
            </div>
        </div>

        <!-- LAYOUT DE 2 COLUMNAS -->
        <div class="lvr-form-layout">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lvr-form-main">

                <!-- 1. BANNER PRINCIPAL DEL BLOG -->
                <div class="lvr-step-card">
                    <div class="lvr-step-header">
                        <div class="lvr-step-header-left">
                            <div class="lvr-step-icon-wrap">
                                <i class='bx bx-news'></i>
                                <span class="lvr-step-num">1</span>
                            </div>
                            <div class="lvr-step-titles">
                                <h3>Banner principal del Blog</h3>
                                <p>Gestiona la imagen panorámica (3.5:1) del encabezado de la sección de artículos</p>
                            </div>
                        </div>
                        <a href="/admin/blogs" class="lvr-btn-soft-blue" style="text-decoration: none;">
                            <i class='bx bx-edit'></i> Gestionar Artículos
                        </a>
                    </div>

                    <div class="image-manager">
                        <div class="image-slots-container" style="justify-content: center;">
                            <div class="image-slot lvr-banner-wide-slot <?php echo $isExisting0 ? 'has-preview' : ''; ?>" id="blog-banner-slot">
                                <input type="hidden" class="slot-image-id" name="slot_image_id_banner" value="<?php echo $isExisting0 ? htmlspecialchars($imageName0) : ''; ?>">
                                <input type="hidden" class="delete-slot-flag" name="delete_image_banner" value="">
                                <label for="banner-slot">
                                    <?php if ($isExisting0): ?>
                                        <div class="image-preview-slot-frame">
                                            <img src="/assets/uploads/blog/banner/<?php echo htmlspecialchars($imageName0); ?>" class="image-preview-slot">
                                            <button type="button" class="remove-preview-btn"><i class='bx bx-x'></i></button> 
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder-slot">
                                            <i class='bx bx-image-add'></i>
                                            <span>Subir Banner del Blog</span>
                                        </div>
                                    <?php endif; ?>
                                </label>
                                <input type="file" name="blog_banner_image" id="banner-slot" accept="image/*" style="display:none;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- SIDEBAR DERECHO: RESUMEN -->
            <aside class="lvr-form-sidebar">
                <div class="lvr-summary-card">
                    <h4 class="lvr-summary-title">Resumen</h4>

                    <div class="lvr-summary-thumb-box" id="summary-blog-thumb-box">
                        <?php if ($isExisting0): ?>
                            <img src="/assets/uploads/blog/banner/<?php echo htmlspecialchars($imageName0); ?>" alt="Banner Blog">
                        <?php else: ?>
                            <i class='bx bx-image'></i>
                        <?php endif; ?>
                    </div>

                    <div class="lvr-summary-row">
                        <span>Banner del Blog</span>
                        <strong id="summary-blog-banner-status"><?php echo $isExisting0 ? 'Activo' : 'Sin imagen'; ?></strong>
                    </div>

                    <button type="submit" class="lvr-btn-save-note" style="margin-top: 0.45rem;">
                        <i class='bx bx-check'></i> Guardar banner
                    </button>

                    <a href="/admin/blogs" class="lvr-btn-cancel-note">Ir a lista de artículos</a>
                </div>
            </aside>

        </div>

        <!-- MODAL DE RECORTE (CROPPER.JS) -->
        <div id="crop-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.75); backdrop-filter: blur(3px); z-index:9999; align-items:center; justify-content:center;">
            <div style="background:#fff; padding:24px; border-radius:16px; width:90%; max-width:820px; display:flex; flex-direction:column; gap:16px; max-height:90vh; box-shadow: 0 20px 40px rgba(0,0,0,0.25);">
                <h3 id="crop-modal-title" style="margin:0; font-size:1.15rem; color:#0f172a;">Recortar Imagen</h3>
                <div style="max-height: 60vh; overflow: hidden; background:#f8fafc; border-radius:12px; display:flex; justify-content:center;">
                    <img id="image-to-crop" style="max-width: 100%; display: block;">
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" id="btn-cancel-crop" class="btn" style="background:#fee2e2; color:#dc2626; border-radius:999px; padding:8px 18px; font-weight:600; border:none; cursor:pointer;">Cancelar</button>
                    <button type="button" id="btn-crop-confirm" class="btn btn-primary" style="background:#2563eb; color:#fff; border-radius:999px; padding:8px 20px; font-weight:600; border:none; cursor:pointer;">Recortar y Usar</button>
                </div>
            </div>
        </div>

<?php include_once ROOT_PATH . 'app/views/admin/templates/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerSlot = document.getElementById('blog-banner-slot');

        function updateBlogPageSummary() {
            if (!bannerSlot) return;
            const hasBanner = bannerSlot.classList.contains('has-preview');
            const statusEl  = document.getElementById('summary-blog-banner-status');
            const thumbBox  = document.getElementById('summary-blog-thumb-box');

            if (statusEl) statusEl.textContent = hasBanner ? 'Activo' : 'Sin imagen';
            if (thumbBox) {
                const img = bannerSlot.querySelector('img.image-preview-slot');
                if (hasBanner && img && img.src) {
                    thumbBox.innerHTML = `<img src="${img.src}" alt="Banner Blog">`;
                } else {
                    thumbBox.innerHTML = `<i class='bx bx-image'></i>`;
                }
            }
        }

        if (bannerSlot) {
            const obs = new MutationObserver(() => setTimeout(updateBlogPageSummary, 80));
            obs.observe(bannerSlot, { childList: true, subtree: true, attributes: true });
        }

        updateBlogPageSummary();
    });
</script>