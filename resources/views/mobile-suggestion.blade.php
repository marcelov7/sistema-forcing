<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Forcing - Versão Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Meta tags para PWA -->
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Sistema Forcing">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .mobile-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
        }
        .app-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .app-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        .download-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: transform 0.3s ease;
        }
        .download-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        .continue-web {
            background: #6c757d;
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        .continue-web:hover {
            color: white;
            background: #5a6268;
        }
        .pwa-install {
            background: #28a745;
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            cursor: pointer;
        }
        .pwa-install:hover {
            background: #218838;
            color: white;
        }
        .device-info {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            color: white;
            font-size: 0.9rem;
        }
        .qr-code {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            display: inline-block;
        }
        .install-banner {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .install-steps {
            text-align: left;
            margin-top: 15px;
        }
        .install-steps li {
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Banner de Instalação PWA (aparece automaticamente) -->
        <div id="pwa-install-banner" class="install-banner hidden">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-download fa-2x text-primary"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">Instalar App</h6>
                    <p class="mb-0 text-muted small">Adicione à tela inicial para acesso rápido</p>
                </div>
                <button id="pwa-install-btn" class="btn btn-primary btn-sm">Instalar</button>
                <button id="pwa-dismiss-btn" class="btn btn-link btn-sm ms-2">×</button>
            </div>
        </div>

        <div class="app-card">
            <div class="app-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            
            <h2 class="mb-3">Sistema de Forcing</h2>
            <p class="text-muted mb-4">
                Detectamos que você está acessando via dispositivo móvel. 
                Para uma melhor experiência, recomendamos usar nosso aplicativo.
            </p>

            <!-- QR Code para download -->
            <div class="qr-code">
                <div id="qrcode"></div>
                <p class="mt-2 text-muted small">Escaneie com seu celular</p>
            </div>

            <!-- Botões de ação -->
            <div class="mt-4">
                <!-- PWA Install (aparece se suportado) -->
                <button id="pwa-install-main" class="pwa-install hidden">
                    <i class="fas fa-plus-circle me-2"></i>
                    Adicionar à Tela Inicial
                </button>
                
                <!-- Download App Nativo -->
                <a href="#" class="download-btn" onclick="downloadApp('android')">
                    <i class="fab fa-android me-2"></i>
                    Download Android
                </a>
                <a href="#" class="download-btn" onclick="downloadApp('ios')">
                    <i class="fab fa-apple me-2"></i>
                    Download iOS
                </a>
            </div>

            <!-- Continuar na web -->
            <div class="mt-3">
                <a href="{{ route('forcing.index') }}" class="continue-web">
                    <i class="fas fa-desktop me-2"></i>
                    Continuar na Web
                </a>
            </div>

            <!-- Instruções de instalação -->
            <div id="install-instructions" class="install-banner hidden mt-4">
                <h6><i class="fas fa-info-circle me-2"></i>Como Instalar:</h6>
                <div id="android-steps" class="install-steps hidden">
                    <ol>
                        <li>Toque no menu do navegador (⋮)</li>
                        <li>Selecione "Adicionar à tela inicial"</li>
                        <li>Toque em "Adicionar"</li>
                    </ol>
                </div>
                <div id="ios-steps" class="install-steps hidden">
                    <ol>
                        <li>Toque no botão de compartilhar (□↗)</li>
                        <li>Role para baixo e toque "Adicionar à Tela de Início"</li>
                        <li>Toque em "Adicionar"</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Informações do dispositivo -->
        <div class="device-info">
            <h6><i class="fas fa-info-circle me-2"></i>Informações do Dispositivo</h6>
            <div class="row">
                <div class="col-6">
                    <strong>Plataforma:</strong><br>
                    <span class="badge bg-light text-dark">{{ ucfirst($deviceInfo['platform'] ?? 'Unknown') }}</span>
                </div>
                <div class="col-6">
                    <strong>Navegador:</strong><br>
                    <span class="badge bg-light text-dark">{{ ucfirst($deviceInfo['browser'] ?? 'Unknown') }}</span>
                </div>
            </div>
            <div class="mt-2">
                <small>
                    <i class="fas fa-user me-1"></i>
                    Logado como: <strong>{{ $user->name }}</strong>
                </small>
            </div>
            <div class="mt-2">
                <small>
                    <i class="fas fa-cog me-1"></i>
                    PWA: <span id="pwa-status" class="badge bg-secondary">Verificando...</span>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        let deferredPrompt;
        let isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        let isAndroid = /Android/.test(navigator.userAgent);
        let isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                          window.navigator.standalone === true;

        // Verificar se PWA está instalado
        if (isStandalone) {
            document.getElementById('pwa-status').textContent = 'Instalado';
            document.getElementById('pwa-status').className = 'badge bg-success';
        }

        // Gerar QR Code
        const qrData = window.location.origin + '/api/mobile-download';
        QRCode.toCanvas(document.getElementById('qrcode'), qrData, {
            width: 150,
            height: 150,
            color: {
                dark: '#667eea',
                light: '#ffffff'
            }
        });

        // Detectar suporte a PWA
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('PWA install prompt disponível');
            e.preventDefault();
            deferredPrompt = e;
            
            // Mostrar banner de instalação
            document.getElementById('pwa-install-banner').classList.remove('hidden');
            document.getElementById('pwa-install-main').classList.remove('hidden');
            document.getElementById('pwa-status').textContent = 'Suportado';
            document.getElementById('pwa-status').className = 'badge bg-success';
        });

        // Instalar PWA
        document.getElementById('pwa-install-btn').addEventListener('click', () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA instalado com sucesso');
                        document.getElementById('pwa-install-banner').classList.add('hidden');
                    }
                    deferredPrompt = null;
                });
            }
        });

        // Instalar PWA (botão principal)
        document.getElementById('pwa-install-main').addEventListener('click', () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA instalado com sucesso');
                        // Redirecionar para o app
                        window.location.href = '/forcing';
                    }
                    deferredPrompt = null;
                });
            } else {
                // Mostrar instruções manuais
                showInstallInstructions();
            }
        });

        // Dismissar banner
        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            document.getElementById('pwa-install-banner').classList.add('hidden');
            localStorage.setItem('pwa_banner_dismissed', 'true');
        });

        // Mostrar instruções de instalação manual
        function showInstallInstructions() {
            const instructions = document.getElementById('install-instructions');
            const androidSteps = document.getElementById('android-steps');
            const iosSteps = document.getElementById('ios-steps');
            
            instructions.classList.remove('hidden');
            
            if (isIOS) {
                iosSteps.classList.remove('hidden');
            } else if (isAndroid) {
                androidSteps.classList.remove('hidden');
            }
        }

        // Download do app nativo
        function downloadApp(platform) {
            if (platform === 'android') {
                // Tentar abrir Google Play Store
                const playStoreUrl = 'https://play.google.com/store/apps/details?id=com.seuapp.forcing';
                window.open(playStoreUrl, '_blank');
            } else if (platform === 'ios') {
                // Tentar abrir App Store
                const appStoreUrl = 'https://apps.apple.com/app/forcing-system/id123456789';
                window.open(appStoreUrl, '_blank');
            }
        }

        // Detectar se já tem o app instalado
        function checkAppInstalled() {
            // Tentar abrir o app via deep link
            const deepLink = 'forcing://open';
            const fallbackUrl = window.location.origin + '/api/mobile-download';
            
            window.location.href = deepLink;
            
            // Se não conseguir abrir o app, mostrar opções de download
            setTimeout(() => {
                // App não instalado, mostrar botões de download
                document.querySelector('.download-btn').style.display = 'inline-block';
            }, 1000);
        }

        // Auto-detectar na primeira visita
        if (!localStorage.getItem('forcing_app_suggested')) {
            checkAppInstalled();
            localStorage.setItem('forcing_app_suggested', 'true');
        }

        // Verificar se banner foi dismissado
        if (localStorage.getItem('pwa_banner_dismissed') === 'true') {
            document.getElementById('pwa-install-banner').classList.add('hidden');
        }

        // Detectar mudanças no display mode
        window.matchMedia('(display-mode: standalone)').addEventListener('change', (e) => {
            if (e.matches) {
                console.log('App instalado como PWA');
                document.getElementById('pwa-status').textContent = 'Instalado';
                document.getElementById('pwa-status').className = 'badge bg-success';
            }
        });

        // Service Worker registration (para PWA)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('Service Worker registrado:', registration);
                })
                .catch((error) => {
                    console.log('Erro ao registrar Service Worker:', error);
                });
        }
    </script>
</body>
</html>