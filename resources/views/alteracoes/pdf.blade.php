<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $alteracao->numero_documento }} - Controle de Alterações Elétricas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20mm;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #34495e;
            margin-bottom: 5px;
        }

        .document-subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .document-number {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .document-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
        }

        .status-pendente { background: #fff3cd; color: #856404; }
        .status-em_analise { background: #d1ecf1; color: #0c5460; }
        .status-aprovada { background: #d4edda; color: #155724; }
        .status-rejeitada { background: #f8d7da; color: #721c24; }
        .status-implementada { background: #cce5ff; color: #004085; }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .form-row {
            display: flex;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            margin-right: 20px;
        }

        .form-group:last-child {
            margin-right: 0;
        }

        .form-label {
            font-weight: bold;
            color: #34495e;
            margin-bottom: 5px;
            display: block;
        }

        .form-value {
            background: #f8f9fa;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            min-height: 35px;
            display: flex;
            align-items: center;
        }

        .form-textarea {
            min-height: 80px;
            align-items: flex-start;
            padding-top: 8px;
        }

        .approvals-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .approval-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .approval-item:last-child {
            border-bottom: none;
        }

        .approval-title {
            font-weight: bold;
            color: #2c3e50;
        }

        .approval-status {
            display: flex;
            align-items: center;
        }

        .approval-approved {
            color: #28a745;
            font-weight: bold;
        }

        .approval-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .approval-icon {
            margin-right: 5px;
        }

        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .checkbox-section {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }

        .checkbox {
            margin-right: 10px;
            transform: scale(1.2);
        }

        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                padding: 15mm;
            }
            
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <div class="logo">⚡ SISTEMA DE CONTROLE</div>
            <div class="document-title">CONTROLE DE ALTERAÇÕES ELÉTRICAS E LÓGICAS</div>
            <div class="document-subtitle">Formulário de Solicitação e Aprovação</div>
        </div>

        <!-- Informações do Documento -->
        <div class="document-info">
            <div class="document-number">
                {{ $alteracao->numero_documento }} - Versão {{ $alteracao->versao }}
            </div>
            <div class="document-status status-{{ $alteracao->status }}">
                {{ ucfirst($alteracao->status) }}
            </div>
        </div>

        <!-- Dados do Solicitante -->
        <div class="section">
            <div class="section-title">📋 DADOS DO SOLICITANTE</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Solicitante:</label>
                    <div class="form-value">{{ $alteracao->solicitante }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Departamento:</label>
                    <div class="form-value">{{ $alteracao->departamento }}</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Data da Solicitação:</label>
                    <div class="form-value">{{ $alteracao->data_solicitacao->format('d/m/Y') }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Data de Publicação:</label>
                    <div class="form-value">{{ $alteracao->data_publicacao->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Descrição da Alteração -->
        <div class="section">
            <div class="section-title">📝 DESCRIÇÃO DA ALTERAÇÃO NECESSÁRIA</div>
            <div class="form-value form-textarea">{{ $alteracao->descricao_alteracao }}</div>
        </div>

        <!-- Motivo da Alteração -->
        <div class="section">
            <div class="section-title">🎯 MOTIVO DA ALTERAÇÃO</div>
            <div class="form-value form-textarea">{{ $alteracao->motivo_alteracao }}</div>
        </div>

        <!-- Termo de Concordância -->
        <div class="checkbox-section">
            <input type="checkbox" class="checkbox" {{ $alteracao->status !== 'pendente' ? 'checked' : '' }} disabled>
            <strong>Estou ciente das alterações acima descritas e concordo que permanecerão válidas por tempo indeterminado até que uma nova versão deste documento seja validada pelos responsáveis.</strong>
        </div>

        <!-- Aprovações -->
        <div class="section">
            <div class="section-title">✅ APROVAÇÕES E ASSINATURAS</div>
            
            <div class="approvals-section">
                <!-- Gerente de Manutenção -->
                <div class="approval-item">
                    <div class="approval-title">Gerente de Manutenção</div>
                    <div class="approval-status">
                        @if($alteracao->gerente_manutencao)
                            <span class="approval-icon">✅</span>
                            <span class="approval-approved">
                                {{ $alteracao->gerente_manutencao }}<br>
                                <small>{{ $alteracao->data_aprovacao_gerente->format('d/m/Y H:i') }}</small>
                            </span>
                        @else
                            <span class="approval-icon">⏳</span>
                            <span class="approval-pending">Pendente</span>
                        @endif
                    </div>
                </div>

                <!-- Coordenador de Manutenção -->
                <div class="approval-item">
                    <div class="approval-title">Coordenador de Manutenção</div>
                    <div class="approval-status">
                        @if($alteracao->coordenador_manutencao)
                            <span class="approval-icon">✅</span>
                            <span class="approval-approved">
                                {{ $alteracao->coordenador_manutencao }}<br>
                                <small>{{ $alteracao->data_aprovacao_coordenador->format('d/m/Y H:i') }}</small>
                            </span>
                        @else
                            <span class="approval-icon">⏳</span>
                            <span class="approval-pending">Pendente</span>
                        @endif
                    </div>
                </div>

                <!-- Técnico Especialista -->
                <div class="approval-item">
                    <div class="approval-title">Técnico Especialista Automação</div>
                    <div class="approval-status">
                        @if($alteracao->tecnico_especialista)
                            <span class="approval-icon">✅</span>
                            <span class="approval-approved">
                                {{ $alteracao->tecnico_especialista }}<br>
                                <small>{{ $alteracao->data_aprovacao_tecnico->format('d/m/Y H:i') }}</small>
                            </span>
                        @else
                            <span class="approval-icon">⏳</span>
                            <span class="approval-pending">Pendente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Comentários de Rejeição (se aplicável) -->
        @if($alteracao->comentarios_rejeicao)
        <div class="section">
            <div class="section-title">❌ MOTIVO DA REJEIÇÃO</div>
            <div class="form-value form-textarea" style="background: #f8d7da; color: #721c24;">
                {{ $alteracao->comentarios_rejeicao }}
            </div>
        </div>
        @endif

        <!-- Assinatura de Implementação (se implementada) -->
        @if($alteracao->status === 'implementada')
        <div class="section">
            <div class="section-title">🚀 IMPLEMENTAÇÃO</div>
            <div class="form-value">
                <strong>Status:</strong> Implementada<br>
                <strong>Data de Atualização:</strong> {{ $alteracao->updated_at->format('d/m/Y H:i') }}
            </div>
        </div>
        @endif

        <!-- Assinaturas -->
        <div class="signature-section">
            <div class="section-title">✍️ ASSINATURAS</div>
            
            <div class="form-row">
                <div class="form-group">
                    <div class="signature-line"></div>
                    <div class="signature-label">Gerente de Manutenção</div>
                </div>
                <div class="form-group">
                    <div class="signature-line"></div>
                    <div class="signature-label">Coordenador de Manutenção</div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <div class="signature-line"></div>
                    <div class="signature-label">Técnico Especialista</div>
                </div>
                <div class="form-group">
                    <div class="signature-line"></div>
                    <div class="signature-label">Data: ___/___/_______</div>
                </div>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <p><strong>Sistema de Controle de Forcing</strong> - Documento gerado automaticamente</p>
            <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }} | Página 1 de 1</p>
            <p>© 2025 Sistema de Controle de Forcing. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        // Auto-print quando a página carregar
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

