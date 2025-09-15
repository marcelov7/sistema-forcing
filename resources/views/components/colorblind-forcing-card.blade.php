{{-- 
    Componente de Acessibilidade para Daltonicos
    Sistema de Forcing - Devaxis
--}}

@php
    $statusIcons = [
        'aguardando_liberacao' => ['icon' => '⏳', 'color' => 'warning', 'label' => 'Aguardando Liberação'],
        'liberado' => ['icon' => '✅', 'color' => 'success', 'label' => 'Liberado'],
        'executado' => ['icon' => '🔄', 'color' => 'info', 'label' => 'Executado'],
        'solicitacao_retirada' => ['icon' => '📤', 'color' => 'warning', 'label' => 'Solicitação de Retirada'],
        'retirado' => ['icon' => '✓', 'color' => 'primary', 'label' => 'Retirado']
    ];
@endphp

{{-- Badge de Status Acessível --}}
@if(isset($status) && isset($statusIcons[$status]))
    @php $statusInfo = $statusIcons[$status]; @endphp
    <span class="badge bg-{{ $statusInfo['color'] }} status-{{ $status }}" 
          data-status="{{ $status }}"
          aria-label="{{ $statusInfo['label'] }}"
          title="{{ $statusInfo['label'] }}">
        <span class="status-icon me-1" aria-hidden="true">{{ $statusInfo['icon'] }}</span>
        {{ $statusInfo['label'] }}
    </span>
@endif

{{-- Card de Forcing Acessível --}}
<div class="card forcing-status-card status-{{ $forcing->status ?? 'unknown' }} mb-3" 
     data-status="{{ $forcing->status ?? 'unknown' }}"
     role="article"
     aria-labelledby="forcing-title-{{ $forcing->id ?? 'unknown' }}">
    
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0" id="forcing-title-{{ $forcing->id ?? 'unknown' }}">
            @if(isset($forcing->status) && isset($statusIcons[$forcing->status]))
                <span class="status-icon me-2" aria-hidden="true">{{ $statusIcons[$forcing->status]['icon'] }}</span>
            @endif
            {{ $forcing->titulo ?? 'Sem título' }}
        </h5>
        
        <div class="status-indicators">
            {{-- Badge principal --}}
            @if(isset($forcing->status))
                @include('components.colorblind-status', ['status' => $forcing->status])
            @endif
            
            {{-- Indicador de prioridade --}}
            @if(isset($forcing->prioridade))
                @php
                    $prioridadeIcons = [
                        'baixa' => ['icon' => '🟢', 'color' => 'success'],
                        'media' => ['icon' => '🟡', 'color' => 'warning'], 
                        'alta' => ['icon' => '🔴', 'color' => 'danger'],
                        'critica' => ['icon' => '🚨', 'color' => 'danger']
                    ];
                    $prioInfo = $prioridadeIcons[$forcing->prioridade] ?? ['icon' => '⚪', 'color' => 'secondary'];
                @endphp
                <span class="badge bg-{{ $prioInfo['color'] }} ms-2" 
                      title="Prioridade: {{ ucfirst($forcing->prioridade) }}"
                      aria-label="Prioridade {{ $forcing->prioridade }}">
                    <span aria-hidden="true">{{ $prioInfo['icon'] }}</span>
                    {{ ucfirst($forcing->prioridade) }}
                </span>
            @endif
        </div>
    </div>
    
    <div class="card-body">
        {{-- Informações do forcing --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <small class="text-muted d-block">Sistema</small>
                <strong>{{ $forcing->sistema ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">Subsistema</small>
                <strong>{{ $forcing->subsistema ?? 'N/A' }}</strong>
            </div>
        </div>
        
        {{-- Timeline visual --}}
        <div class="forcing-timeline">
            <div class="timeline-item {{ $forcing->created_at ? 'completed' : '' }}">
                <span class="timeline-icon" aria-hidden="true">📝</span>
                <span class="timeline-label">Criado</span>
                <small class="timeline-date">{{ $forcing->created_at ? $forcing->created_at->format('d/m/Y H:i') : 'N/A' }}</small>
            </div>
            
            <div class="timeline-item {{ in_array($forcing->status, ['liberado', 'executado', 'retirado']) ? 'completed' : '' }}">
                <span class="timeline-icon" aria-hidden="true">✅</span>
                <span class="timeline-label">Liberado</span>
                <small class="timeline-date">{{ $forcing->data_liberacao ? $forcing->data_liberacao->format('d/m/Y H:i') : '-' }}</small>
            </div>
            
            <div class="timeline-item {{ in_array($forcing->status, ['executado', 'retirado']) ? 'completed' : '' }}">
                <span class="timeline-icon" aria-hidden="true">🔄</span>
                <span class="timeline-label">Executado</span>
                <small class="timeline-date">{{ $forcing->data_execucao ? $forcing->data_execucao->format('d/m/Y H:i') : '-' }}</small>
            </div>
            
            <div class="timeline-item {{ $forcing->status === 'retirado' ? 'completed' : '' }}">
                <span class="timeline-icon" aria-hidden="true">✓</span>
                <span class="timeline-label">Retirado</span>
                <small class="timeline-date">{{ $forcing->data_retirada ? $forcing->data_retirada->format('d/m/Y H:i') : '-' }}</small>
            </div>
        </div>
        
        {{-- Descrição --}}
        @if(isset($forcing->descricao) && $forcing->descricao)
            <div class="mt-3">
                <small class="text-muted d-block">Descrição</small>
                <p class="mb-0">{{ $forcing->descricao }}</p>
            </div>
        @endif
        
        {{-- Ações --}}
        <div class="card-actions mt-3 d-flex gap-2 flex-wrap">
            @if($forcing->status === 'aguardando_liberacao' && Auth::user()->isLiberador())
                <form method="POST" action="{{ route('forcing.liberar', $forcing->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" 
                            class="btn btn-success btn-sm" 
                            aria-label="Liberar forcing"
                            onclick="return confirm('Confirma a liberação deste forcing?')">
                        <span aria-hidden="true">✅</span> Liberar
                    </button>
                </form>
            @endif
            
            @if($forcing->status === 'liberado' && Auth::user()->isExecutante())
                <form method="POST" action="{{ route('forcing.executar', $forcing->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" 
                            class="btn btn-info btn-sm" 
                            aria-label="Executar forcing"
                            onclick="return confirm('Confirma a execução deste forcing?')">
                        <span aria-hidden="true">🔄</span> Executar
                    </button>
                </form>
            @endif
            
            @if(in_array($forcing->status, ['executado', 'solicitacao_retirada']) && Auth::user()->isExecutante())
                <form method="POST" action="{{ route('forcing.retirar', $forcing->id) }}" class="d-inline">
                    @csrf
                    <button type="submit" 
                            class="btn btn-primary btn-sm" 
                            aria-label="Retirar forcing"
                            onclick="return confirm('Confirma a retirada deste forcing?')">
                        <span aria-hidden="true">✓</span> Retirar
                    </button>
                </form>
            @endif
            
            <a href="{{ route('forcing.show', $forcing->id) }}" 
               class="btn btn-outline-secondary btn-sm" 
               aria-label="Ver detalhes do forcing">
                <span aria-hidden="true">👁️</span> Detalhes
            </a>
        </div>
    </div>
</div>

{{-- Estilos CSS para timeline --}}
@push('styles')
<style>
.forcing-timeline {
    display: flex;
    justify-content: space-between;
    margin: 20px 0;
    padding: 10px 0;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.timeline-item {
    flex: 1;
    text-align: center;
    position: relative;
    opacity: 0.5;
    transition: opacity 0.3s ease;
}

.timeline-item.completed {
    opacity: 1;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    right: -50%;
    width: 100%;
    height: 2px;
    background: #e2e8f0;
    z-index: -1;
}

.timeline-item.completed:not(:last-child)::after {
    background: #10b981;
}

.timeline-icon {
    display: block;
    font-size: 18px;
    margin-bottom: 5px;
}

.timeline-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
}

.timeline-date {
    display: block;
    font-size: 10px;
    color: #6b7280;
}

/* Melhorias para modo daltonico */
.colorblind-mode .timeline-item.completed .timeline-icon {
    background: #10b981;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    line-height: 30px;
    margin: 0 auto 5px;
}

.colorblind-mode .forcing-timeline {
    background: linear-gradient(to right, #f3f4f6 0%, white 50%, #f3f4f6 100%);
    border-radius: 8px;
    padding: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .forcing-timeline {
        flex-direction: column;
        gap: 10px;
    }
    
    .timeline-item:not(:last-child)::after {
        display: none;
    }
    
    .timeline-item {
        text-align: left;
        padding-left: 40px;
        position: relative;
    }
    
    .timeline-item .timeline-icon {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endpush
