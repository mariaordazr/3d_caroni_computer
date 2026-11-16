<?php
// app/controller/PublicController.php

// REQUERIMOS LOS MODELOS NECESARIOS AL INICIO
require_once ROOT_PATH . 'app/model/Offer.php';
require_once ROOT_PATH . 'app/model/NewProduct.php';
require_once ROOT_PATH . 'app/model/Category.php';
require_once ROOT_PATH . 'app/model/Brand.php';
require_once ROOT_PATH . 'app/model/Product.php';
require_once ROOT_PATH . 'app/model/SiteContent.php';
require_once ROOT_PATH . 'app/model/Settings.php';

class PublicController {
    protected $db; // Asegúrate de tener la conexión a la BD disponible

    private $offerModel;
    private $newProductModel;
    private $categoryModel;
    private $brandModel;
    private $productModel;
    private $siteContentModel;
    private $settingsModel;

    public function __construct($connection) {
        $this->db = $connection;
        // Inicializa tus modelos aquí si lo prefieres
        $this->offerModel = new Offer($connection);
        $this->categoryModel = new Category($connection); 
        $this->newProductModel = new NewProduct($connection);
        $this->brandModel = new Brand($connection);
        $this->productModel = new Product($connection);
        $this->siteContentModel = new SiteContent($this->db);
        $this->settingsModel = new Settings($this->db);
    }

    public function index() {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');
        $showPrice = (int) ($this->settingsModel->getValueByKey('show_price') ?? 0);
        $showStock = (int) ($this->settingsModel->getValueByKey('show_stock') ?? 0);

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------


        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        // --- Fin de Cargar datos para el Header ---

        $sliderKey = 'home_slider';
        $bannerKey = 'home_banner';
        $featuredCategoriesKey = 'home_category';
        $featuredBrandsKey = 'home_brand';

        // 1. IMÁGENES DEL SLIDER
        $sliderContent = $this->siteContentModel->getOrderedContentByPrefix($sliderKey);
        $sliderImages = array_column($sliderContent, 'content_value');

        
        // 2. IMAGEN DEL BANNER
        $bannerImage = $this->siteContentModel->getValue($bannerKey);
        
       // 3. CATEGORÍAS DESTACADAS
        // Usamos el método que obtiene TODOS los IDs ordenados para esta clave
        $featuredCategoryIds = $this->siteContentModel->getOrderedIdsByKey($featuredCategoriesKey);

        // Usar IDs guardadas o un array por defecto si no se han configurado
        if (empty($featuredCategoryIds)) {
             $featuredCategoryIds = [1, 2, 3, 4, 5, 6, 7]; // IDs por defecto
        }
        $featuredCategories = $this->categoryModel->getFeaturedCategories($featuredCategoryIds);

        // 4. MARCAS DESTACADAS
        // Usamos el método que obtiene TODOS los IDs ordenados para esta clave
        $featuredBrandIds = $this->siteContentModel->getOrderedIdsByKey($featuredBrandsKey);
        
        // Usar IDs guardadas o un array por defecto si no se han configurado
        if (empty($featuredBrandIds)) {
             $featuredBrandIds = [1, 2, 3, 4, 5, 6, 7]; // IDs por defecto
        }
        $featuredBrands = $this->brandModel->getFeaturedBrands($featuredBrandIds);

        $allOffers = $this->offerModel->getAll();
        $offers = array_slice($allOffers, 0, 10); // Solo los primeros 10


        $allNewProducts = $this->newProductModel->getAll(); 
        $newProducts = array_slice($allNewProducts, 0, 10); // Solo los primeros 10

        // Incluir la vista de la página de inicio
        include ROOT_PATH . 'app/views/public/home.php';
    }


    public function products() {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');
        $showPrice = (int) ($this->settingsModel->getValueByKey('show_price') ?? 0);
        $showStock = (int) ($this->settingsModel->getValueByKey('show_stock') ?? 0);

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------
        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        
        $categoriesWithData = $this->productModel->getCategoriesWithBrands();
        $headerCategories = $this->categoryModel->getAllSimple();
        $categories = $this->categoryModel->getAll(); // Lo mantenemos para el resumen, si lo vuelves a activar

        // Recogemos los filtros
        $filters = [
            'search'       => $_GET['search'] ?? null,
            'category'     => null,
            'brand'        => null,
            'status'       => $_GET['status'] ?? null,
            'type'         => $_GET['type'] ?? null, // <- Lo lee directo de la URL
            'sort'         => $_GET['sort'] ?? 'name-asc',
        ];

        // --- LÓGICA DE FILTROS ---
        if (!empty($filters['search'])) {
            // Si hay búsqueda por texto, priorizamos eso y limpiamos categoría/marca
            $filters['category'] = null;
            $filters['brand'] = null;
        } else {
            // Si no hay búsqueda por texto, revisamos los filtros de la barra lateral o del home
            if (isset($_GET['filter_choice']) && !empty($_GET['filter_choice'])) {
                $choice = $_GET['filter_choice'];
                
                // Opción A: Se seleccionó CATEGORÍA y MARCA desde el sidebar (ej: "cat-1-brand-5")
                if (strpos($choice, 'cat-') === 0 && strpos($choice, '-brand-') !== false) {
                    $parts = explode('-brand-', $choice);
                    $filters['category'] = (int) str_replace('cat-', '', $parts[0]);
                    $filters['brand'] = (int) $parts[1];
                }
                // Opción B: Se seleccionó SOLO una MARCA desde el home (ej: "brand-5")
                else if (strpos($choice, 'brand-') === 0) {
                    $filters['brand'] = (int) str_replace('brand-', '', $choice);
                }
                // Opción C: Se seleccionó SOLO una CATEGORÍA (ej: "cat-1")
                else if (strpos($choice, 'cat-') === 0) {
                    $filters['category'] = (int) str_replace('cat-', '', $choice);
                }
            }
        }

        // Llamada al modelo
        $products = $this->productModel->getAllPublic($filters);

        // Pasamos la nueva variable a la vista
        include ROOT_PATH . 'app/views/public/products.php';
    }

    public function showProduct($id) {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');
        $showPrice = (int) ($this->settingsModel->getValueByKey('show_price') ?? 0);
        $showStock = (int) ($this->settingsModel->getValueByKey('show_stock') ?? 0);

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------

        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();

        $product = $this->productModel->getById((int)$id);


        // --- NUEVA LÓGICA DE VISIBILIDAD ---
        // Chequeamos si el producto, su categoría o su marca están ocultos
        if (!$product || $product['product_hidden'] == 1 || $product['category_hidden'] == 1 || $product['brand_hidden'] == 1) {
            http_response_code(403); // Forbidden
            
            $reason = "";
            if ($product['product_hidden'] == 1) {
                $reason = "El producto se encuentra oculto temporalmente.";
            } elseif ($product['category_hidden'] == 1) {
                $reason = "La categoría '{$product['category_name']}' se encuentra inactiva.";
            } elseif ($product['brand_hidden'] == 1) {
                $reason = "La marca '{$product['brand_name']}' se encuentra inactiva.";
            }

            // Puedes estilizar este mensaje o incluir una vista de error bonita
            echo "<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>";
            echo "<h2 style='color:#d32f2f;'>Contenido no disponible</h2>";
            // echo "<p>{$reason}</p>";
            echo "<a href='/productos' style='color:#0d6efd; text-decoration:none;'>Volver a la tienda</a>";
            echo "</div>";
            exit;
        }
        // ------------------------------------

        // Obtenemos TODAS las imágenes de ese producto
        $images = $this->productModel->getImagesByProductId((int)$id);

        // Cargamos la vista
        include ROOT_PATH . 'app/views/public/product-detail.php';
    }

    // ... otros métodos ...

    public function blog() {
        // --- Header y Footer data (Copia lo mismo que en otros métodos) ---
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');
        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        $activePage = 'blog'; 
        // ----------------------------------------------------------------

        // 1. BANNER DEL BLOG
        $bannerKey = 'blog_banner';
        $bannerImage = $this->siteContentModel->getValue($bannerKey);

        // --- 4. NUEVA LÓGICA: Obtener los artículos reales ---
        require_once ROOT_PATH . 'app/model/Blog.php';
        $blogModel = new Blog($this->db);
        
        // Le pedimos al modelo que traiga SOLO los artículos publicados 
        // y los ordene desde el más reciente al más antiguo
        $blogs = $blogModel->getAll([
            'status' => 'publicado',
            'sort'   => 'created_at-desc' 
        ]);
        

        include ROOT_PATH . 'app/views/public/blog.php';
    }

    public function showBlogPost($slug) {
        // --- 1. Cargar datos del Header y Footer (igual que el resto) ---
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');
        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        $activePage = 'blog'; 
        // ----------------------------------------------------------------

        // --- 2. Buscar el artículo por su slug ---
        require_once ROOT_PATH . 'app/model/Blog.php';
        $blogModel = new Blog($this->db);
        $post = $blogModel->getBySlug($slug);

        // Si el artículo no existe o está en borrador, lo regresamos a la lista principal
        if (!$post) {
            header('Location: /blog');
            exit;
        }

        // --- 3. Renderizar la nueva vista ---
        include ROOT_PATH . 'app/views/public/blog-detail.php';
    }

     // NUEVO MÉTODO PARA "NOSOTROS"
    public function aboutUs() {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------


        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        // --- Fin de Cargar datos para el Header ---

        $activePage = 'about-us'; // Para resaltar el menú activo


        $bannerKey = 'about_banner';
        $aboutTitleKey = 'about_title';
        $aboutTextKey = 'about_text';
        $aboutImageKey = 'about_image';

        $bannerImage = $this->siteContentModel->getValue($bannerKey);
        $titleContent = $this->siteContentModel->getOrderedContentByPrefix($aboutTitleKey);
        $textContent = $this->siteContentModel->getOrderedContentByPrefix($aboutTextKey);
        $imageContent = $this->siteContentModel->getOrderedContentByPrefix($aboutImageKey);


        $aboutTitles = array_column($titleContent, 'content_value');
        $aboutTexts = array_column($textContent, 'content_value');
        $aboutImages = array_column($imageContent, 'content_value');

        
        include ROOT_PATH . 'app/views/public/about-us.php';
    }

    // NUEVO MÉTODO PARA "SERVICIOS"
    public function services() {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------

        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        // --- Fin de Cargar datos para el Header ---

        $activePage = 'services'; // Para resaltar el menú activo


        $bannerKey = 'service_banner';
        $serviceTitleKey = 'service_title';
        $serviceTextKey = 'service_text';
        $serviceImageKey = 'service_image';
        $serviceLinkKey = 'service_link';
        $serviceClientsKey = 'service_client';

        $bannerImage = $this->siteContentModel->getValue($bannerKey);
        $serviceLink = $this->siteContentModel->getValue($serviceLinkKey);
        $titleContent = $this->siteContentModel->getOrderedContentByPrefix($serviceTitleKey);
        $textContent = $this->siteContentModel->getOrderedContentByPrefix($serviceTextKey);
        $imageContent = $this->siteContentModel->getOrderedContentByPrefix($serviceImageKey);
        $clientsContent = $this->siteContentModel->getOrderedContentByPrefix($serviceClientsKey);

        $serviceTitles = array_column($titleContent, 'content_value');
        $serviceTexts = array_column($textContent, 'content_value');
        $serviceImages = array_column($imageContent, 'content_value');
        $serviceClients = array_column($clientsContent, 'content_value');

        $serviceClientsCount = count($serviceClients);

        include ROOT_PATH . 'app/views/public/services.php';
    }

    public function shipping() {
        //------------header----------
        $pageName = $this->siteContentModel->getValue('page_title');
        $locationHeader = $this->siteContentModel->getValue('location_header');
        $logoImage = $this->siteContentModel->getValue('logo_header');
        $locationLink = $this->siteContentModel->getValue('location_link');
        $moneySymbol = $this->siteContentModel->getValue('money_symbol');

        //-------footer-------------------
        $locationAddress = $this->siteContentModel->getValue('location_address');
        $contactPhone = $this->siteContentModel->getValue('contact_number');
        $paymentContent = $this->siteContentModel->getOrderedContentByPrefix('payment_logo');
        $paymentMethods = array_column($paymentContent,'content_value');

        $facebook = $this->siteContentModel->getValue('facebook_link');
        $instagram = $this->siteContentModel->getValue('instagram_link');
        $youtube = $this->siteContentModel->getValue('youtube_link');
        $tiktok = $this->siteContentModel->getValue('tiktok_link');
        $copyrightText = $this->siteContentModel->getValue('copyright');
        //----------------------------------------

        $categoryModel = new Category($this->db);
        $brandModel = new Brand($this->db);
        $headerCategories = $this->categoryModel->getAllSimple();
        // --- Fin de Cargar datos para el Header ---

        $activePage = 'shipping'; // Para resaltar el menú activo

        $bannerKey = 'shipping_banner';
        $shippingTitleKey = 'shipping_title';
        $shippingBrandKey = 'shipping_brand';
        $shippingTextKey = 'shipping_text';
        $shippingImageKey = 'shipping_image';
        $shippingLinkKey = 'shipping_link';

        $bannerImage = $this->siteContentModel->getValue($bannerKey);
        $shippingLink = $this->siteContentModel->getValue($shippingLinkKey);
        $shippingImage = $this->siteContentModel->getValue($shippingImageKey);
        $titleContent = $this->siteContentModel->getOrderedContentByPrefix($shippingTitleKey);
        $textContent = $this->siteContentModel->getOrderedContentByPrefix($shippingTextKey);
        $brandContent = $this->siteContentModel->getOrderedContentByPrefix($shippingBrandKey);


        $shippingTitles = array_column($titleContent, 'content_value');
        $shippingTexts = array_column($textContent, 'content_value');
        $shippingBrands = array_column($brandContent, 'content_value');
        
        $shippingBrandsCount = count($shippingBrands);

        include ROOT_PATH . 'app/views/public/shipping.php';
    }

    public function viewPublicReceipt($token) {
        require_once ROOT_PATH . 'app/model/Receipt.php';
        $receiptModel = new Receipt($this->db);
        
        $receipt = $receiptModel->getByToken($token);

        if ($receipt) {
            // 1. Si el cliente presiona "Sí, acepto", "No" o "Deshacer respuesta" (POST o GET AJAX)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['save_choice'])) {
                header('Content-Type: application/json');
                $jsonInput = json_decode(file_get_contents('php://input'), true) ?: $_POST;
                
                if (isset($jsonInput['action']) && $jsonInput['action'] === 'save_client_choice') {
                    $rowIndex = (int)($jsonInput['row_index'] ?? -1);
                    $choice   = trim($jsonInput['choice'] ?? ''); // 'accepted', 'rejected', o '' (sin respuesta)

                    $budgetData = json_decode($receipt['budget_json'] ?? '{}', true) ?: [];
                    // Soportar estructura con 'items' en budget_json
                    if (isset($budgetData['items'][$rowIndex])) {
                        $budgetData['items'][$rowIndex]['client_choice'] = $choice;
                        $newBudgetJson = json_encode($budgetData, JSON_UNESCAPED_UNICODE);

                        $stmt = $this->db->prepare("UPDATE receipts SET budget_json = ? WHERE id_receipt = ?");
                        $stmt->bind_param("si", $newBudgetJson, $receipt['id_receipt']);
                        $ok = $stmt->execute();

                        echo json_encode(['success' => $ok, 'choice' => $choice]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'message' => 'Fila no encontrada']);
                exit;
            }

            // 2. Cargamos el símbolo de la moneda para la vista pública
            $moneySymbol = $this->siteContentModel->getValue('money_symbol') ?: '$';

            $settings = $receiptModel->getSettings();
            $items = json_decode($receipt['items_json'], true);
            $budget = json_decode($receipt['budget_json'], true); 
            $data = $receipt;
            
            $isPublic = true; 
            include ROOT_PATH . 'app/views/admin/receipt-pdf.php';
        } else {
            echo "<h2 style='text-align:center; padding: 50px; font-family:sans-serif; color: #d32f2f;'>Enlace inválido o caducado.</h2>";
            echo "<p style='text-align:center;'>Por su seguridad, solicite un nuevo enlace de acceso a Caroni Computer.</p>";
        }
    }   
 

}
?>