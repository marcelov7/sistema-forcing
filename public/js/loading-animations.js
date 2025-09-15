/**
 * 🎯 Sistema de Animações de Loading para Sistema de Forcing
 * Gerencia animações durante processamento de ações
 */

class ForcingLoadingManager {
    constructor() {
        this.activeLoadings = new Set();
        this.init();
    }

    init() {
        // Interceptar formulários de ação
        this.interceptForms();
        
        // Interceptar links de ação
        this.interceptActionLinks();
        
        // Configurar timeouts para evitar loading infinito
        this.setupTimeouts();
    }

    /**
     * Interceptar formulários de ação (modais)
     */
    interceptForms() {
        // Formulários de liberar
        document.querySelectorAll('form[action*="/liberar"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showLoading('liberar', 'Liberando forcing...');
            });
        });

        // Formulários de executar
        document.querySelectorAll('form[action*="/registrar-execucao"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showLoading('executar', 'Registrando execução...');
            });
        });

        // Formulários de solicitar retirada
        document.querySelectorAll('form[action*="/solicitar-retirada"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showLoading('retirada', 'Solicitando retirada...');
            });
        });

        // Formulários de retirar
        document.querySelectorAll('form[action*="/retirar"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showLoading('retirar', 'Retirando forcing...');
            });
        });
    }

    /**
     * Interceptar links de ação (mobile)
     */
    interceptActionLinks() {
        // Links de liberar
        document.querySelectorAll('a[href*="/liberar-page"]').forEach(link => {
            link.addEventListener('click', (e) => {
                this.showLoading('liberar', 'Abrindo página de liberação...');
            });
        });

        // Links de executar
        document.querySelectorAll('a[href*="/executar-page"]').forEach(link => {
            link.addEventListener('click', (e) => {
                this.showLoading('executar', 'Abrindo página de execução...');
            });
        });

        // Links de solicitar retirada
        document.querySelectorAll('a[href*="/solicitar-retirada-page"]').forEach(link => {
            link.addEventListener('click', (e) => {
                this.showLoading('retirada', 'Abrindo página de solicitação...');
            });
        });

        // Links de retirar
        document.querySelectorAll('a[href*="/retirar-page"]').forEach(link => {
            link.addEventListener('click', (e) => {
                this.showLoading('retirar', 'Abrindo página de retirada...');
            });
        });
    }

    /**
     * Mostrar loading com overlay
     */
    showLoading(action, message) {
        const loadingId = `${action}_${Date.now()}`;
        this.activeLoadings.add(loadingId);

        // Criar overlay de loading
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay show';
        overlay.id = `loading-${loadingId}`;
        
        overlay.innerHTML = `
            <div class="loading-overlay-content">
                <div class="loading-overlay-spinner loading-${action}"></div>
                <div class="loading-overlay-text">${message}</div>
                <div class="mt-2">
                    <small class="text-muted">Por favor, aguarde...</small>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        // Auto-remover após timeout
        setTimeout(() => {
            this.hideLoading(loadingId);
        }, 30000); // 30 segundos máximo

        return loadingId;
    }

    /**
     * Esconder loading
     */
    hideLoading(loadingId) {
        const overlay = document.getElementById(`loading-${loadingId}`);
        if (overlay) {
            overlay.classList.remove('show');
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }, 300);
        }
        this.activeLoadings.delete(loadingId);
    }

    /**
     * Mostrar loading em botão específico
     */
    showButtonLoading(button, action, message) {
        const originalText = button.innerHTML;
        button.classList.add('btn-loading');
        button.disabled = true;
        
        button.innerHTML = `
            <div class="loading-spinner loading-${action}"></div>
            <span class="btn-text">${message}</span>
        `;

        return () => {
            button.classList.remove('btn-loading');
            button.disabled = false;
            button.innerHTML = originalText;
        };
    }

    /**
     * Configurar timeouts para evitar loading infinito
     */
    setupTimeouts() {
        // Limpar loadings ativos quando a página carregar
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.activeLoadings.forEach(loadingId => {
                    this.hideLoading(loadingId);
                });
            }, 1000);
        });

        // Limpar loadings quando sair da página
        window.addEventListener('beforeunload', () => {
            this.activeLoadings.forEach(loadingId => {
                this.hideLoading(loadingId);
            });
        });
    }

    /**
     * Mostrar toast de notificação
     */
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-loading alert alert-${type} alert-dismissible fade show`;
        
        toast.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="loading-spinner me-2"></div>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, duration);
    }

    /**
     * Mostrar erro com animação
     */
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show shake';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Inserir no topo da página
        const container = document.querySelector('.container-fluid');
        if (container) {
            container.insertBefore(errorDiv, container.firstChild);
        }

        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.parentNode.removeChild(errorDiv);
            }
        }, 5000);
    }
}

/**
 * 🎯 Utilitários para animações específicas
 */
class ForcingAnimations {
    /**
     * Animação de sucesso
     */
    static showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.style.position = 'fixed';
        successDiv.style.top = '20px';
        successDiv.style.right = '20px';
        successDiv.style.zIndex = '10000';
        successDiv.style.minWidth = '300px';
        
        successDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.appendChild(successDiv);

        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.parentNode.removeChild(successDiv);
            }
        }, 4000);
    }

    /**
     * Animação de progresso
     */
    static showProgress(action, progress = 0) {
        let progressDiv = document.getElementById('progress-animation');
        
        if (!progressDiv) {
            progressDiv = document.createElement('div');
            progressDiv.id = 'progress-animation';
            progressDiv.className = 'loading-overlay show';
            progressDiv.innerHTML = `
                <div class="loading-overlay-content">
                    <div class="loading-overlay-text mb-3">${action}</div>
                    <div class="progress" style="width: 200px;">
                        <div class="progress-bar progress-bar-animated" style="width: ${progress}%"></div>
                    </div>
                    <small class="text-muted mt-2">${progress}% concluído</small>
                </div>
            `;
            document.body.appendChild(progressDiv);
        } else {
            const progressBar = progressDiv.querySelector('.progress-bar');
            const progressText = progressDiv.querySelector('.text-muted');
            if (progressBar) progressBar.style.width = `${progress}%`;
            if (progressText) progressText.textContent = `${progress}% concluído`;
        }

        if (progress >= 100) {
            setTimeout(() => {
                if (progressDiv.parentNode) {
                    progressDiv.parentNode.removeChild(progressDiv);
                }
            }, 1000);
        }
    }

    /**
     * Animação de pulsação para botões
     */
    static pulseButton(button, duration = 2000) {
        button.classList.add('btn-pulse');
        setTimeout(() => {
            button.classList.remove('btn-pulse');
        }, duration);
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.forcingLoadingManager = new ForcingLoadingManager();
    window.forcingAnimations = ForcingAnimations;
});

// Interceptar respostas AJAX para esconder loading
document.addEventListener('DOMContentLoaded', () => {
    // Interceptar formulários com fetch
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            const action = form.action;
            
            // Se for uma ação de forcing, interceptar
            if (action.includes('/liberar') || action.includes('/executar') || 
                action.includes('/solicitar-retirada') || action.includes('/retirar')) {
                
                e.preventDefault();
                
                const formData = new FormData(form);
                const loadingId = window.forcingLoadingManager.showLoading(
                    action.includes('/liberar') ? 'liberar' : 
                    action.includes('/executar') ? 'executar' : 
                    action.includes('/solicitar-retirada') ? 'retirada' : 'retirar',
                    'Processando...'
                );

                try {
                    const response = await fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    window.forcingLoadingManager.hideLoading(loadingId);

                    if (response.ok) {
                        const result = await response.text();
                        if (result.includes('success')) {
                            window.forcingAnimations.showSuccess('Ação realizada com sucesso!');
                            
                            // Extrair ID do forcing da URL da ação
                            const forcingId = action.match(/forcing\/(\d+)/);
                            if (forcingId) {
                                setTimeout(() => {
                                    window.location.href = `/forcing/${forcingId[1]}`;
                                }, 800);
                            } else {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 800);
                            }
                        } else {
                            window.forcingLoadingManager.showError('Erro ao processar ação');
                        }
                    } else {
                        window.forcingLoadingManager.showError('Erro no servidor');
                    }
                } catch (error) {
                    window.forcingLoadingManager.hideLoading(loadingId);
                    window.forcingLoadingManager.showError('Erro de conexão');
                }
            }
        });
    });
});
