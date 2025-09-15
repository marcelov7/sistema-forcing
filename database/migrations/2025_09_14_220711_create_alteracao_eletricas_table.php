<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alteracao_eletricas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_documento')->unique(); // BR-RE-1030
            $table->string('versao')->default('1.0');
            $table->date('data_publicacao');
            
            // Dados do Solicitante
            $table->string('solicitante');
            $table->string('departamento');
            $table->date('data_solicitacao');
            
            // Descrição da Alteração
            $table->text('descricao_alteracao');
            $table->text('motivo_alteracao');
            
            // Status da Alteração
            $table->enum('status', ['pendente', 'em_analise', 'aprovada', 'rejeitada', 'implementada'])->default('pendente');
            
            // Assinaturas e Aprovações
            $table->string('gerente_manutencao')->nullable();
            $table->timestamp('data_aprovacao_gerente')->nullable();
            $table->string('coordenador_manutencao')->nullable();
            $table->timestamp('data_aprovacao_coordenador')->nullable();
            $table->string('tecnico_especialista')->nullable();
            $table->timestamp('data_aprovacao_tecnico')->nullable();
            
            // Observações e Comentários
            $table->text('observacoes')->nullable();
            $table->text('comentarios_rejeicao')->nullable();
            
            // Arquivos Anexos
            $table->json('arquivos_anexos')->nullable(); // URLs dos arquivos
            
            // Relacionamentos
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alteracao_eletricas');
    }
};
