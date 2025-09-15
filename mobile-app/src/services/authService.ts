import { apiService } from './api';
import { User } from '../types/User';

interface LoginResponse {
  user: User;
  token: string;
  token_type: string;
  expires_in: number;
}

export const authService = {
  async login(username: string, password: string): Promise<LoginResponse> {
    const response = await apiService.post<LoginResponse>('/auth/login', {
      username,
      password,
    });

    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro no login');
    }

    return response.data;
  },

  async logout(): Promise<void> {
    await apiService.post('/auth/logout');
  },

  async getMe(): Promise<User> {
    const response = await apiService.get<{ user: User }>('/auth/me');
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao obter dados do usuário');
    }

    return response.data.user;
  },

  async refreshToken(): Promise<string> {
    const response = await apiService.post<{ token: string }>('/auth/refresh');
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao renovar token');
    }

    return response.data.token;
  },
};

