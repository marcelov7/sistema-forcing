const CACHE_VERSION = 'forcing-pwa-v2025-09-14-2324';
const CACHE_NAME = `forcing-cache-${CACHE_VERSION}`;
const OFFLINE_CACHE = 'forcing-offline-v1';

// URLs para cachear (shell da aplicação)
const urlsToCache = [
    '/',
    '/forcing',
    '/offline.html',
    '/css/app.css',
    '/js/app.js',
    '/js/loading-animations.js',
    '/js/dropdown-fix.js',
    '/manifest.json'
];

// Instalação do Service Worker
self.addEventListener('install', (event) => {
    console.log('SW: Instalando versão', CACHE_VERSION);
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('SW: Cache aberto');
                return cache.addAll(urlsToCache);
            })
            .then(() => {
                console.log('SW: Cache preenchido');
                // Força a ativação imediata
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('SW: Erro na instalação:', error);
            })
    );
});

// Ativação do Service Worker
self.addEventListener('activate', (event) => {
    console.log('SW: Ativando versão', CACHE_VERSION);
    
    event.waitUntil(
        Promise.all([
            // Limpa caches antigos
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && cacheName !== OFFLINE_CACHE) {
                            console.log('SW: Removendo cache antigo:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }),
            // Assume controle imediato de todas as abas
            self.clients.claim()
        ])
    );
});

// Estratégias de cache
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Estratégia para HTML (Network First - sempre tenta rede primeiro)
    if (request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            networkFirstStrategy(request, CACHE_NAME)
        );
        return;
    }
    
    // Estratégia para API (Network Only - sempre busca na rede)
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Cacheia respostas de sucesso da API por 5 minutos
                    if (response.ok) {
                        const cacheResponse = response.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, cacheResponse);
                        });
                    }
                    return response;
                })
                .catch(() => {
                    // Em caso de falha, tenta o cache como fallback
                    return caches.match(request);
                })
        );
        return;
    }
    
    // Estratégia para assets estáticos (Cache First com revalidação)
    if (request.destination === 'script' || 
        request.destination === 'style' || 
        request.destination === 'image') {
        event.respondWith(
            cacheFirstStrategy(request, CACHE_NAME)
        );
        return;
    }
    
    // Estratégia padrão (Network First)
    event.respondWith(
        networkFirstStrategy(request, CACHE_NAME)
    );
});

// Estratégia Network First (tenta rede, fallback para cache)
async function networkFirstStrategy(request, cacheName) {
    try {
        const networkResponse = await fetch(request);
        
        // Se a resposta da rede for bem-sucedida, atualiza o cache
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('SW: Rede falhou, tentando cache:', request.url);
        
        // Tenta o cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Se for uma página HTML e não tiver cache, mostra offline
        if (request.headers.get('accept').includes('text/html')) {
            return caches.match('/offline.html');
        }
        
        // Para outros recursos, retorna erro
        return new Response('Recurso não disponível offline', { 
            status: 503, 
            statusText: 'Service Unavailable' 
        });
    }
}

// Estratégia Cache First (tenta cache, revalida em background)
async function cacheFirstStrategy(request, cacheName) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        // Revalida em background
        fetch(request).then((networkResponse) => {
            if (networkResponse.ok) {
                caches.open(cacheName).then((cache) => {
                    cache.put(request, networkResponse);
                });
            }
        }).catch(() => {
            // Ignora erros de rede na revalidação
        });
        
        return cachedResponse;
    }
    
    // Se não tiver cache, tenta a rede
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        return new Response('Recurso não disponível', { 
            status: 503, 
            statusText: 'Service Unavailable' 
        });
    }
}

// Comunicação com o cliente
self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.action === 'getVersion') {
        event.ports[0].postMessage({ version: CACHE_VERSION });
    }
});

// Notificação de atualização disponível
self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'checkUpdate') {
        // Verifica se há uma nova versão disponível
        fetch('/sw.js?v=' + Date.now())
            .then(response => response.text())
            .then(swText => {
                const newVersion = swText.match(/const CACHE_VERSION = 'forcing-pwa-v2025-09-14-2324']+)'/);
                if (newVersion && newVersion[1] !== CACHE_VERSION) {
                    event.ports[0].postMessage({ 
                        updateAvailable: true, 
                        newVersion: newVersion[1],
                        currentVersion: CACHE_VERSION 
                    });
                }
            })
            .catch(() => {
                // Ignora erros de verificação
            });
    }
});
