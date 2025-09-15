@extends('emails.layout')

@section('title', 'Forcing Retirado')
@section('subtitle', 'Um forcing foi retirado e o processo está concluído')

@section('content')
    <h2>✅✅ Forcing Retirado</h2>
    
    <p>Olá!</p>
    
    <p>Um forcing foi retirado no sistema e o processo está <strong>concluído</strong>.</p>
    
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
                    ✅✅ {{ ucfirst($forcing->status) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Data de Retirada:</span>
            <span class="info-value">{{ $forcing->data_retirada->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
    
    <div class="forcing-info">
        <h3>🔧 Retirado por</h3>
        
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span class="info-value">{{ $retiradoPor->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $retiradoPor->email }}</span>
        </div>
        
        @if($forcing->descricao_resolucao)
            <div class="info-row">
                <span class="info-label">Resolução:</span>
                <span class="info-value">{{ $forcing->descricao_resolucao }}</span>
            </div>
        @endif
    </div>
    
    <div class="forcing-info">
        <h3>📊 Resumo do Processo</h3>
        
        <div class="info-row">
            <span class="info-label">Criado por:</span>
            <span class="info-value">{{ $forcing->user->name }}</span>
        </div>
        
        @if($forcing->liberador)
            <div class="info-row">
                <span class="info-label">Liberado por:</span>
                <span class="info-value">{{ $forcing->liberador->name }}</span>
            </div>
        @endif
        
        @if($forcing->executante)
            <div class="info-row">
                <span class="info-label">Executado por:</span>
                <span class="info-value">{{ $forcing->executante->name }}</span>
            </div>
        @endif
        
        @if($forcing->solicitadoRetiradaPor)
            <div class="info-row">
                <span class="info-label">Retirada solicitada por:</span>
                <span class="info-value">{{ $forcing->solicitadoRetiradaPor->name }}</span>
            </div>
        @endif
        
        <div class="info-row">
            <span class="info-label">Duração total:</span>
            <span class="info-value">{{ $forcing->data_forcing->diffForHumans($forcing->data_retirada, true) }}</span>
        </div>
    </div>
    
    <div class="alert alert-success">
        <strong>✅ Processo Concluído:</strong> Este forcing foi retirado com sucesso e o equipamento retornou ao estado normal.
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('forcing.from-email', $forcing->id) }}" class="btn">
            🔍 Ver Forcing na Lista
        </a>
        <a href="{{ route('forcing.show', $forcing->id) }}" class="btn">
            📋 Ver Histórico Completo
        </a>
    </div>
    
    <p><strong>✅ Forcing concluído com sucesso!</strong></p>
    <ul>
        <li>O equipamento foi retirado do estado forçado</li>
        <li>O sistema voltou ao funcionamento normal</li>
        <li>Este processo está agora arquivado</li>
        <li>Nenhuma ação adicional é necessária</li>
    </ul>
@endsection
