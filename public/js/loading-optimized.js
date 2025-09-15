/**
 * 🎯 Sistema de Loading Otimizado - Sem Delays Desnecessários
 * Versão mais rápida que remove simulações e usa redirecionamento real
 */

class OptimizedForcingLoader {
    constructor() {
        this.init();
    }

    init() {
        this.interceptForms();
    }

    /**
     * Interceptar formulários de ação
     */
    interceptForms() {
        // Formulários de liberar
        document.querySelectorAll('form[action*="/liberar"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showQuickLoading('liberar', 'Liberando forcing...');
            });
        });

        // Formulários de executar
        document.querySelectorAll('form[action*="/registrar-execucao"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showQuickLoading('executar', 'Registrando execução...');
            });
        });

        // Formulários de solicitar retirada
        document.querySelectorAll('form[action*="/solicitar-retirada"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showQuickLoading('retirada', 'Solicitando retirada...');
            });
        });

        // Formulários de retirar
        document.querySelectorAll('form[action*="/retirar"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showQuickLoading('retirar', 'Retirando forcing...');
            });
        });
    }

    /**
     * Mostrar loading rápido sem delays desnecessários
     */
    showQuickLoading(action, message) {
        // Criar overlay de loading
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay show';
        overlay.innerHTML = `
            <div class="loading-overlay-content">
                <div class="loading-overlay-spinner loading-${action}"></div>
                <div class="loading-overlay-text">${message}</div>
                <div class="mt-2">
                    <small class="text-muted">Processando...</small>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Auto-remover após timeout de segurança
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 10000); // 10 segundos máximo para evitar loading infinito
    }

    /**
     * Mostrar sucesso rápido e redirecionar
     */
    showQuickSuccess(message, redirectUrl) {
        // Remover loading atual
        const currentOverlay = document.querySelector('.loading-overlay');
        if (currentOverlay) {
            currentOverlay.remove();
        }

        // Mostrar sucesso
        const successOverlay = document.createElement('div');
        successOverlay.className = 'loading-overlay show';
        successOverlay.innerHTML = `
            <div class="loading-overlay-content">
                <div class="text-success mb-3">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
                <div class="loading-overlay-text">${message}</div>
                <div class="mt-2">
                    <small class="text-muted">Redirecionando...</small>
                </div>
            </div>
        `;

        document.body.appendChild(successOverlay);

        // Redirecionar rapidamente
        setTimeout(() => {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }, 600); // Redirecionamento em 600ms
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.optimizedForcingLoader = new OptimizedForcingLoader();
});

/**
 * 🎯 Interceptação de Formulários com Fetch Otimizado
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            const action = form.action;
            
            // Se for uma ação de forcing, interceptar
            if (action.includes('/liberar') || action.includes('/executar') || 
                action.includes('/solicitar-retirada') || action.includes('/retirar')) {
                
                e.preventDefault();
                
                const formData = new FormData(form);
                
                try {
                    // Mostrar loading
                    if (window.optimizedForcingLoader) {
                        window.optimizedForcingLoader.showQuickLoading(
                            action.includes('/liberar') ? 'liberar' : 
                            action.includes('/executar') ? 'executar' : 
                            action.includes('/solicitar-retirada') ? 'retirada' : 'retirar',
                            'Processando...'
                        );
                    }

                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const result = await response.text();
                        if (result.includes('success')) {
                            // Extrair ID do forcing da URL da ação
                            const forcingId = action.match(/forcing\/(\d+)/);
                            const redirectUrl = forcingId ? `/forcing/${forcingId[1]}` : null;
                            
                            // Mostrar sucesso e redirecionar
                            if (window.optimizedForcingLoader) {
                                window.optimizedForcingLoader.showQuickSuccess(
                                    'Ação realizada com sucesso!',
                                    redirectUrl
                                );
                            }
                        } else {
                            // Mostrar erro
                            this.showError('Erro ao processar ação');
                        }
                    } else {
                        this.showError('Erro no servidor');
                    }
                } catch (error) {
                    this.showError('Erro de conexão');
                }
            }
        });
    });
});

/**
 * 🎯 Utilitários de Erro
 */
function showError(message) {
    // Remover loading atual
    const currentOverlay = document.querySelector('.loading-overlay');
    if (currentOverlay) {
        currentOverlay.remove();
    }

    // Mostrar erro
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
    errorDiv.style.position = 'fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.right = '20px';
    errorDiv.style.zIndex = '10000';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(errorDiv);

    setTimeout(() => {
        if (errorDiv.parentNode) {
            errorDiv.parentNode.removeChild(errorDiv);
        }
    }, 5000);
}

