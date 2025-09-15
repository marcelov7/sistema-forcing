@extends('layouts.app')

@section('title', 'Termo de Responsabilidade - Forcing')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> TERMO DE RESPONSABILIDADE
                    </h4>
                    <p class="mb-0 mt-2">Solicitação de Forcing - Sistema de Controle</p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Identificação do Solicitante -->
                    <div class="alert alert-info border-0 mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user"></i> Solicitante:</h6>
                                <p class="mb-1"><strong>{{ auth()->user()->name }}</strong></p>
                                <p class="mb-1">{{ auth()->user()->empresa }} - {{ auth()->user()->setor }}</p>
                                <p class="mb-0">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-clock"></i> Data/Hora:</h6>
                                <p class="mb-1">{{ now()->format('d/m/Y H:i:s') }}</p>
                                <h6><i class="fas fa-id-badge"></i> Perfil:</h6>
                                <span class="badge bg-secondary">{{ ucfirst(auth()->user()->perfil) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Conteúdo do Termo -->
                    <div class="terms-content">
                        <h5 class="text-danger mb-3">
                            <i class="fas fa-balance-scale"></i> TERMO DE RESPONSABILIDADE - SMC-MAN-PR-014
                        </h5>
                        
                        <div class="alert alert-warning mb-3">
                            <strong><i class="fas fa-info-circle"></i> PROCEDIMENTO:</strong> 
                            Este termo segue as diretrizes do procedimento interno 
                            <strong>SMC-MAN-PR-014 - CONTROLE DE FORCING V.4</strong> para garantir 
                            a integridade física dos equipamentos e pessoas.
                        </div>
                        
                        <p class="text-justify mb-3">
                            Eu, <strong>{{ auth()->user()->name }}</strong>, funcionário(a) da empresa 
                            <strong>{{ auth()->user()->empresa }}</strong>, lotado(a) no setor 
                            <strong>{{ auth()->user()->setor }}</strong>, na qualidade de 
                            <strong>SOLICITANTE</strong> conforme procedimento SMC-MAN-PR-014, 
                            declaro estar ciente das seguintes responsabilidades ao solicitar um 
                            <strong>FORCING</strong> no sistema:
                        </p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-check-circle"></i> RESPONSABILIDADES DO SOLICITANTE:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Análise de Segurança:</strong> Avaliar riscos pessoais, patrimoniais e de processo
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Justificativa Técnica:</strong> Apresentar motivos justificados para a solicitação
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Informações Precisas:</strong> Fornecer dados técnicos corretos e completos
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Acompanhamento:</strong> Monitorar o processo até a retirada do forcing
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-arrow-right text-success me-2"></i>
                                        <strong>Comunicação:</strong> Informar ao operador responsável antes da execução
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> OBRIGAÇÕES E RESTRIÇÕES:</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-times text-danger me-2"></i>
                                        <strong>Proibição de Execução:</strong> Apenas técnicos de manutenção elétrica podem executar
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-times text-danger me-2"></i>
                                        <strong>Área de Competência:</strong> Solicitar apenas dentro da sua área de responsabilidade
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-times text-danger me-2"></i>
                                        <strong>Autorização Obrigatória:</strong> Forcing deve ser aprovado pelo autorizante competente
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-times text-danger me-2"></i>
                                        <strong>Prazo de Validade:</strong> Definir prazo para retirada do forcing
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-times text-danger me-2"></i>
                                        <strong>Registro SAP:</strong> Abrir nota M2 no SAP conforme procedimento
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mt-4">
                            <h6><i class="fas fa-info-circle"></i> TIPOS DE FORCING CONFORME SMC-MAN-PR-014:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>📊 FORCING DE PROCESSO:</strong>
                                    <small class="d-block text-muted">Intertravamentos de processo (pressões, vazões, temperaturas, sensores, velocidade)</small>
                                </div>
                                <div class="col-md-6">
                                    <strong>🔧 FORCING DE MANUTENÇÃO:</strong>
                                    <small class="d-block text-muted">Intertravamentos de equipamentos (proteções, relés, botoeiras, chaves)</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 mt-4">
                            <h6><i class="fas fa-route"></i> FLUXO OBRIGATÓRIO SMC-MAN-PR-014:</h6>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary me-2 mb-1">1. Nota SAP M2</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-primary me-2 mb-1">2. Sistema Forcing</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-warning me-2 mb-1">3. Autorização</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-info me-2 mb-1">4. Execução Técnica</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-success mb-1">5. Retirada</span>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <strong>IMPORTANTE:</strong> E-mails automáticos serão enviados para todos os envolvidos em cada etapa.
                            </small>
                        </div>

                        <div class="alert alert-danger border-0 mt-4">
                            <h6><i class="fas fa-shield-alt"></i> COMPROMISSOS DE SEGURANÇA - SMC-MAN-PR-014:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Certeza absoluta</strong> de ausência de danos pessoais/patrimoniais</li>
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Comunicação prévia</strong> ao operador responsável</li>
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Consulta hierárquica</strong> em caso de dúvidas</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled mb-0">
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Acompanhamento contínuo</strong> até resolução do problema</li>
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Registro detalhado</strong> de todas as alterações</li>
                                        <li><i class="fas fa-check text-success me-2"></i> <strong>Retirada imediata</strong> após resolução</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mt-4">
                            <h6><i class="fas fa-users"></i> HIERARQUIA DE AUTORIZAÇÃO:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>🏭 FORCING DE PRODUÇÃO:</strong>
                                    <ul class="list-unstyled small">
                                        <li>• Gerente Industrial</li>
                                        <li>• Coordenador de Produção</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>🔧 FORCING DE MANUTENÇÃO:</strong>
                                    <ul class="list-unstyled small">
                                        <li>• Gerente de Manutenção</li>
                                        <li>• Coordenadores de Manutenção</li>
                                        <li>• Coordenador de Planejamento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox de Aceite -->
                    <div class="mt-4 p-3 border rounded bg-light">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                            <label class="form-check-label fw-bold" for="acceptTerms">
                                <i class="fas fa-hand-point-right text-primary me-2"></i>
                                Li, COMPREENDI e ACEITO todos os termos e responsabilidades descritos acima conforme 
                                <strong>SMC-MAN-PR-014 - CONTROLE DE FORCING V.4</strong>. Declaro estar ciente 
                                de que sou totalmente responsável pelas informações fornecidas e pelas consequências 
                                da solicitação de forcing, assumindo todos os riscos pessoais, patrimoniais e de processo.
                            </label>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-database"></i>
                                <strong>Registro:</strong> Este aceite será registrado no sistema com data, 
                                horário, IP e informações do navegador para fins de auditoria e controle.
                            </small>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="{{ route('forcing.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" id="proceedBtn" class="btn btn-success w-100" disabled>
                                <i class="fas fa-check"></i> Aceitar e Continuar
                            </button>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            <i class="fas fa-lock"></i>
                            Sistema de Forcing - {{ config('app.name') }} | 
                            Termo gerado em {{ now()->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.terms-content {
    font-size: 0.95rem;
    line-height: 1.6;
}

.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.text-justify {
    text-align: justify;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.alert {
    border-radius: 10px;
}

.badge {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .fas.fa-arrow-right {
        display: none;
    }
    
    .d-flex.align-items-center.flex-wrap .badge {
        margin-bottom: 0.5rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const acceptCheckbox = document.getElementById('acceptTerms');
    const proceedButton = document.getElementById('proceedBtn');
    
    // Habilitar/desabilitar botão baseado no checkbox
    acceptCheckbox.addEventListener('change', function() {
        proceedButton.disabled = !this.checked;
        
        if (this.checked) {
            proceedButton.classList.remove('btn-outline-success');
            proceedButton.classList.add('btn-success');
        } else {
            proceedButton.classList.remove('btn-success');
            proceedButton.classList.add('btn-outline-success');
        }
    });
    
    // Ação do botão de continuar
    proceedButton.addEventListener('click', function() {
        if (acceptCheckbox.checked) {
            // Mostrar loading
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecionando...';
            this.disabled = true;
            
            // Redirecionar para o formulário
            setTimeout(function() {
                window.location.href = '{{ route("forcing.create") }}?terms_accepted=1';
            }, 1500);
        }
    });
    
    // Verificar se já foi aceito (opcional - remover se quiser sempre mostrar)
    const previousAcceptance = localStorage.getItem('forcing_terms_accepted');
    if (previousAcceptance) {
        const acceptance = JSON.parse(previousAcceptance);
        const acceptedDate = new Date(acceptance.accepted_at);
        const now = new Date();
        const hoursDiff = (now - acceptedDate) / (1000 * 60 * 60);
        
        // Se foi aceito há menos de 24 horas, mostrar informação
        if (hoursDiff < 24) {
            const alert = document.createElement('div');
            alert.className = 'alert alert-info';
            alert.innerHTML = `
                <i class="fas fa-info-circle"></i>
                <strong>Informação:</strong> Você já aceitou este termo hoje às ${acceptedDate.toLocaleTimeString()}.
                É necessário aceitar novamente para cada nova solicitação.
            `;
            document.querySelector('.terms-content').insertBefore(alert, document.querySelector('.terms-content').firstChild);
        }
    }
    
    // Adicionar animação ao scroll
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
@endsection
