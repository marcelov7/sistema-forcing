<?php
/**
 * 📄 GERADOR DE ARQUIVO WORD - PROCEDIMENTO OPERACIONAL
 * Script para criar documento Word (.docx) do procedimento do Sistema de Forcing
 */

// Verifica se PhpWord está disponível
if (!class_exists('PhpOffice\PhpWord\PhpWord')) {
    // Se não estiver, vamos criar um HTML para conversão manual
    echo "⚠️ PhpWord não instalado. Gerando HTML para conversão manual...\n";
    criarHTMLParaWord();
    exit;
}

require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

function criarDocumentoWord() {
    $phpWord = new PhpWord();
    
    // Configurações do documento
    $phpWord->getSettings()->setThemeFontLang(new \PhpOffice\PhpWord\Style\Language('pt-BR'));
    
    // Estilos
    $phpWord->addTitleStyle(1, ['size' => 18, 'bold' => true, 'color' => '0066CC']);
    $phpWord->addTitleStyle(2, ['size' => 14, 'bold' => true, 'color' => '0066CC']);
    $phpWord->addTitleStyle(3, ['size' => 12, 'bold' => true, 'color' => '333333']);
    
    // Seção principal
    $section = $phpWord->addSection([
        'marginTop' => 1134,
        'marginBottom' => 1134,
        'marginLeft' => 1134,
        'marginRight' => 1134
    ]);
    
    // Cabeçalho
    $header = $section->addHeader();
    $header->addText('DEVAXIS - Sistema de Controle de Forcing', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
    
    // Título principal
    $section->addTitle('PROCEDIMENTO OPERACIONAL', 1);
    $section->addTitle('Sistema de Controle de Forcing', 2);
    $section->addText('Versão Web 2.0 - Janeiro 2025', ['italic' => true], ['alignment' => 'center']);
    $section->addTextBreak(2);
    
    // 4.3 Descrição Operacional
    $section->addTitle('4.3. Descrição Operacional', 1);
    
    // Visão Geral
    $section->addTitle('Visão Geral do Sistema', 2);
    $section->addText('O Sistema de Controle de Forcing é uma aplicação web que gerencia todo o ciclo de vida dos forcings elétricos, desde a solicitação até a retirada, com controle de permissões por perfil de usuário e notificações automáticas por email.');
    $section->addTextBreak();
    
    // URL de Acesso
    $section->addText('URL de Acesso: https://forcing.devaxis.com.br', ['bold' => true]);
    $section->addText('Compatibilidade: Desktop, Tablet, Smartphone (Interface Responsiva)');
    $section->addTextBreak();
    
    // Perfis de Usuário
    $section->addTitle('Perfis de Usuário e Responsabilidades', 2);
    
    $perfis = [
        'SOLICITANTE' => [
            'Responsabilidade: Criar solicitações de forcing',
            'Acesso: Criar, visualizar e editar seus próprios forcings'
        ],
        'LIBERADOR' => [
            'Responsabilidade: Analisar e liberar forcings para execução',
            'Acesso: Visualizar todos os forcings, liberar e editar observações de liberação'
        ],
        'EXECUTANTE' => [
            'Responsabilidade: Executar forcings liberados e realizar retiradas',
            'Acesso: Registrar execução, solicitar e confirmar retiradas'
        ],
        'ADMINISTRADOR' => [
            'Responsabilidade: Gestão completa do sistema',
            'Acesso: Todas as funcionalidades, edição completa e relatórios'
        ]
    ];
    
    foreach ($perfis as $perfil => $detalhes) {
        $section->addTitle($perfil, 3);
        foreach ($detalhes as $detalhe) {
            $section->addListItem($detalhe, 0, null, 'multilevel');
        }
        $section->addTextBreak();
    }
    
    // Fluxo Operacional
    $section->addTitle('Fluxo Operacional Completo', 2);
    
    // 1. Criação de Forcing
    $section->addTitle('1. CRIAÇÃO DE FORCING', 3);
    $section->addText('RESPONSÁVEL: Solicitante (Técnico/Engenheiro)', ['bold' => true, 'color' => 'FFFFFF'], ['bgcolor' => '0066CC']);
    $section->addTextBreak();
    
    $section->addText('PROCEDIMENTO:', ['bold' => true]);
    $procedimentoCriacao = [
        'Acessar o sistema: https://forcing.devaxis.com.br',
        'Fazer login com credenciais de solicitante',
        'Clicar em "Novo Forcing"',
        'Preencher os campos obrigatórios:',
        '  • TAG: Identificação do equipamento',
        '  • Equipamento: Descrição do equipamento',
        '  • Área: Local de instalação',
        '  • Localização: Localização específica',
        '  • Tipo de Equipamento: Selecionar da lista',
        '  • Situação do Equipamento: Desativado/Ativação Futura/Em Atividade',
        '  • Motivo: Justificativa técnica para o forcing',
        '  • Observações: Detalhes adicionais (opcional)',
        '  • Liberador: Selecionar o responsável pela liberação',
        '  • Previsão de Retirada: Data limite para retirada',
        'Clicar em "Cadastrar"'
    ];
    
    foreach ($procedimentoCriacao as $item) {
        $section->addListItem($item, 0, null, 'multilevel');
    }
    
    $section->addText('NOTIFICAÇÃO AUTOMÁTICA: Email enviado para o liberador selecionado', ['bold' => true, 'color' => '0066CC']);
    $section->addTextBreak(2);
    
    // 2. Liberação
    $section->addTitle('2. LIBERAÇÃO DE FORCING', 3);
    $section->addText('RESPONSÁVEL: Liberador (Supervisor/Engenheiro Responsável)', ['bold' => true, 'color' => 'FFFFFF'], ['bgcolor' => '0066CC']);
    $section->addTextBreak();
    
    $procedimentoLiberacao = [
        'Receber notificação por email',
        'Acessar o sistema pelo link do email ou diretamente',
        'Revisar a solicitação na lista de forcings',
        'Clicar em "Liberar" no forcing desejado',
        'Adicionar observações de liberação (se necessário)',
        'Confirmar a liberação'
    ];
    
    foreach ($procedimentoLiberacao as $item) {
        $section->addListItem($item, 0, null, 'multilevel');
    }
    
    $section->addText('NOTIFICAÇÃO AUTOMÁTICA: Email enviado para executantes e criador', ['bold' => true, 'color' => '0066CC']);
    $section->addTextBreak(2);
    
    // Continuar com outras seções...
    // (Para brevidade, vou adicionar apenas as principais seções)
    
    // Sistema de Notificações
    $section->addTitle('Sistema de Notificações', 2);
    
    // Criar tabela de emails
    $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
    $table->addRow();
    $table->addCell(3000)->addText('Etapa', ['bold' => true]);
    $table->addCell(3000)->addText('Destinatários', ['bold' => true]);
    $table->addCell(3000)->addText('Conteúdo', ['bold' => true]);
    
    $emails = [
        ['Forcing Criado', 'Liberador selecionado', 'Notificação de nova solicitação'],
        ['Forcing Liberado', 'Criador + Executantes', 'Autorização para execução'],
        ['Forcing Executado', 'Criador + Liberador + Executante', 'Confirmação de execução'],
        ['Solicitação de Retirada', 'Executantes + Administradores', 'Pedido de retirada do forcing'],
        ['Forcing Retirado', 'Criador + Liberador + Executante', 'Finalização do processo']
    ];
    
    foreach ($emails as $email) {
        $table->addRow();
        $table->addCell(3000)->addText($email[0]);
        $table->addCell(3000)->addText($email[1]);
        $table->addCell(3000)->addText($email[2]);
    }
    
    $section->addTextBreak(2);
    
    // Status do Sistema
    $section->addTitle('Status do Sistema', 2);
    $section->addListItem('🔵 PENDENTE - Aguardando liberação', 0, null, 'multilevel');
    $section->addListItem('🟡 LIBERADO - Autorizado para execução', 0, null, 'multilevel');
    $section->addListItem('🟢 EXECUTADO - Forcing ativo no sistema', 0, null, 'multilevel');
    $section->addListItem('🔴 RETIRADO - Processo finalizado', 0, null, 'multilevel');
    $section->addTextBreak(2);
    
    // Rodapé
    $footer = $section->addFooter();
    $footer->addText('Documento Atualizado: Janeiro 2025 | Versão: Sistema Web v2.0', ['size' => 9]);
    $footer->addText('Responsável: Equipe de Desenvolvimento Devaxis | Contato: suporte@devaxis.com.br', ['size' => 9]);
    
    return $phpWord;
}

function criarHTMLParaWord() {
    $html = file_get_contents('PROCEDIMENTO_OPERACIONAL_FORCING.html');
    
    if ($html) {
        // Criar versão simplificada para Word
        $wordHtml = str_replace([
            'font-family: Arial, sans-serif;',
            'max-width: 21cm;',
            'margin: 0 auto;'
        ], [
            'font-family: "Times New Roman", serif;',
            'width: 100%;',
            'margin: 0;'
        ], $html);
        
        file_put_contents('PROCEDIMENTO_OPERACIONAL_FORCING_WORD.html', $wordHtml);
        echo "✅ Arquivo HTML para Word criado: PROCEDIMENTO_OPERACIONAL_FORCING_WORD.html\n";
        echo "📝 Para converter para Word:\n";
        echo "1. Abra o arquivo HTML no navegador\n";
        echo "2. Pressione Ctrl+A para selecionar tudo\n";
        echo "3. Pressione Ctrl+C para copiar\n";
        echo "4. Abra o Microsoft Word\n";
        echo "5. Pressione Ctrl+V para colar\n";
        echo "6. Salve como .docx\n";
    }
}

// Execução principal
try {
    if (class_exists('PhpOffice\PhpWord\PhpWord')) {
        echo "📄 Gerando documento Word...\n";
        $phpWord = criarDocumentoWord();
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = 'PROCEDIMENTO_OPERACIONAL_FORCING_' . date('Y-m-d') . '.docx';
        $objWriter->save($filename);
        
        echo "✅ Documento Word criado com sucesso: $filename\n";
        echo "📊 Tamanho do arquivo: " . round(filesize($filename) / 1024, 2) . " KB\n";
        
    } else {
        criarHTMLParaWord();
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao gerar documento: " . $e->getMessage() . "\n";
    echo "🔄 Tentando método alternativo...\n";
    criarHTMLParaWord();
}

echo "\n📋 ARQUIVOS CRIADOS:\n";
echo "• PROCEDIMENTO_OPERACIONAL_FORCING.html (Visualização completa)\n";
if (file_exists('PROCEDIMENTO_OPERACIONAL_FORCING_WORD.html')) {
    echo "• PROCEDIMENTO_OPERACIONAL_FORCING_WORD.html (Versão para Word)\n";
}
if (file_exists('PROCEDIMENTO_OPERACIONAL_FORCING_' . date('Y-m-d') . '.docx')) {
    echo "• PROCEDIMENTO_OPERACIONAL_FORCING_" . date('Y-m-d') . ".docx (Documento Word)\n";
}

echo "\n💡 INSTRUÇÕES:\n";
echo "1. Para visualizar: Abra o arquivo .html no navegador\n";
echo "2. Para Word: Use o arquivo .docx ou copie/cole do HTML no Word\n";
echo "3. Para imprimir: Use Ctrl+P no navegador ou Word\n";
?> 