// Modal Fix para iOS e dispositivos móveis

document.addEventListener('DOMContentLoaded', function() {
    
    // Detectar se é iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    // Detectar se é mobile
    const isMobile = window.innerWidth <= 768;
    
    // Aplicar fix para todas as modals
    function fixModalPosition(modal) {
        if (!modal) return;
        
        const modalDialog = modal.querySelector('.modal-dialog');
        if (!modalDialog) return;
        
        // Garantir centralização
        modalDialog.style.position = 'absolute';
        modalDialog.style.top = '50%';
        modalDialog.style.left = '50%';
        modalDialog.style.transform = 'translate(-50%, -50%)';
        modalDialog.style.margin = '0';
        
        if (isMobile) {
            modalDialog.style.width = '95%';
            modalDialog.style.maxWidth = 'none';
        }
        
        // Fix específico para iOS
        if (isIOS) {
            modal.style.webkitTransform = 'translateZ(0)';
            modal.style.transform = 'translateZ(0)';
            modalDialog.style.webkitTransform = 'translate(-50%, -50%) translateZ(0)';
            modalDialog.style.transform = 'translate(-50%, -50%) translateZ(0)';
        }
    }
    
    // Event listeners para modals
    function setupModalListeners() {
        // Quando modal é mostrada
        document.addEventListener('show.bs.modal', function(e) {
            const modal = e.target;
            
            // Adicionar classe para body
            document.body.classList.add('modal-open');
            
            // Prevenir scroll do body
            if (isIOS || isMobile) {
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.height = '100%';
            }
            
            // Fix posição após modal aparecer
            setTimeout(() => {
                fixModalPosition(modal);
            }, 100);
        });
        
        // Quando modal é escondida
        document.addEventListener('hide.bs.modal', function(e) {
            // Restaurar scroll do body
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.height = '';
            document.body.classList.remove('modal-open');
        });
        
        // Event listener para quando modal é totalmente mostrada
        document.addEventListener('shown.bs.modal', function(e) {
            const modal = e.target;
            fixModalPosition(modal);
            
            // Focar no primeiro input se existir
            const firstInput = modal.querySelector('input, select, textarea');
            if (firstInput && !isIOS) {
                firstInput.focus();
            }
        });
    }
    
    // Configurar modais existentes
    function fixExistingModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            // Garantir z-index alto
            modal.style.zIndex = '9999';
            
            // Fix inicial
            fixModalPosition(modal);
            
            // Adicionar event listeners específicos se não existirem
            if (!modal.hasAttribute('data-modal-fixed')) {
                modal.setAttribute('data-modal-fixed', 'true');
                
                modal.addEventListener('click', function(e) {
                    // Fechar modal se clicar no backdrop
                    if (e.target === modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                });
            }
        });
    }
    
    // Fix para viewport em iOS
    function fixIOSViewport() {
        if (isIOS) {
            // Prevenir zoom ao focar inputs
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 
                    'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'
                );
            }
            
            // Fix para height em iOS
            const fixHeight = () => {
                document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
            };
            
            window.addEventListener('resize', fixHeight);
            window.addEventListener('orientationchange', fixHeight);
            fixHeight();
        }
    }
    
    // Função para corrigir modal específica
    window.fixModalForMobile = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            fixModalPosition(modal);
            
            // Mostrar modal com bootstrap
            const bsModal = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: false
            });
            bsModal.show();
        }
    };
    
    // Função para centralizar modal atual
    window.centerCurrentModal = function() {
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            fixModalPosition(activeModal);
        }
    };
    
    // Observer para modals dinamicas
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('modal')) {
                        fixModalPosition(node);
                    }
                });
            }
        });
    });
    
    // Inicializar tudo
    fixIOSViewport();
    setupModalListeners();
    fixExistingModals();
    
    // Observar mudanças no DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Fix adicional para resize
    window.addEventListener('resize', function() {
        setTimeout(() => {
            fixExistingModals();
            centerCurrentModal();
        }, 100);
    });
    
    // Fix adicional para orientação
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            fixExistingModals();
            centerCurrentModal();
        }, 300);
    });
    
    console.log('Modal iOS Fix carregado!');
});

// CSS adicional via JavaScript se necessário
const additionalCSS = `
<style>
/* Fix adicional via JS */
.modal {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modal-dialog {
    flex: 0 0 auto !important;
}

/* Específico para webkit (Safari/iOS) */
@supports (-webkit-appearance: none) {
    .modal input,
    .modal select,
    .modal textarea {
        font-size: 16px !important; /* Prevenir zoom no iOS */
    }
}
</style>
`;

// Adicionar CSS se necessário
if (document.head && !document.getElementById('modal-ios-additional-css')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'modal-ios-additional-css';
    styleElement.innerHTML = additionalCSS;
    document.head.appendChild(styleElement);
} 