document.addEventListener('DOMContentLoaded', function() {
    const editorContainer = document.querySelector('#editor-container');
    
    if (editorContainer) {
        const Video = Quill.import('formats/video');
        const originalSanitize = Video.sanitize;

        Video.sanitize = function(url) {
            const raw = originalSanitize ? originalSanitize.call(this, url) : url;
            const cleanUrl = String(raw || '').trim();
            const ytMatch = cleanUrl.match(
                /(?:youtube\.com\/(?:shorts\/|watch\?v=|embed\/|live\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/
            );
            if (ytMatch && ytMatch[1]) {
                return `https://www.youtube.com/embed/${ytMatch[1]}`;
            }
            return cleanUrl;
        };

        const originalCreate = Video.create;
        Video.create = function(value) {
            const node = originalCreate.call(this, value);
            node.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
            node.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
            return node;
        };

        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Empieza a escribir tu genialidad aquí...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }, { 'size': ['small', false, 'large', 'huge'] }],
                    ['bold', 'italic', 'underline', 'strike'], 
                    [{ 'color': [] }, { 'background': [] }],   
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }], 
                    [{ 'align': [] }], 
                    ['link', 'image', 'video'], 
                    ['clean'] 
                ]
            }
        });

        const contentInput = document.getElementById('blog_content');
        const syncContent = () => {
            if (contentInput) contentInput.value = quill.root.innerHTML;
        };

        // Sincronizar al cargar (útil en Editar) y en cada cambio
        syncContent();
        quill.on('text-change', syncContent);

        const blogForm = document.getElementById('blog-form');
        if (blogForm) {
            blogForm.addEventListener('submit', function(e) {
                syncContent();
                const hasText  = quill.getText().trim().length > 0;
                const hasMedia = !!quill.root.querySelector('img, iframe');

                if (!hasText && !hasMedia) {
                    e.preventDefault();
                    alert('El contenido del artículo no puede estar vacío.');
                }
            });
        }
    }
});

// 3. PREVISUALIZAR IMAGEN DE PORTADA (Global)
window.previewBlogImage = function(input) {
    const preview = document.getElementById('preview-cover');
    // Buscamos el contenedor principal por su ID
    const container = document.getElementById('image-upload-container'); 
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // 1. Ponemos la imagen en el <img>
            if (preview) {
                preview.src = e.target.result;
            }
            // 2. Añadimos la clase al contenedor para que el CSS haga su magia
            if (container) {
                container.classList.add('has-preview');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
};


// 4. ELIMINAR ARTÍCULO VÍA AJAX (Global)
window.deleteBlog = function(id) {
    if(confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.')) {
        fetch('/admin/blogs/delete/' + id)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload(); // Recarga la página para mostrar los cambios
            } else {
                alert('Error al eliminar: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
};


// ==========================================
// MENÚ INTELIGENTE: DETECCIÓN DE BORDES
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    
    function adjustDropdownPosition(btn) {
        const container = btn.closest('.actions-menu-container');
        if (!container) return;
        
        const dropdown = container.querySelector('.actions-dropdown');
        if (!dropdown) return;

        // 1. Reseteamos la clase para poder medir su altura real hacia abajo
        dropdown.classList.remove('drop-up');
        
        // 2. Le damos un pequeñísimo respiro al navegador para que renderice el menú
        setTimeout(() => {
            const rect = dropdown.getBoundingClientRect();
            
            // 3. Si la parte inferior del menú (+ un margen de 20px) supera el alto de la ventana, lo invertimos
            if ((rect.bottom + 20) > window.innerHeight) {
                dropdown.classList.add('drop-up');
            }
        }, 10);
    }

    // Escuchar cuando el usuario hace CLIC en el botón
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.actions-menu-toggle');
        if (btn) {
            adjustDropdownPosition(btn);
        }
    });

    // Escuchar cuando el usuario pasa el MOUSE (si tienes configurado el menú en hover)
    document.addEventListener('mouseenter', function(e) {
        const container = e.target.closest('.actions-menu-container');
        if (container) {
            const btn = container.querySelector('.actions-menu-toggle');
            if (btn) adjustDropdownPosition(btn);
        }
    }, true); 
});