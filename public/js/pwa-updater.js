/**
 * Sistema de Atualização Automática para PWA
 * Gerencia atualizações do Service Worker e notifica o usuário
 */
class PWAUpdater {
    constructor() {
        this.registration = null;
        this.updateAvailable = false;
        this.updateBanner = null;
        this.init();
    }

    async init() {
        // Verifica se o navegador suporta Service Worker
        if ('serviceWorker' in navigator) {
            try {
                // Registra o Service Worker
                this.registration = await navigator.serviceWorker.register('/sw.js');
                console.log('SW: Registrado com sucesso');

                // Configura listeners para atualizações
                this.setupUpdateListeners();
                
                // Verifica atualizações periodicamente
                this.checkForUpdates();
                
                // Verifica atualizações quando a página ganha foco
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        this.checkForUpdates();
                    }
                });

            } catch (error) {
                console.error('SW: Falha no registro:', error);
            }
        }
    }

    setupUpdateListeners() {
        // Listener para quando um novo Service Worker é instalado
        this.registration.addEventListener('updatefound', () => {
            const newWorker = this.registration.installing;
            
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed') {
                    if (navigator.serviceWorker.controller) {
                        // Nova versão disponível
                        this.updateAvailable = true;
                        this.showUpdateBanner();
                    } else {
                        // Primeira instalação
                        console.log('PWA: Instalado pela primeira vez');
                    }
                }
            });
        });

        // Listener para quando o Service Worker assume controle
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            // Recarrega a página quando um novo SW assume controle
            window.location.reload();
        });

        // Listener para mensagens do Service Worker
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data && event.data.action === 'updateAvailable') {
                this.updateAvailable = true;
                this.showUpdateBanner(event.data.newVersion);
            }
        });
    }

    async checkForUpdates() {
        if (this.registration) {
            try {
                await this.registration.update();
            } catch (error) {
                console.log('SW: Erro ao verificar atualizações:', error);
            }
        }
    }

    showUpdateBanner(newVersion = null) {
        // Remove banner existente se houver
        this.removeUpdateBanner();

        // Cria o banner de atualização
        this.updateBanner = document.createElement('div');
        this.updateBanner.id = 'pwa-update-banner';
        this.updateBanner.innerHTML = `
            <div class="update-banner-content">
                <div class="update-banner-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="update-banner-text">
                    <strong>Nova versão disponível!</strong>
                    <span>Uma atualização do sistema foi encontrada.</span>
                    ${newVersion ? `<small>Versão: ${newVersion}</small>` : ''}
                </div>
                <div class="update-banner-actions">
                    <button id="update-now" class="btn btn-primary btn-sm">
                        <i class="fas fa-download"></i> Atualizar Agora
                    </button>
                    <button id="update-later" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i> Depois
                    </button>
                </div>
            </div>
        `;

        // Adiciona estilos inline para garantir que funcione
        this.updateBanner.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
        `;

        // Adiciona estilos para o conteúdo
        const style = document.createElement('style');
        style.textContent = `
            .update-banner-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 1200px;
                margin: 0 auto;
                gap: 15px;
            }
            
            .update-banner-icon {
                font-size: 20px;
                animation: spin 2s linear infinite;
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            
            .update-banner-text {
                flex: 1;
            }
            
            .update-banner-text strong {
                display: block;
                font-size: 14px;
                margin-bottom: 2px;
            }
            
            .update-banner-text span {
                font-size: 13px;
                opacity: 0.9;
            }
            
            .update-banner-text small {
                display: block;
                font-size: 11px;
                opacity: 0.7;
                margin-top: 2px;
            }
            
            .update-banner-actions {
                display: flex;
                gap: 8px;
            }
            
            .update-banner-actions .btn {
                border: none;
                padding: 6px 12px;
                border-radius: 4px;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .update-banner-actions .btn-primary {
                background: rgba(255,255,255,0.2);
                color: white;
                border: 1px solid rgba(255,255,255,0.3);
            }
            
            .update-banner-actions .btn-primary:hover {
                background: rgba(255,255,255,0.3);
                transform: translateY(-1px);
            }
            
            .update-banner-actions .btn-outline-secondary {
                background: transparent;
                color: white;
                border: 1px solid rgba(255,255,255,0.3);
            }
            
            .update-banner-actions .btn-outline-secondary:hover {
                background: rgba(255,255,255,0.1);
            }
            
            /* Responsivo */
            @media (max-width: 768px) {
                .update-banner-content {
                    flex-direction: column;
                    text-align: center;
                    gap: 10px;
                }
                
                .update-banner-actions {
                    width: 100%;
                    justify-content: center;
                }
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(this.updateBanner);

        // Adiciona padding ao body para compensar o banner
        document.body.style.paddingTop = '60px';

        // Anima o banner para baixo
        setTimeout(() => {
            this.updateBanner.style.transform = 'translateY(0)';
        }, 100);

        // Configura os botões
        this.setupBannerButtons();

        // Auto-remove após 30 segundos se não interagir
        this.autoHideTimer = setTimeout(() => {
            this.removeUpdateBanner();
        }, 30000);
    }

    setupBannerButtons() {
        const updateNowBtn = document.getElementById('update-now');
        const updateLaterBtn = document.getElementById('update-later');

        if (updateNowBtn) {
            updateNowBtn.addEventListener('click', () => {
                this.applyUpdate();
            });
        }

        if (updateLaterBtn) {
            updateLaterBtn.addEventListener('click', () => {
                this.removeUpdateBanner();
            });
        }
    }

    async applyUpdate() {
        if (this.registration && this.registration.waiting) {
            // Envia mensagem para o Service Worker para pular o estado waiting
            this.registration.waiting.postMessage({ action: 'skipWaiting' });
        }

        this.removeUpdateBanner();
        
        // Mostra loading
        this.showLoadingOverlay();
    }

    showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'pwa-update-loading';
        overlay.innerHTML = `
            <div class="update-loading-content">
                <div class="update-loading-spinner">
                    <i class="fas fa-sync-alt fa-spin"></i>
                </div>
                <h3>Atualizando Sistema...</h3>
                <p>Aguarde enquanto aplicamos a nova versão.</p>
            </div>
        `;

        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            color: white;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        `;

        const style = document.createElement('style');
        style.textContent = `
            .update-loading-content {
                text-align: center;
                padding: 40px;
            }
            
            .update-loading-spinner {
                font-size: 48px;
                margin-bottom: 20px;
                color: #667eea;
            }
            
            .update-loading-content h3 {
                margin: 0 0 10px 0;
                font-size: 24px;
                font-weight: 600;
            }
            
            .update-loading-content p {
                margin: 0;
                font-size: 16px;
                opacity: 0.8;
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(overlay);
    }

    removeUpdateBanner() {
        if (this.updateBanner) {
            this.updateBanner.style.transform = 'translateY(-100%)';
            setTimeout(() => {
                if (this.updateBanner && this.updateBanner.parentNode) {
                    this.updateBanner.parentNode.removeChild(this.updateBanner);
                }
                this.updateBanner = null;
                
                // Remove padding do body
                document.body.style.paddingTop = '';
            }, 300);
        }

        if (this.autoHideTimer) {
            clearTimeout(this.autoHideTimer);
            this.autoHideTimer = null;
        }
    }

    // Método público para verificar atualizações manualmente
    async checkForUpdatesManually() {
        if (this.registration) {
            try {
                await this.registration.update();
                return true;
            } catch (error) {
                console.error('Erro ao verificar atualizações:', error);
                return false;
            }
        }
        return false;
    }

    // Método para obter a versão atual
    getCurrentVersion() {
        return this.registration ? 'Versão atual do PWA' : 'PWA não instalado';
    }
}

// Inicializa o sistema de atualização quando a página carrega
document.addEventListener('DOMContentLoaded', () => {
    window.pwaUpdater = new PWAUpdater();
});

// Adiciona método global para verificar atualizações
window.checkPWAUpdate = function() {
    if (window.pwaUpdater) {
        return window.pwaUpdater.checkForUpdatesManually();
    }
    return Promise.resolve(false);
};

// Adiciona listener para erros não capturados (útil para debug)
window.addEventListener('unhandledrejection', (event) => {
    console.error('Erro não capturado:', event.reason);
    // Aqui você pode enviar logs para um serviço de monitoramento
});

// Adiciona listener para erros JavaScript
window.addEventListener('error', (event) => {
    console.error('Erro JavaScript:', event.error);
    // Aqui você pode enviar logs para um serviço de monitoramento
});

