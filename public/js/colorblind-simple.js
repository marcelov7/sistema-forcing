// Sistema de acessibilidade para daltonismo - Botão no canto inferior direito
document.addEventListener('DOMContentLoaded', function() {
    // Remover toggle existente se houver
    const existing = document.querySelector('#colorblind-toggle-btn');
    if (existing) {
        existing.remove();
    }
    
    // Criar botão
    const toggleButton = document.createElement('button');
    toggleButton.id = 'colorblind-toggle-btn';
    toggleButton.innerHTML = '🎨 Daltonismo';
    
    // Estilos do botão - POSICIONADO NO CANTO INFERIOR DIREITO
    toggleButton.style.cssText = 
        'position: fixed;' +
        'bottom: 20px;' +
        'right: 20px;' +
        'background: #007bff;' +
        'color: white;' +
        'border: none;' +
        'border-radius: 25px;' +
        'padding: 12px 20px;' +
        'cursor: pointer;' +
        'font-size: 14px;' +
        'font-weight: 600;' +
        'font-family: system-ui, sans-serif;' +
        'box-shadow: 0 4px 12px rgba(0,123,255,0.3);' +
        'z-index: 10000;' +
        'transition: all 0.3s ease;';
    
    // Estado do filtro
    let isActive = false;
    
    // Filtros para diferentes tipos de daltonismo
    const filters = {
        protanopia: 'sepia(1) saturate(0.8) hue-rotate(-30deg) contrast(1.2)',
        deuteranopia: 'sepia(1) saturate(0.7) hue-rotate(60deg) contrast(1.1)',
        tritanopia: 'sepia(1) saturate(0.9) hue-rotate(180deg) contrast(1.3)',
        normal: ''
    };
    
    let currentFilter = 'normal';
    const filterKeys = Object.keys(filters);
    
    // Função para aplicar filtro
    function applyFilter(filterName) {
        document.documentElement.style.filter = filters[filterName];
        currentFilter = filterName;
        
        if (filterName === 'normal') {
            toggleButton.style.background = '#007bff';
            toggleButton.innerHTML = '🎨 Daltonismo';
            isActive = false;
        } else {
            toggleButton.style.background = '#28a745';
            toggleButton.innerHTML = '✅ Ativo';
            isActive = true;
        }
        
        // Salvar estado
        localStorage.setItem('colorblind-filter', filterName);
    }
    
    // Evento de clique
    toggleButton.addEventListener('click', function() {
        const currentIndex = filterKeys.indexOf(currentFilter);
        const nextIndex = (currentIndex + 1) % filterKeys.length;
        const nextFilter = filterKeys[nextIndex];
        
        applyFilter(nextFilter);
        
        // Mostrar qual filtro está ativo
        if (nextFilter !== 'normal') {
            console.log('Filtro ativo:', nextFilter);
        }
    });
    
    // Hover effects
    toggleButton.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 6px 16px rgba(0,123,255,0.4)';
    });
    
    toggleButton.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 4px 12px rgba(0,123,255,0.3)';
    });
    
    // Adicionar ao body
    document.body.appendChild(toggleButton);
    
    // Restaurar filtro salvo
    const savedFilter = localStorage.getItem('colorblind-filter');
    if (savedFilter && filters[savedFilter]) {
        applyFilter(savedFilter);
    }
    
    console.log('Sistema de daltonismo carregado - Botão no canto inferior direito');
});
