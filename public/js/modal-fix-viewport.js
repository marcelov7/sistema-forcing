// MODAL FIX VIEWPORT - Força modal a ocupar tela inteira
document.addEventListener('DOMContentLoaded', function() {
    
    console.log('🎯 MODAL FIX VIEWPORT - Centralizando modals');
    
    // Função para mover modal para o body e forçar viewport completo
    function forceModalToViewport(modal) {
        if (!modal) return;
        
        console.log('📍 Movendo modal para viewport:', modal.id);
        
        // Mover modal para o body se não estiver lá
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
            console.log('🔄 Modal movida para body');
        }
        
        // Forçar posicionamento de viewport completo
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100vw';
        modal.style.height = '100vh';
        modal.style.zIndex = '100000';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.margin = '0';
        modal.style.padding = '20px';
        modal.style.boxSizing = 'border-box';
        
        // Dialog centralizado
        const modalDialog = modal.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.position = 'relative';
            modalDialog.style.margin = '0';
            modalDialog.style.width = 'auto';
            modalDialog.style.maxWidth = '90vw';
            modalDialog.style.maxHeight = '90vh';
            modalDialog.style.transform = 'none';
        }
        
        // Content com scroll se necessário
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.maxHeight = '85vh';
            modalContent.style.overflow = 'auto';
            modalContent.style.borderRadius = '10px';
            modalContent.style.boxShadow = '0 10px 30px rgba(0,0,0,0.5)';
        }
        
        // Fix backdrop
        setTimeout(() => {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.position = 'fixed';
                backdrop.style.top = '0';
                backdrop.style.left = '0';
                backdrop.style.width = '100vw';
                backdrop.style.height = '100vh';
                backdrop.style.zIndex = '99999';
                backdrop.style.backgroundColor = 'rgba(0,0,0,0.6)';
            }
        }, 50);
        
        // Fix botões cancelar
        const cancelButtons = modal.querySelectorAll('[data-bs-dismiss="modal"]');
        cancelButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                console.log('❌ Cancelar clicado');
                e.preventDefault();
                e.stopPropagation();
                
                const instance = bootstrap.Modal.getInstance(modal);
                if (instance) {
                    instance.hide();
                } else {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    
                    // Remover backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            });
        });
        
        console.log('✅ Modal configurada para viewport completo');
    }
    
    // Interceptar abertura de modal ANTES de mostrar
    document.addEventListener('show.bs.modal', function(e) {
        console.log('🔵 Modal abrindo:', e.target.id);
        forceModalToViewport(e.target);
    });
    
    // Re-aplicar após modal estar totalmente aberta
    document.addEventListener('shown.bs.modal', function(e) {
        console.log('🟢 Modal aberta:', e.target.id);
        
        // Re-aplicar viewport fix
        setTimeout(() => {
            forceModalToViewport(e.target);
        }, 100);
        
        // Verificar se está ocupando viewport completo
        const modal = e.target;
        const rect = modal.getBoundingClientRect();
        if (rect.width >= window.innerWidth * 0.95 && rect.height >= window.innerHeight * 0.95) {
            console.log('✅ Modal ocupando viewport completo');
        } else {
            console.log('⚠️ Modal não está em viewport completo, re-aplicando...');
            forceModalToViewport(modal);
        }
    });
    
    // Limpeza após fechar
    document.addEventListener('hidden.bs.modal', function(e) {
        console.log('🔴 Modal fechada:', e.target.id);
        
        // Limpar backdrops órfãos
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            const openModals = document.querySelectorAll('.modal.show');
            
            if (backdrops.length > 0 && openModals.length === 0) {
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                console.log('🧹 Limpeza realizada');
            }
        }, 100);
    });
    
    // Fix para mudança de orientação
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                forceModalToViewport(openModal);
            }
        }, 300);
    });
    
    console.log('🎯 Modal Fix Viewport inicializado');
}); 