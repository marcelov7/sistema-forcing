@extends('emails.layout')

@section('title', 'Forcing Executado')
@section('subtitle', 'Um forcing foi executado com sucesso')

@section('content')
    <h2>🔧 Forcing Executado</h2>
    
    <p>Olá!</p>
    
    <p>Um forcing foi executado no sistema e o equipamento está agora forçado.</p>
    
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
                    🔧 {{ ucfirst($forcing->status) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Data de Execução:</span>
            <span class="info-value">{{ $forcing->data_execucao->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
    
    <div class="forcing-info">
        <h3>🔧 Detalhes da Execução</h3>
        
        <div class="info-row">
            <span class="info-label">Executado por:</span>
            <span class="info-value">{{ $executante->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $executante->email }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Local de Execução:</span>
            <span class="info-value">{{ $forcing->getLocalExecucaoTexto() }}</span>
        </div>
        
        @if($forcing->observacoes_execucao)
            <div class="info-row">
                <span class="info-label">Observações:</span>
                <span class="info-value">{{ $forcing->observacoes_execucao }}</span>
            </div>
        @endif
    </div>
    
    <div class="alert alert-warning">
        <strong>⚠️ Equipamento Forçado:</strong> O equipamento está agora em estado forçado. Monitore o sistema e proceda com a retirada quando apropriado.
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('forcing.from-email', $forcing->id) }}" class="btn">
            🔍 Ver Forcing na Lista
        </a>
        <a href="{{ route('forcing.show', $forcing->id) }}" class="btn">
            📋 Ver Detalhes Completos
        </a>
    </div>
    
    <p><strong>Informação importante:</strong></p>
    <ul>
        <li>O equipamento está agora em estado forçado</li>
        <li>Monitore o comportamento do sistema</li>
        <li>Solicite a retirada quando necessário</li>
        <li>A retirada deverá ser executada por um executante</li>
    </ul>
@endsection
