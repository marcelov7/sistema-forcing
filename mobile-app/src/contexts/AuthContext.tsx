import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authService } from '../services/authService';
import { User } from '../types/User';

interface AuthContextData {
  user: User | null;
  loading: boolean;
  login: (username: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  refreshToken: () => Promise<void>;
}

const AuthContext = createContext<AuthContextData>({} as AuthContextData);

interface AuthProviderProps {
  children: ReactNode;
}

export function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadStoredUser();
  }, []);

  async function loadStoredUser() {
    try {
      const token = await AsyncStorage.getItem('@forcing:token');
      if (token) {
        const userData = await authService.getMe();
        setUser(userData);
      }
    } catch (error) {
      console.error('Erro ao carregar usuário:', error);
      await AsyncStorage.removeItem('@forcing:token');
    } finally {
      setLoading(false);
    }
  }

  async function login(username: string, password: string) {
    try {
      const response = await authService.login(username, password);
      
      await AsyncStorage.setItem('@forcing:token', response.token);
      setUser(response.user);
    } catch (error) {
      throw error;
    }
  }

  async function logout() {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Erro ao fazer logout:', error);
    } finally {
      await AsyncStorage.removeItem('@forcing:token');
      setUser(null);
    }
  }

  async function refreshToken() {
    try {
      const newToken = await authService.refreshToken();
      await AsyncStorage.setItem('@forcing:token', newToken);
    } catch (error) {
      console.error('Erro ao renovar token:', error);
      await logout();
    }
  }

  return (
    <AuthContext.Provider value={{
      user,
      loading,
      login,
      logout,
      refreshToken,
    }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth deve ser usado dentro de um AuthProvider');
  }
  return context;
}

