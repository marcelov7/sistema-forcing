/**
 * 🎯 Sistema de Toggle de Visualização - Forcing
 * Permite alternar entre visualização em lista e cards
 */

class ForcingViewToggle {
    constructor() {
        this.currentView = this.getStoredView() || 'list';
        this.init();
    }

    init() {
        // Elementos DOM
        this.viewListBtn = document.getElementById('viewListBtn');
        this.viewCardsBtn = document.getElementById('viewCardsBtn');
        this.tableContainer = document.getElementById('table-container');
        this.cardsContainer = document.getElementById('cards-container');
        
        // Event listeners
        this.setupEventListeners();
        
        // Aplicar visualização inicial
        this.applyView(this.currentView);
    }

    setupEventListeners() {
        // Botão de lista
        if (this.viewListBtn) {
            this.viewListBtn.addEventListener('click', () => {
                this.switchView('list');
            });
        }

        // Botão de cards
        if (this.viewCardsBtn) {
            this.viewCardsBtn.addEventListener('click', () => {
                this.switchView('cards');
            });
        }

        // Salvar preferência ao sair da página
        window.addEventListener('beforeunload', () => {
            this.saveView();
        });

        // Salvar preferência quando mudar de visualização
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.saveView();
            }
        });
    }

    switchView(viewType) {
        if (viewType === this.currentView) return;
        
        this.currentView = viewType;
        this.applyView(viewType);
        this.saveView();
        
        // Animação suave
        this.animateTransition();
    }

    applyView(viewType) {
        // Atualizar botões
        this.updateButtons(viewType);
        
        // Mostrar/ocultar containers
        if (viewType === 'list') {
            this.showListView();
        } else {
            this.showCardsView();
        }
    }

    updateButtons(viewType) {
        // Remover classe active de todos os botões
        if (this.viewListBtn) this.viewListBtn.classList.remove('active');
        if (this.viewCardsBtn) this.viewCardsBtn.classList.remove('active');
        
        // Adicionar classe active ao botão correto
        if (viewType === 'list' && this.viewListBtn) {
            this.viewListBtn.classList.add('active');
        } else if (viewType === 'cards' && this.viewCardsBtn) {
            this.viewCardsBtn.classList.add('active');
        }
    }

    showListView() {
        if (this.tableContainer && this.cardsContainer) {
            this.tableContainer.style.display = 'block';
            this.cardsContainer.style.display = 'none';
            
            // Adicionar classe para animação
            this.tableContainer.classList.add('view-transition', 'show');
            setTimeout(() => {
                this.tableContainer.classList.remove('view-transition');
            }, 300);
        }
    }

    showCardsView() {
        if (this.tableContainer && this.cardsContainer) {
            this.tableContainer.style.display = 'none';
            this.cardsContainer.style.display = 'block';
            
            // Adicionar classe para animação
            this.cardsContainer.classList.add('view-transition', 'show');
            setTimeout(() => {
                this.cardsContainer.classList.remove('view-transition');
            }, 300);
        }
    }

    animateTransition() {
        // Adicionar efeito de transição
        const activeContainer = this.currentView === 'list' ? 
            this.tableContainer : this.cardsContainer;
        
        if (activeContainer) {
            activeContainer.style.opacity = '0';
            activeContainer.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                activeContainer.style.transition = 'all 0.3s ease-in-out';
                activeContainer.style.opacity = '1';
                activeContainer.style.transform = 'translateY(0)';
            }, 50);
        }
    }

    getStoredView() {
        try {
            return localStorage.getItem('forcing-view-preference');
        } catch (error) {
            console.warn('Não foi possível acessar localStorage:', error);
            return 'list';
        }
    }

    saveView() {
        try {
            localStorage.setItem('forcing-view-preference', this.currentView);
        } catch (error) {
            console.warn('Não foi possível salvar preferência:', error);
        }
    }

    // Método público para obter visualização atual
    getCurrentView() {
        return this.currentView;
    }

    // Método público para definir visualização
    setView(viewType) {
        this.switchView(viewType);
    }
}

/**
 * 🎯 Utilitários para Cards
 */
class ForcingCardsUtils {
    static highlightCard(forcingId) {
        const card = document.querySelector(`[data-forcing-id="${forcingId}"]`);
        if (card) {
            card.classList.add('border-warning');
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remover destaque após 5 segundos
            setTimeout(() => {
                card.classList.remove('border-warning');
            }, 5000);
        }
    }

    static filterCards(searchTerm) {
        const cards = document.querySelectorAll('.forcing-card');
        const term = searchTerm.toLowerCase();
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            const isVisible = text.includes(term);
            
            card.closest('.col-12').style.display = isVisible ? 'block' : 'none';
        });
    }

    static sortCards(sortBy) {
        const container = document.getElementById('cards-container');
        const cards = Array.from(container.querySelectorAll('.col-12'));
        
        cards.sort((a, b) => {
            const cardA = a.querySelector('.forcing-card');
            const cardB = b.querySelector('.forcing-card');
            
            switch (sortBy) {
                case 'id':
                    const idA = parseInt(cardA.dataset.forcingId);
                    const idB = parseInt(cardB.dataset.forcingId);
                    return idB - idA; // Mais recente primeiro
                    
                case 'status':
                    const statusA = cardA.querySelector('.badge').textContent.trim();
                    const statusB = cardB.querySelector('.badge').textContent.trim();
                    return statusA.localeCompare(statusB);
                    
                case 'area':
                    const areaA = cardA.querySelector('[data-area]')?.dataset.area || '';
                    const areaB = cardB.querySelector('[data-area]')?.dataset.area || '';
                    return areaA.localeCompare(areaB);
                    
                default:
                    return 0;
            }
        });
        
        // Reordenar no DOM
        cards.forEach(card => container.appendChild(card));
    }
}

/**
 * 🎯 Inicialização
 */
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar toggle de visualização
    window.forcingViewToggle = new ForcingViewToggle();
    window.forcingCardsUtils = ForcingCardsUtils;
    
    // Destacar card se houver parâmetro highlight
    const urlParams = new URLSearchParams(window.location.search);
    const highlightId = urlParams.get('highlight');
    if (highlightId && window.forcingViewToggle.getCurrentView() === 'cards') {
        setTimeout(() => {
            ForcingCardsUtils.highlightCard(highlightId);
        }, 500);
    }
    
    // Interceptar mudanças de filtro para manter visualização
    const filterForm = document.getElementById('filtroForm');
    if (filterForm) {
        filterForm.addEventListener('submit', () => {
            // Manter visualização atual após filtro
            const currentView = window.forcingViewToggle.getCurrentView();
            setTimeout(() => {
                window.forcingViewToggle.setView(currentView);
            }, 100);
        });
    }
});

/**
 * 🎯 Hotkeys para alternância rápida
 */
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + 1 = Lista
    if ((e.ctrlKey || e.metaKey) && e.key === '1') {
        e.preventDefault();
        if (window.forcingViewToggle) {
            window.forcingViewToggle.setView('list');
        }
    }
    
    // Ctrl/Cmd + 2 = Cards
    if ((e.ctrlKey || e.metaKey) && e.key === '2') {
        e.preventDefault();
        if (window.forcingViewToggle) {
            window.forcingViewToggle.setView('cards');
        }
    }
});

/**
 * 🎯 Responsividade automática
 */
window.addEventListener('resize', () => {
    if (window.forcingViewToggle) {
        const currentView = window.forcingViewToggle.getCurrentView();
        
        // Em telas muito pequenas, forçar lista se estiver em cards
        if (window.innerWidth < 576 && currentView === 'cards') {
            // Opcional: auto-switch para lista em mobile
            // window.forcingViewToggle.setView('list');
        }
    }
});

