<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent', '');
        $isMobile = $this->isMobileDevice($userAgent);
        $isApi = $request->is('api/*');
        
        // Adicionar informações do dispositivo à requisição
        $request->merge([
            'device_info' => [
                'is_mobile' => $isMobile,
                'is_api' => $isApi,
                'user_agent' => $userAgent,
                'platform' => $this->detectPlatform($userAgent),
                'browser' => $this->detectBrowser($userAgent),
            ]
        ]);

        // Log do acesso para analytics
        $this->logDeviceAccess($request, $isMobile, $isApi);

        return $next($request);
    }

    /**
     * Detecta se é um dispositivo mobile
     */
    private function isMobileDevice(string $userAgent): bool
    {
        $mobileKeywords = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry',
            'Windows Phone', 'Opera Mini', 'IEMobile', 'Mobile Safari',
            'Expo', 'ReactNative', 'Cordova', 'PhoneGap'
        ];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta a plataforma
     */
    private function detectPlatform(string $userAgent): string
    {
        if (stripos($userAgent, 'Android') !== false) {
            return 'android';
        }
        
        if (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            return 'ios';
        }
        
        if (stripos($userAgent, 'Windows') !== false) {
            return 'windows';
        }
        
        if (stripos($userAgent, 'Mac') !== false) {
            return 'macos';
        }
        
        if (stripos($userAgent, 'Linux') !== false) {
            return 'linux';
        }

        return 'unknown';
    }

    /**
     * Detecta o navegador
     */
    private function detectBrowser(string $userAgent): string
    {
        if (stripos($userAgent, 'Chrome') !== false) {
            return 'chrome';
        }
        
        if (stripos($userAgent, 'Firefox') !== false) {
            return 'firefox';
        }
        
        if (stripos($userAgent, 'Safari') !== false) {
            return 'safari';
        }
        
        if (stripos($userAgent, 'Edge') !== false) {
            return 'edge';
        }

        if (stripos($userAgent, 'Expo') !== false) {
            return 'expo';
        }

        return 'unknown';
    }

    /**
     * Log do acesso para analytics
     */
    private function logDeviceAccess(Request $request, bool $isMobile, bool $isApi): void
    {
        $logData = [
            'timestamp' => now(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'is_mobile' => $isMobile,
            'is_api' => $isApi,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'platform' => $request->get('device_info')['platform'],
            'browser' => $request->get('device_info')['browser'],
        ];

        // Log apenas se não for uma requisição de API para evitar spam
        if (!$isApi) {
            \Log::info('Device Access', $logData);
        }
    }
}

