export interface Forcing {
  id: number;
  tag: string;
  situacao_equipamento: 'desativado' | 'ativacao_futura' | 'em_atividade';
  situacao_equipamento_texto: string;
  descricao_equipamento: string;
  area: string;
  observacoes?: string;
  status: 'pendente' | 'liberado' | 'forcado' | 'solicitacao_retirada' | 'retirado';
  status_texto: string;
  status_cor: string;
  status_execucao: 'pendente' | 'executado';
  status_execucao_texto: string;
  local_execucao?: 'supervisorio' | 'plc' | 'local' | 'campo';
  local_execucao_texto?: string;
  
  // Datas
  data_forcing?: string;
  data_forcing_formatada?: string;
  data_liberacao?: string;
  data_liberacao_formatada?: string;
  data_execucao?: string;
  data_execucao_formatada?: string;
  data_solicitacao_retirada?: string;
  data_solicitacao_retirada_formatada?: string;
  data_retirada?: string;
  data_retirada_formatada?: string;
  
  // Observações
  observacoes_liberacao?: string;
  observacoes_execucao?: string;
  descricao_resolucao?: string;
  
  // Relacionamentos
  user: {
    id: number;
    name: string;
    username: string;
    empresa: string;
    setor: string;
  };
  
  liberador?: {
    id: number;
    name: string;
    username: string;
  };
  
  executante?: {
    id: number;
    name: string;
    username: string;
  };
  
  retirado_por?: {
    id: number;
    name: string;
    username: string;
  };
  
  solicitado_retirada_por?: {
    id: number;
    name: string;
    username: string;
  };
  
  unit?: {
    id: number;
    name: string;
    code: string;
  };
  
  // Timestamps
  created_at?: string;
  updated_at?: string;
  
  // Métodos de status para facilitar no frontend
  is_pendente: boolean;
  is_liberado: boolean;
  is_forcado: boolean;
  is_solicitacao_retirada: boolean;
  is_retirado: boolean;
  is_executado: boolean;
}

export interface ForcingFilters {
  status?: string;
  area?: string;
  busca?: string;
  situacao?: string;
  criador?: string;
  data_inicio?: string;
  data_fim?: string;
}

export interface DashboardStats {
  total: number;
  pendentes: number;
  liberados: number;
  forcados: number;
  solicitacao_retirada: number;
  retirados: number;
  executados: number;
}

