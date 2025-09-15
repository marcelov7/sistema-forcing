# 🔒 Restrição de Aprovação por Setor - Implementada

## 🎯 Objetivo

**Implementar restrições baseadas no setor do usuário para aprovações de alterações elétricas**, garantindo que apenas usuários dos setores apropriados possam aprovar como Gerente de Manutenção, Coordenador de Manutenção ou Técnico Especialista.

## 🏗️ Estrutura Implementada

### **1. Modelo User - Novos Métodos de Validação**

#### **`podeAprovarComoGerente()`:**
```php
public function podeAprovarComoGerente()
{
    // Super admin sempre pode
    if ($this->isSuperAdmin()) {
        return true;
    }
    
    // Usuários do setor "Manutenção" podem aprovar como gerente
    return strtolower($this->setor) === 'manutenção' || 
           strtolower($this->setor) === 'manutencao';
}
```

#### **`podeAprovarComoCoordenador()`:**
```php
public function podeAprovarComoCoordenador()
{
    // Super admin sempre pode
    if ($this->isSuperAdmin()) {
        return true;
    }
    
    // Usuários do setor "Manutenção" podem aprovar como coordenador
    return strtolower($this->setor) === 'manutenção' || 
           strtolower($this->setor) === 'manutencao';
}
```

#### **`podeAprovarComoTecnico()`:**
```php
public function podeAprovarComoTecnico()
{
    // Super admin sempre pode
    if ($this->isSuperAdmin()) {
        return true;
    }
    
    // Usuários dos setores técnicos podem aprovar como técnico
    $setoresTecnicos = ['automação', 'automacao', 'elétrica', 'eletrica', 'instrumentação', 'instrumentacao', 'técnico', 'tecnico'];
    return in_array(strtolower($this->setor), $setoresTecnicos);
}
```

#### **`tiposAprovacaoPermitidos()`:**
```php
public function tiposAprovacaoPermitidos()
{
    $tipos = [];
    
    if ($this->podeAprovarComoGerente()) {
        $tipos[] = 'gerente';
    }
    
    if ($this->podeAprovarComoCoordenador()) {
        $tipos[] = 'coordenador';
    }
    
    if ($this->podeAprovarComoTecnico()) {
        $tipos[] = 'tecnico';
    }
    
    return $tipos;
}
```

### **2. Controller - Validação de Setor**

#### **Validação no Método `aprovar()`:**
```php
public function aprovar(Request $request, AlteracaoEletrica $alteracao)
{
    $user = Auth::user();

    // Verifica se o usuário pode aprovar como este tipo baseado no setor
    switch ($request->tipo_aprovador) {
        case 'gerente':
            if (!$user->podeAprovarComoGerente()) {
                return redirect()->route('alteracoes.show', $alteracao)
                    ->with('error', 'Você não tem permissão para aprovar como Gerente de Manutenção. Apenas usuários do setor "Manutenção" podem realizar esta aprovação.');
            }
            break;
        case 'coordenador':
            if (!$user->podeAprovarComoCoordenador()) {
                return redirect()->route('alteracoes.show', $alteracao)
                    ->with('error', 'Você não tem permissão para aprovar como Coordenador de Manutenção. Apenas usuários do setor "Manutenção" podem realizar esta aprovação.');
            }
            break;
        case 'tecnico':
            if (!$user->podeAprovarComoTecnico()) {
                return redirect()->route('alteracoes.show', $alteracao)
                    ->with('error', 'Você não tem permissão para aprovar como Técnico Especialista. Apenas usuários dos setores técnicos podem realizar esta aprovação.');
            }
            break;
    }

    // ... resto da lógica de aprovação
}
```

### **3. Interface - Botões Dinâmicos**

#### **Botões de Aprovação Condicionais:**
```blade
@if($alteracao->podeSerAprovada())
    @php
        $tiposPermitidos = auth()->user()->tiposAprovacaoPermitidos();
    @endphp
    
    @if(!empty($tiposPermitidos))
        <div class="mb-2">
            <small class="text-muted d-block mb-2">
                <i class="fas fa-info-circle"></i> 
                Seu setor: <strong>{{ auth()->user()->setor }}</strong>
            </small>
            
            @foreach($tiposPermitidos as $tipo)
                @if($alteracao->podeSerAprovadaPor($tipo))
                    <button class="btn btn-success btn-sm w-100 mb-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#aprovarModal"
                            data-tipo="{{ $tipo }}">
                        <i class="fas fa-check"></i> 
                        @if($tipo === 'gerente')
                            Aprovar como Gerente
                        @elseif($tipo === 'coordenador')
                            Aprovar como Coordenador
                        @elseif($tipo === 'tecnico')
                            Aprovar como Técnico
                        @endif
                    </button>
                @endif
            @endforeach
        </div>
    @else
        <div class="alert alert-warning p-2 mb-2">
            <small>
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Sem permissão:</strong><br>
                Seu setor ({{ auth()->user()->setor }}) não tem permissão para aprovar alterações.
            </small>
        </div>
    @endif
@endif
```

#### **Modal de Aprovação Simplificado:**
```blade
<div class="modal fade" id="aprovarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aprovar Alteração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('alteracoes.aprovar', $alteracao) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo_aprovador_display" class="form-label">Tipo de Aprovador</label>
                        <input type="text" class="form-control" id="tipo_aprovador_display" readonly>
                        <input type="hidden" id="tipo_aprovador" name="tipo_aprovador">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Apenas usuários do setor apropriado podem realizar esta aprovação.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="nome_aprovador" class="form-label">Nome do Aprovador</label>
                        <input type="text" class="form-control" id="nome_aprovador" name="nome_aprovador" 
                               value="{{ auth()->user()->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Aprovação</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

#### **JavaScript para Capturar Tipo:**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Captura o tipo quando o botão é clicado
    document.querySelectorAll('[data-bs-target="#aprovarModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            const tipoDisplay = document.getElementById('tipo_aprovador_display');
            const tipoHidden = document.getElementById('tipo_aprovador');
            
            let tipoTexto = '';
            switch(tipo) {
                case 'gerente':
                    tipoTexto = 'Gerente de Manutenção';
                    break;
                case 'coordenador':
                    tipoTexto = 'Coordenador de Manutenção';
                    break;
                case 'tecnico':
                    tipoTexto = 'Técnico Especialista Automação';
                    break;
            }
            
            tipoDisplay.value = tipoTexto;
            tipoHidden.value = tipo;
        });
    });
});
```

## 🔐 Regras de Acesso por Setor

### **1. Gerente de Manutenção:**
- **Setor Permitido**: "Manutenção" ou "Manutencao"
- **Acesso**: Super Admin + Usuários do setor Manutenção
- **Função**: Aprovação de nível gerencial

### **2. Coordenador de Manutenção:**
- **Setor Permitido**: "Manutenção" ou "Manutencao"
- **Acesso**: Super Admin + Usuários do setor Manutenção
- **Função**: Aprovação de nível coordenacional

### **3. Técnico Especialista:**
- **Setores Permitidos**: 
  - "Automação" / "Automacao"
  - "Elétrica" / "Eletrica"
  - "Instrumentação" / "Instrumentacao"
  - "Técnico" / "Tecnico"
- **Acesso**: Super Admin + Usuários dos setores técnicos
- **Função**: Aprovação técnica especializada

## 🎯 Benefícios da Implementação

### **1. Segurança por Setor:**
- ✅ **Controle granular** baseado no setor do usuário
- ✅ **Prevenção de aprovações inadequadas** por usuários de outros setores
- ✅ **Validação no backend** independente do frontend

### **2. Interface Inteligente:**
- ✅ **Botões dinâmicos** mostram apenas opções permitidas
- ✅ **Feedback visual** sobre setor do usuário
- ✅ **Modal simplificado** sem dropdowns confusos

### **3. Experiência do Usuário:**
- ✅ **Clareza sobre permissões** com mensagens explicativas
- ✅ **Processo simplificado** com botões específicos
- ✅ **Feedback imediato** sobre restrições

### **4. Flexibilidade:**
- ✅ **Super Admin sempre pode** (bypass de segurança)
- ✅ **Múltiplos setores técnicos** para técnico especialista
- ✅ **Case insensitive** para nomes de setor

## 📊 Cenários de Uso

### **1. Usuário do Setor "Manutenção":**
- ✅ **Pode aprovar como**: Gerente + Coordenador
- ✅ **Não pode aprovar como**: Técnico (a menos que seja Super Admin)
- ✅ **Botões mostrados**: "Aprovar como Gerente" + "Aprovar como Coordenador"

### **2. Usuário do Setor "Elétrica":**
- ✅ **Pode aprovar como**: Técnico Especialista
- ✅ **Não pode aprovar como**: Gerente ou Coordenador
- ✅ **Botões mostrados**: "Aprovar como Técnico"

### **3. Usuário do Setor "RH":**
- ❌ **Não pode aprovar como**: Nenhum tipo
- ❌ **Botões mostrados**: Nenhum (apenas mensagem de sem permissão)

### **4. Super Admin:**
- ✅ **Pode aprovar como**: Todos os tipos
- ✅ **Botões mostrados**: Todos os tipos disponíveis
- ✅ **Bypass**: Todas as restrições de setor

## 🛡️ Validações de Segurança

### **1. Frontend (Interface):**
- ✅ **Botões condicionais** baseados no setor
- ✅ **Feedback visual** sobre permissões
- ✅ **Modal pré-configurado** com tipo correto

### **2. Backend (Controller):**
- ✅ **Validação rigorosa** antes da aprovação
- ✅ **Mensagens de erro específicas** por tipo de aprovação
- ✅ **Prevenção de bypass** via manipulação de formulário

### **3. Modelo (Lógica de Negócio):**
- ✅ **Métodos dedicados** para cada tipo de validação
- ✅ **Case insensitive** para nomes de setor
- ✅ **Flexibilidade** para múltiplos setores técnicos

## ✅ Status da Implementação

**✅ CONCLUÍDO** - Restrições por setor implementadas com segurança total

### **Verificado:**
- ✅ Validação de setor para Gerente de Manutenção
- ✅ Validação de setor para Coordenador de Manutenção  
- ✅ Validação de setor para Técnico Especialista
- ✅ Interface dinâmica baseada em permissões
- ✅ Validação no backend independente do frontend
- ✅ Super Admin com bypass de todas as restrições
- ✅ Mensagens de erro específicas e informativas

### **Funcionalidades:**
- ✅ **Controle granular** por setor do usuário
- ✅ **Interface inteligente** com botões condicionais
- ✅ **Validação dupla** (frontend + backend)
- ✅ **Flexibilidade** para múltiplos setores técnicos
- ✅ **Segurança total** contra bypass de permissões

O sistema agora **garante que apenas usuários dos setores apropriados possam aprovar alterações elétricas** conforme suas responsabilidades organizacionais! 🔒✅

