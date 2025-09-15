@extends('emails.layout')

@section('title', 'Forcing Liberado')
@section('subtitle', 'Um forcing foi liberado e está pronto para execução')

@section('content')
    <h2>✅ Forcing Liberado</h2>
    
    <p>Olá, <strong>Executante(s)</strong>!</p>
    
    <p>Um forcing foi liberado no sistema e está pronto para execução.</p>
    
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
                    ✅ {{ ucfirst($forcing->status) }}
                </span>
            </span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Data de Liberação:</span>
            <span class="info-value">{{ $forcing->data_liberacao->format('d/m/Y H:i:s') }}</span>
        </div>
    </div>
    
    <div class="forcing-info">
        <h3>👨‍💼 Liberado por</h3>
        
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span class="info-value">{{ $liberador->name }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $liberador->email }}</span>
        </div>
        
        @if($forcing->observacoes)
            <div class="info-row">
                <span class="info-label">Observações:</span>
                <span class="info-value">{{ $forcing->observacoes }}</span>
            </div>
        @endif
    </div>
    
    <div class="alert alert-success">
        <strong>✅ Forcing Liberado:</strong> Este forcing está pronto para execução. Proceda com o forçamento do equipamento.
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('forcing.from-email', $forcing->id) }}" class="btn">
            🔧 Ver Forcing e Executar
        </a>
        <a href="{{ route('forcing.show', $forcing->id) }}" class="btn">
            📋 Ver Detalhes Completos
        </a>
    </div>
    
    <p><strong>Próximos passos:</strong></p>
    <ol>
        <li>Acesse o sistema através do link acima</li>
        <li>Registre a execução do forcing</li>
        <li>Informe o local de execução (Supervisório, PLC ou Local)</li>
        <li>Adicione observações se necessário</li>
    </ol>
@endsection
