export interface User {
  id: number;
  name: string;
  username: string;
  email: string;
  empresa: string;
  setor: string;
  perfil: 'user' | 'liberador' | 'executante' | 'admin' | 'super_admin';
  unit_id: number;
  unit_name?: string;
}

