import { apiService } from './api';
import { Forcing, ForcingFilters, DashboardStats } from '../types/Forcing';

export const forcingService = {
  async getForcings(filters?: ForcingFilters, page: number = 1): Promise<{
    data: Forcing[];
    meta: {
      current_page: number;
      last_page: number;
      per_page: number;
      total: number;
      from: number;
      to: number;
    };
  }> {
    const params = {
      page,
      per_page: 15,
      ...filters,
    };

    const response = await apiService.get<{
      data: Forcing[];
      meta: any;
    }>('/forcings', params);

    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao buscar forcings');
    }

    return response.data;
  },

  async getForcing(id: number): Promise<Forcing> {
    const response = await apiService.get<Forcing>(`/forcings/${id}`);
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao buscar forcing');
    }

    return response.data;
  },

  async createForcing(data: {
    tag: string;
    situacao_equipamento: string;
    descricao_equipamento: string;
    area: string;
    observacoes?: string;
  }): Promise<Forcing> {
    const response = await apiService.post<Forcing>('/forcings', data);
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao criar forcing');
    }

    return response.data;
  },

  async updateForcing(id: number, data: {
    tag: string;
    situacao_equipamento: string;
    descricao_equipamento: string;
    area: string;
    observacoes?: string;
  }): Promise<Forcing> {
    const response = await apiService.put<Forcing>(`/forcings/${id}`, data);
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao atualizar forcing');
    }

    return response.data;
  },

  async deleteForcing(id: number): Promise<void> {
    const response = await apiService.delete(`/forcings/${id}`);
    
    if (!response.success) {
      throw new Error(response.message || 'Erro ao excluir forcing');
    }
  },

  async liberarForcing(id: number, observacoes?: string): Promise<Forcing> {
    const response = await apiService.post<Forcing>(`/forcings/${id}/liberar`, {
      observacoes,
    });
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao liberar forcing');
    }

    return response.data;
  },

  async executarForcing(id: number, data: {
    local_execucao: string;
    observacoes_execucao?: string;
  }): Promise<Forcing> {
    const response = await apiService.post<Forcing>(`/forcings/${id}/executar`, data);
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao registrar execução');
    }

    return response.data;
  },

  async solicitarRetirada(id: number, data: {
    descricao_resolucao: string;
    observacoes?: string;
  }): Promise<Forcing> {
    const response = await apiService.post<Forcing>(`/forcings/${id}/solicitar-retirada`, data);
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao solicitar retirada');
    }

    return response.data;
  },

  async retirarForcing(id: number, observacoes?: string): Promise<Forcing> {
    const response = await apiService.post<Forcing>(`/forcings/${id}/retirar`, {
      observacoes,
    });
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao retirar forcing');
    }

    return response.data;
  },

  async getDashboard(): Promise<DashboardStats> {
    const response = await apiService.get<DashboardStats>('/dashboard');
    
    if (!response.success || !response.data) {
      throw new Error(response.message || 'Erro ao buscar estatísticas');
    }

    return response.data;
  },
};

