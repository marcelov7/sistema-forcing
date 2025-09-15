/**
 * 🎯 Correção de Dropdowns - Sistema de Forcing
 * Evita refresh da página e corrige comportamento dos dropdowns
 */

document.addEventListener('DOMContentLoaded', function() {
    // Aguardar Bootstrap carregar
    setTimeout(function() {
        initializeDropdowns();
    }, 100);
    
    // Re-aplicar quando novos elementos forem adicionados
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                setTimeout(function() {
                    initializeDropdowns();
                }, 50);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

function initializeDropdowns() {
    // Garantir que todos os dropdowns têm o tipo correto
    document.querySelectorAll('.dropdown-toggle').forEach(function(button) {
        // Garantir que não está dentro de um formulário
        const form = button.closest('form');
        if (form) {
            button.setAttribute('type', 'button');
        }
        
        // Garantir que tem os atributos corretos
        if (!button.hasAttribute('data-bs-toggle')) {
            button.setAttribute('data-bs-toggle', 'dropdown');
        }
        if (!button.hasAttribute('aria-expanded')) {
            button.setAttribute('aria-expanded', 'false');
        }
        
        // Adicionar event listener para stopPropagation
        button.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Adicionar stopPropagation aos links do dropdown
    document.querySelectorAll('.dropdown-item').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    
    // Inicializar dropdowns do Bootstrap se disponível
    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
        document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
            // Inicializar apenas se não foi inicializado
            if (!element._dropdown) {
                try {
                    new bootstrap.Dropdown(element);
                } catch (e) {
                    console.warn('Erro ao inicializar dropdown:', e);
                }
            }
        });
    }
}

// Debug helper
window.debugDropdowns = function() {
    console.log('Dropdowns encontrados:', document.querySelectorAll('.dropdown-toggle').length);
    document.querySelectorAll('.dropdown-toggle').forEach(function(btn, index) {
        console.log(`Dropdown ${index + 1}:`, {
            element: btn,
            hasBootstrap: btn._dropdown !== undefined,
            type: btn.type,
            inForm: btn.closest('form') !== null
        });
    });
};
