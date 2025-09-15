/**
 * Sistema de Acessibilidade para Daltonismo
 * Forcing Control System - Laravel
 */

class ColorblindAccessibility {
    constructor() {
        this.isEnabled = false;
        this.storageKey = 'colorblind_mode_enabled';
        this.toggleSelector = '#colorblind-toggle';
        this.modeClass = 'colorblind-mode';
        
        this.init();
    }

    init() {
        console.log('🎨 Inicializando ColorblindAccessibility...');
        
        // Verificar se há preferência salva
        this.loadUserPreference();
        console.log('💾 Preferência carregada:', this.isEnabled);
        
        // Criar toggle se não existir
        this.createToggle();
        console.log('🔘 Toggle criado');
        
        // Configurar eventos
        this.setupEventListeners();
        console.log('👂 Event listeners configurados');
        
        // Aplicar modo se habilitado
        if (this.isEnabled) {
            this.enableColorblindMode();
        }
        
        console.log('✅ ColorblindAccessibility inicializado com sucesso');
    }

    loadUserPreference() {
        const saved = localStorage.getItem(this.storageKey);
        this.isEnabled = saved === 'true';
    }

    saveUserPreference() {
        localStorage.setItem(this.storageKey, this.isEnabled.toString());
    }

    createToggle() {
        console.log('🔍 Verificando se toggle já existe...');
        
        // Verificar se já existe
        if (document.querySelector(this.toggleSelector)) {
            console.log('⚠️ Toggle já existe, pulando criação');
            return;
        }

        console.log('🏗️ Criando toggle HTML...');
        const toggleHtml = `
            <div class="colorblind-toggle" id="colorblind-toggle-container">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="colorblind-toggle" ${this.isEnabled ? 'checked' : ''}>
                    <label class="form-check-label" for="colorblind-toggle">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                        Modo Daltonismo
                    </label>
                </div>
                <small class="text-muted d-block mt-1">
                    Melhora contraste e adiciona padrões visuais
                </small>
            </div>
        `;

        console.log('📝 Inserindo toggle no DOM...');
        document.body.insertAdjacentHTML('beforeend', toggleHtml);
        
        // Verificar se foi inserido com sucesso
        const insertedToggle = document.querySelector(this.toggleSelector);
        if (insertedToggle) {
            console.log('✅ Toggle inserido com sucesso');
        } else {
            console.error('❌ Falha ao inserir toggle no DOM');
        }
    }

    setupEventListeners() {
        // Event listener para o toggle
        document.addEventListener('change', (e) => {
            if (e.target && e.target.matches(this.toggleSelector)) {
                this.toggle();
            }
        });

        // Detectar preferência de sistema para alto contraste
        if (window.matchMedia) {
            const highContrastQuery = window.matchMedia('(prefers-contrast: high)');
            highContrastQuery.addListener(() => {
                if (highContrastQuery.matches && !this.isEnabled) {
                    this.suggestColorblindMode();
                }
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl + Shift + A para toggle
            if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                this.toggle();
                this.announceToggle();
            }
        });
    }

    toggle() {
        this.isEnabled = !this.isEnabled;
        this.saveUserPreference();
        
        if (this.isEnabled) {
            this.enableColorblindMode();
        } else {
            this.disableColorblindMode();
        }

        // Atualizar checkbox
        const checkbox = document.querySelector(this.toggleSelector);
        if (checkbox) {
            checkbox.checked = this.isEnabled;
        }
    }

    enableColorblindMode() {
        document.body.classList.add(this.modeClass);
        this.enhanceElements();
        this.addAriaLabels();
        console.log('Modo daltonismo ativado');
    }

    disableColorblindMode() {
        document.body.classList.remove(this.modeClass);
        this.removeEnhancements();
        console.log('Modo daltonismo desativado');
    }

    enhanceElements() {
        // Adicionar indicadores visuais aos status de forcing
        this.enhanceStatusCards();
        this.enhanceBadges();
        this.enhanceButtons();
        this.enhanceAlerts();
    }

    enhanceStatusCards() {
        const cards = document.querySelectorAll('.forcing-status-card, .card');
        cards.forEach(card => {
            const statusElement = card.querySelector('.badge, .status');
            if (statusElement) {
                const status = this.determineStatus(statusElement);
                card.setAttribute('data-status', status);
                card.setAttribute('aria-label', `Forcing ${status}`);
            }
        });
    }

    enhanceBadges() {
        const badges = document.querySelectorAll('.badge');
        badges.forEach(badge => {
            const text = badge.textContent.trim().toLowerCase();
            let icon = '';
            let ariaLabel = '';

            switch (text) {
                case 'forçado':
                case 'forcado':
                case 'ativo':
                    icon = '🔴';
                    ariaLabel = 'Status: Forçado (Ativo)';
                    break;
                case 'retirado':
                case 'inativo':
                    icon = '🟢';
                    ariaLabel = 'Status: Retirado (Inativo)';
                    break;
                case 'pendente':
                    icon = '🟡';
                    ariaLabel = 'Status: Pendente';
                    break;
                default:
                    icon = 'ℹ️';
                    ariaLabel = `Status: ${text}`;
            }

            badge.setAttribute('data-icon', icon);
            badge.setAttribute('aria-label', ariaLabel);
        });
    }

    enhanceButtons() {
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            const text = button.textContent.trim().toLowerCase();
            let ariaLabel = button.getAttribute('aria-label') || text;

            // Melhorar aria-labels para ações específicas
            if (text.includes('forçar')) {
                ariaLabel = 'Ação: Forçar sistema';
            } else if (text.includes('retirar')) {
                ariaLabel = 'Ação: Retirar forcing';
            } else if (text.includes('editar')) {
                ariaLabel = 'Ação: Editar registro';
            } else if (text.includes('excluir') || text.includes('deletar')) {
                ariaLabel = 'Ação: Excluir registro';
            }

            button.setAttribute('aria-label', ariaLabel);
        });
    }

    enhanceAlerts() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            let type = 'info';
            let icon = 'ℹ️';

            if (alert.classList.contains('alert-success')) {
                type = 'sucesso';
                icon = '✅';
            } else if (alert.classList.contains('alert-warning')) {
                type = 'aviso';
                icon = '⚠️';
            } else if (alert.classList.contains('alert-danger')) {
                type = 'erro';
                icon = '❌';
            }

            alert.setAttribute('data-alert-type', type);
            alert.setAttribute('aria-label', `${type}: ${alert.textContent.trim()}`);
        });
    }

    addAriaLabels() {
        // Melhorar acessibilidade geral
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            if (!table.getAttribute('aria-label')) {
                table.setAttribute('aria-label', 'Tabela de dados do sistema');
            }
        });

        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (!form.getAttribute('aria-label')) {
                form.setAttribute('aria-label', 'Formulário do sistema');
            }
        });
    }

    removeEnhancements() {
        // Remover atributos adicionados
        const enhancedElements = document.querySelectorAll('[data-status], [data-icon], [data-alert-type]');
        enhancedElements.forEach(element => {
            element.removeAttribute('data-status');
            element.removeAttribute('data-icon');
            element.removeAttribute('data-alert-type');
        });
    }

    determineStatus(element) {
        const text = element.textContent.trim().toLowerCase();
        const classes = element.className.toLowerCase();

        if (text.includes('forçado') || text.includes('forcado') || text.includes('ativo') || classes.includes('danger')) {
            return 'forcado';
        } else if (text.includes('retirado') || text.includes('inativo') || classes.includes('success')) {
            return 'retirado';
        } else if (text.includes('pendente') || classes.includes('warning')) {
            return 'pendente';
        }

        return 'indefinido';
    }

    suggestColorblindMode() {
        if (this.isEnabled) return;

        const suggestion = document.createElement('div');
        suggestion.innerHTML = `
            <div class="alert alert-info alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; left: 20px; z-index: 1050; max-width: 300px;">
                <strong>Sugestão de Acessibilidade:</strong><br>
                Detectamos que você usa modo de alto contraste. Deseja ativar o modo para daltonismo?
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-primary me-2" onclick="window.colorblindAccessibility.enableFromSuggestion()">
                        Ativar
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="alert">
                        Não, obrigado
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(suggestion);

        // Auto-dismiss após 10 segundos
        setTimeout(() => {
            if (suggestion.parentNode) {
                suggestion.remove();
            }
        }, 10000);
    }

    enableFromSuggestion() {
        this.isEnabled = true;
        this.saveUserPreference();
        this.enableColorblindMode();
        
        // Atualizar checkbox
        const checkbox = document.querySelector(this.toggleSelector);
        if (checkbox) {
            checkbox.checked = true;
        }

        // Remover sugestão
        const suggestion = document.querySelector('.alert');
        if (suggestion) {
            suggestion.remove();
        }
    }

    announceToggle() {
        const message = this.isEnabled ? 'Modo daltonismo ativado' : 'Modo daltonismo desativado';
        
        // Criar anúncio para screen readers
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            announcement.remove();
        }, 1000);
    }

    // Método público para outras partes do sistema
    isColorblindModeEnabled() {
        return this.isEnabled;
    }

    // Método para forçar atualização
    refresh() {
        if (this.isEnabled) {
            this.enhanceElements();
        }
    }
}

// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.colorblindAccessibility = new ColorblindAccessibility();
});

// Para compatibilidade com sistemas que carregam após DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.colorblindAccessibility) {
            window.colorblindAccessibility = new ColorblindAccessibility();
        }
    });
} else {
    // DOM já carregado
    if (!window.colorblindAccessibility) {
        window.colorblindAccessibility = new ColorblindAccessibility();
    }
}

