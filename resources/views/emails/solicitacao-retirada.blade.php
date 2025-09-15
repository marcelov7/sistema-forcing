@extends('emails.layout')

@section('title', 'Solicitação de Retirada')
@section('subtitle', 'Uma solicitação de retirada foi registrada')

@section('content')
    <h2>🔔 Solicitação de Retirada</h2>
    
    <p>Olá, <strong>Executante(s)</strong>!</p>
    
    <p>Uma solicitação de retirada foi registrada no sistema e precisa da sua atenção.</p>
    
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
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $forcing->status }}">
                    🔔 {{ ucfirst(str_replace('_', ' ', $forcing->status)) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Data da Solicitação:</span>
            <span class="info-value">{{ $forcing->data_solicitacao_retirada->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
    
    <div class="forcing-info">
        <h3>👤 Solicitado por</h3>
        
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span class="info-value">{{ $solicitante->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $solicitante->email }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Empresa:</span>
            <span class="info-value">{{ $solicitante->empresa }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Setor:</span>
            <span class="info-value">{{ $solicitante->setor }}</span>
        </div>
    </div>
    
    <div class="alert alert-info">
        <strong>🔔 Ação Necessária:</strong> Este forcing tem uma solicitação de retirada pendente. Proceda com a retirada o mais breve possível.
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('forcing.from-email', $forcing->id) }}" class="btn">
            🔧 Ver Forcing e Retirar
        </a>
        <a href="{{ route('forcing.show', $forcing->id) }}" class="btn">
            📋 Ver Detalhes Completos
        </a>
    </div>
    
    <p><strong>Próximos passos:</strong></p>
    <ol>
        <li>Acesse o sistema através do link acima</li>
        <li>Revise os detalhes do forcing</li>
        <li>Proceda com a retirada do equipamento</li>
        <li>Adicione a descrição da resolução do problema</li>
    </ol>
    
    <p><strong>⚠️ Importante:</strong> Apenas executantes podem proceder com a retirada do forcing.</p>
@endsection
