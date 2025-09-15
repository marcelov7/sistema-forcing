@extends('emails.layout')

@section('title', 'Nova Solicitação de Forcing')
@section('subtitle', 'Uma nova solicitação de forcing foi feita e precisa de liberação')

@section('content')
    <h2>🆕 Nova Solicitação de Forcing</h2>
    
    <p>Olá, <strong>Liberador(a)</strong>!</p>
    
    <p>Um novo forcing foi criado no sistema e está aguardando sua liberação.</p>
    
    <div class="forcing-info">
        <h3>📋 Detalhes do Forcing</h3>
        
        <div class="info-row">
            <span class="info-label">ID:</span>
            <span class="info-value">#{{ $forcing->id }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">TAG:</span>
            <span class="info-value"><strong>{{ $forcing->tag }}</strong></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Equipamento:</span>
            <span class="info-value">{{ $forcing->descricao_equipamento }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Área:</span>
            <span class="info-value">{{ $forcing->area }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Situação:</span>
            <span class="info-value">{{ $forcing->getSituacaoEquipamentoTexto() }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $forcing->status }}">
                    ⏳ {{ ucfirst($forcing->status) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Data de Criação:</span>
            <span class="info-value">{{ $forcing->data_forcing->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
    
    <div class="forcing-info">
        <h3>👤 Criado por</h3>
        
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span class="info-value">{{ $criador->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $criador->email }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Empresa:</span>
            <span class="info-value">{{ $criador->empresa }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Setor:</span>
            <span class="info-value">{{ $criador->setor }}</span>
        </div>
    </div>
    
    <div class="alert alert-warning">
        <strong>⚠️ Ação Necessária:</strong> Este forcing está aguardando sua liberação para prosseguir no processo.
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('forcing.from-email', $forcing->id) }}" class="btn">
            🔍 Ver Forcing na Lista
        </a>
        <a href="{{ route('forcing.show', $forcing->id) }}" class="btn">
            📋 Ver Detalhes Completos
        </a>
    </div>
    
    <p><strong>Próximos passos:</strong></p>
    <ol>
        <li>Acesse o sistema através do link acima</li>
        <li>Revise os detalhes do forcing</li>
        <li>Libere ou adicione observações conforme necessário</li>
    </ol>
@endsection
