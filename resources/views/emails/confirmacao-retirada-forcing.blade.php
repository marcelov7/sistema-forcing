<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forcing Finalizado</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #6c757d;
            min-width: 120px;
        }
        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
        }
        .timeline {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .timeline h3 {
            color: #28a745;
            margin-top: 0;
            text-align: center;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px 0;
        }
        .timeline-icon {
            width: 12px;
            height: 12px;
            background: #28a745;
            border-radius: 50%;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .timeline-content {
            flex: 1;
        }
        .timeline-date {
            font-size: 12px;
            color: #6c757d;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
            font-size: 14px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Forcing Finalizado</h1>
            <div class="status-badge">CICLO COMPLETO</div>
        </div>

        <div class="success-message">
            Seu forcing foi retirado com sucesso! O ciclo foi finalizado completamente.
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">TAG:</span>
                <span class="info-value"><strong>{{ $forcing->tag }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Área:</span>
                <span class="info-value">{{ $forcing->area }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Equipamento:</span>
                <span class="info-value">{{ $forcing->descricao_equipamento }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Situação:</span>
                <span class="info-value">
                    @if($forcing->situacao_equipamento === 'desativado')
                        🔴 Desativado
                    @elseif($forcing->situacao_equipamento === 'ativacao_futura')
                        🟡 Ativação Futura
                    @else
                        🟢 Em Atividade
                    @endif
                </span>
            </div>
        </div>

        <div class="timeline">
            <h3>📋 Resumo do Ciclo</h3>
            
            <div class="timeline-item">
                <div class="timeline-icon"></div>
                <div class="timeline-content">
                    <strong>Solicitação Criada</strong><br>
                    <span>Por: {{ $solicitante->name }}</span><br>
                    <span class="timeline-date">{{ $forcing->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
            </div>

            @if($liberador)
            <div class="timeline-item">
                <div class="timeline-icon"></div>
                <div class="timeline-content">
                    <strong>Forcing Liberado</strong><br>
                    <span>Por: {{ $liberador->name }}</span><br>
                    @if($forcing->data_liberacao)
                        <span class="timeline-date">{{ $forcing->data_liberacao->format('d/m/Y H:i:s') }}</span>
                    @endif
                </div>
            </div>
            @endif

            @if($executante)
            <div class="timeline-item">
                <div class="timeline-icon"></div>
                <div class="timeline-content">
                    <strong>Forcing Executado</strong><br>
                    <span>Por: {{ $executante->name }}</span><br>
                    @if($forcing->data_execucao)
                        <span class="timeline-date">{{ $forcing->data_execucao->format('d/m/Y H:i:s') }}</span>
                    @endif
                </div>
            </div>
            @endif

            <div class="timeline-item">
                <div class="timeline-icon"></div>
                <div class="timeline-content">
                    <strong>✅ Forcing Retirado</strong><br>
                    <span>Por: {{ $retiradoPor->name }}</span><br>
                    @if($forcing->data_retirada)
                        <span class="timeline-date">{{ $forcing->data_retirada->format('d/m/Y H:i:s') }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if($forcing->observacoes_retirada)
        <div class="info-section">
            <h4 style="margin-top: 0; color: #28a745;">📝 Observações da Retirada:</h4>
            <p style="margin: 0;">{{ $forcing->observacoes_retirada }}</p>
        </div>
        @endif

        @if($forcing->descricao_resolucao)
        <div class="info-section">
            <h4 style="margin-top: 0; color: #28a745;">🔧 Descrição da Resolução:</h4>
            <p style="margin: 0;">{{ $forcing->descricao_resolucao }}</p>
        </div>
        @endif

        <div class="footer">
            <p><strong>Sistema de Controle de Forcing</strong></p>
            <p>Este email foi enviado automaticamente para confirmar a finalização do seu forcing.</p>
            <p>Data de envio: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
