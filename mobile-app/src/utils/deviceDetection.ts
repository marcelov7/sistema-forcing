import { Platform } from 'react-native';

export interface DeviceInfo {
  platform: 'ios' | 'android' | 'web';
  isMobile: boolean;
  isTablet: boolean;
  version: string;
  model?: string;
}

/**
 * Detecta informações do dispositivo
 */
export function getDeviceInfo(): DeviceInfo {
  const platform = Platform.OS;
  const version = Platform.Version.toString();
  
  return {
    platform: platform as 'ios' | 'android' | 'web',
    isMobile: Platform.OS !== 'web',
    isTablet: Platform.isPad || (Platform.OS === 'android' && Platform.Version >= 23),
    version,
    model: Platform.select({
      ios: Platform.constants?.model,
      android: Platform.constants?.Model,
    }),
  };
}

/**
 * Detecta se é um dispositivo móvel
 */
export function isMobileDevice(): boolean {
  return Platform.OS !== 'web';
}

/**
 * Detecta se é um tablet
 */
export function isTabletDevice(): boolean {
  return Platform.isPad || (Platform.OS === 'android' && Platform.Version >= 23);
}

/**
 * Obtém informações para enviar ao backend
 */
export function getDeviceHeaders() {
  const deviceInfo = getDeviceInfo();
  
  return {
    'X-Device-Platform': deviceInfo.platform,
    'X-Device-Version': deviceInfo.version,
    'X-Device-Model': deviceInfo.model || 'Unknown',
    'X-Is-Mobile': deviceInfo.isMobile.toString(),
    'X-Is-Tablet': deviceInfo.isTablet.toString(),
    'X-App-Version': '1.0.0',
  };
}

/**
 * Detecta se deve mostrar interface mobile ou desktop
 */
export function shouldShowMobileInterface(): boolean {
  return isMobileDevice();
}

