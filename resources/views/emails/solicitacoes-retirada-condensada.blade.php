<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de Retirada Pendentes</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .subtitle {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .summary {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .forcing-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .forcing-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .forcing-title {
            font-weight: 600;
            color: #495057;
            margin: 0;
        }
        .forcing-id {
            background-color: #6c757d;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .forcing-body {
            padding: 20px;
        }
        .forcing-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .info-value {
            color: #495057;
            font-weight: 500;
        }
        .description {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #ff6b35;
            margin-top: 15px;
        }
        .description-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .description-text {
            color: #495057;
            line-height: 1.5;
            margin: 0;
        }
        .action-section {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .urgent {
            background-color: #fff2f2 !important;
            border-color: #ffcccb !important;
        }
        .urgent .forcing-header {
            background-color: #ffe6e6 !important;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .header, .content {
                padding: 20px;
            }
            .forcing-info {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Solicitações de Retirada Pendentes</h1>
            <p class="subtitle">Resumo consolidado de forcings aguardando execução</p>
        </div>
        
        <div class="content">
            <div class="summary">
                <h2 style="margin: 0 0 10px 0; color: #856404;">
                    📋 {{ $totalForcings }} Forcing{{ $totalForcings > 1 ? 's' : '' }} Aguardando Retirada
                </h2>
                <p style="margin: 0; color: #856404;">
                    {{ $totalForcings > 1 ? 'Estas solicitações estão' : 'Esta solicitação está' }} pendente{{ $totalForcings > 1 ? 's' : '' }} de execução e aguarda{{ $totalForcings > 1 ? 'm' : '' }} sua ação.
                </p>
            </div>

            @foreach($forcings as $forcing)
                @php
                    $isUrgent = $forcing->created_at->diffInHours(now()) > 24;
                @endphp
                
                <div class="forcing-card {{ $isUrgent ? 'urgent' : '' }}">
                    <div class="forcing-header">
                        <h3 class="forcing-title">{{ $forcing->titulo }}</h3>
                        <span class="forcing-id">#{{ $forcing->id }}</span>
                    </div>
                    
                    <div class="forcing-body">
                        <div class="forcing-info">
                            <div class="info-item">
                                <span class="info-label">Sistema</span>
                                <span class="info-value">{{ $forcing->sistema }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Subsistema</span>
                                <span class="info-value">{{ $forcing->subsistema }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Criado por</span>
                                <span class="info-value">{{ $forcing->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Liberado por</span>
                                <span class="info-value">{{ $forcing->liberador->name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Data Liberação</span>
                                <span class="info-value">{{ $forcing->data_liberacao ? \Carbon\Carbon::parse($forcing->data_liberacao)->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Solicitado por</span>
                                <span class="info-value">{{ $forcing->solicitadoRetiradaPor->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        
                        @if($forcing->descricao)
                            <div class="description">
                                <div class="description-label">Descrição</div>
                                <p class="description-text">{{ $forcing->descricao }}</p>
                            </div>
                        @endif
                        
                        @if($isUrgent)
                            <div style="background-color: #fff2f2; border: 1px solid #ffcccb; border-radius: 4px; padding: 10px; margin-top: 15px; color: #721c24;">
                                ⚠️ <strong>Atenção:</strong> Esta solicitação está pendente há mais de 24 horas.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
            
            <div class="action-section">
                <p style="margin: 0 0 15px 0; color: #1565c0;">
                    <strong>📝 Ação Necessária</strong><br>
                    {{ $totalForcings > 1 ? 'Estas solicitações precisam' : 'Esta solicitação precisa' }} ser processada{{ $totalForcings > 1 ? 's' : '' }} no sistema.
                </p>
                <a href="{{ config('app.url') }}/dashboard" class="btn">
                    🚀 Acessar Sistema
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>📧 Este é um email automático do Sistema de Controle de Forcing.</p>
            <p>🕒 Enviado em {{ now()->format('d/m/Y \à\s H:i') }} (Horário de Brasília)</p>
        </div>
    </div>
</body>
</html>
