/**
 * 🧪 Teste de Dropdowns - Sistema de Forcing
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🧪 Iniciando teste de dropdowns...');
    
    // Aguardar um pouco para garantir que tudo carregou
    setTimeout(function() {
        testDropdowns();
    }, 500);
});

function testDropdowns() {
    console.log('🔍 Testando dropdowns...');
    
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    console.log(`📊 Encontrados ${dropdowns.length} dropdowns`);
    
    dropdowns.forEach(function(dropdown, index) {
        console.log(`Dropdown ${index + 1}:`, {
            element: dropdown,
            hasDataBsToggle: dropdown.hasAttribute('data-bs-toggle'),
            type: dropdown.type,
            inForm: dropdown.closest('form') !== null,
            hasBootstrapInstance: dropdown._dropdown !== undefined
        });
        
        // Testar clique
        dropdown.addEventListener('click', function(e) {
            console.log(`🖱️ Clique no dropdown ${index + 1}`, e);
        });
    });
    
    // Verificar se Bootstrap está disponível
    if (typeof bootstrap !== 'undefined') {
        console.log('✅ Bootstrap disponível:', bootstrap);
        console.log('✅ Bootstrap.Dropdown disponível:', bootstrap.Dropdown);
    } else {
        console.log('❌ Bootstrap não disponível');
    }
    
    // Verificar se há dropdowns com menu
    const menus = document.querySelectorAll('.dropdown-menu');
    console.log(`📋 Encontrados ${menus.length} menus de dropdown`);
    
    menus.forEach(function(menu, index) {
        console.log(`Menu ${index + 1}:`, {
            element: menu,
            hasItems: menu.querySelectorAll('.dropdown-item').length,
            items: Array.from(menu.querySelectorAll('.dropdown-item')).map(item => item.textContent.trim())
        });
    });
}

