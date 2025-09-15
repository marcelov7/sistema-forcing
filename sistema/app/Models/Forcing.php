<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forcing extends Model
{
    use HasFactory;

    /**
     * Nome da tabela
     */
    protected $table = 'forcing';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tag',
        'situacao_equipamento',
        'descricao_equipamento', 
        'area',
        'status',
        'user_id',
        'unit_id',
        'liberador_id',
        'data_forcing',
        'data_liberacao',
        'data_retirada',
        'observacoes',
        'local_execucao',
        'executante_id',
        'data_execucao',
        'observacoes_execucao',
        'status_execucao',
        'data_solicitacao_retirada',
        'observacoes_solicitacao',
        'solicitado_retirada_por',
        'retirado_por',
        'observacoes_retirada',
        'descricao_resolucao',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_forcing' => 'datetime',
            'data_liberacao' => 'datetime',
            'data_retirada' => 'datetime',
            'data_execucao' => 'datetime',
            'data_solicitacao_retirada' => 'datetime',
        ];
    }

    /**
     * Relacionamento com o usuário criador
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a unidade
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Relacionamento com o liberador
     */
    public function liberador()
    {
        return $this->belongsTo(User::class, 'liberador_id');
    }

    /**
     * Relacionamento com o executante
     */
    public function executante()
    {
        return $this->belongsTo(User::class, 'executante_id');
    }

    /**
     * Relacionamento com quem retirou o forcing
     */
    public function retiradoPor()
    {
        return $this->belongsTo(User::class, 'retirado_por');
    }

    /**
     * Relacionamento com quem solicitou a retirada
     */
    public function solicitadoRetiradaPor()
    {
        return $this->belongsTo(User::class, 'solicitado_retirada_por');
    }

    /**
     * Verifica se o forcing está ativo (forçado)
     */
    public function isForcado()
    {
        return $this->status === 'forcado';
    }

    /**
     * Verifica se o forcing foi liberado
     */
    public function isLiberado()
    {
        return $this->status === 'liberado';
    }

    /**
     * Verifica se o forcing está pendente
     */
    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    /**
     * Verifica se o forcing foi executado
     */
    public function isExecutado()
    {
        return $this->status_execucao === 'executado';
    }

    /**
     * Verifica se o forcing está pendente de execução
     */
    public function isPendenteExecucao()
    {
        return $this->status_execucao === 'pendente';
    }

    /**
     * Verifica se há solicitação de retirada
     */
    public function isSolicitacaoRetirada()
    {
        return $this->status === 'solicitacao_retirada';
    }

    /**
     * Verifica se o forcing foi retirado
     */
    public function isRetirado()
    {
        return $this->status === 'retirado';
    }

    /**
     * Libera o forcing (liberador)
     */
    public function liberar($liberadorId = null, $observacoes = null)
    {
        $this->update([
            'status' => 'liberado',
            'data_liberacao' => now(),
            'liberador_id' => $liberadorId,
            'observacoes' => $observacoes,
        ]);
    }

    /**
     * Força o forcing (executante após execução)
     */
    public function forcar()
    {
        $this->update([
            'status' => 'forcado',
        ]);
    }

    /**
     * Registra a execução do forcing e força automaticamente
     */
    public function registrarExecucao($executanteId, $localExecucao, $observacoesExecucao = null)
    {
        $this->update([
            'status' => 'forcado', // Quando executado, fica forçado (amarelo)
            'status_execucao' => 'executado',
            'executante_id' => $executanteId,
            'local_execucao' => $localExecucao,
            'data_execucao' => now(),
            'observacoes_execucao' => $observacoesExecucao,
        ]);
    }

    /**
     * Solicita a retirada do forcing (solicitante após finalizar atividade)
     */
    public function solicitarRetirada($solicitanteId, $observacoes = null)
    {
        $this->update([
            'status' => 'solicitacao_retirada',
            'data_solicitacao_retirada' => now(),
            'observacoes_solicitacao' => $observacoes,
            'solicitado_retirada_por' => $solicitanteId,
        ]);
    }

    /**
     * Retira o forcing definitivamente (executante finaliza a retirada)
     */
    public function retirar($executanteId, $observacoes = null)
    {
        $this->update([
            'status' => 'retirado',
            'data_retirada' => now(),
            'retirado_por' => $executanteId,
            'observacoes_retirada' => $observacoes,
        ]);
    }

    /**
     * Obtém o texto legível do local de execução
     */
    public function getLocalExecucaoTexto()
    {
        $locais = [
            'supervisorio' => 'Supervisório',
            'plc' => 'PLC',
            'local' => 'Local'
        ];

        return $locais[$this->local_execucao] ?? '-';
    }

    /**
     * Obtém o texto legível da situação do equipamento
     */
    public function getSituacaoEquipamentoTexto()
    {
        $situacoes = [
            'desativado' => 'Desativado',
            'ativacao_futura' => 'Ativação Futura',
            'em_atividade' => 'Em Atividade'
        ];

        return $situacoes[$this->situacao_equipamento] ?? $this->situacao_equipamento;
    }

    /**
     * Obtém o texto legível do status
     */
    public function getStatusTexto()
    {
        $status = [
            'pendente' => 'Pendente',
            'liberado' => 'Liberado',
            'forcado' => 'Forçado',
            'solicitacao_retirada' => 'Solicitação de Retirada',
            'retirado' => 'Retirado'
        ];

        return $status[$this->status] ?? $this->status;
    }

    /**
     * Obtém a classe CSS para o badge do status
     */
    public function getStatusBadgeClass()
    {
        $classes = [
            'pendente' => 'bg-warning',
            'liberado' => 'bg-info',
            'forcado' => 'bg-success',
            'solicitacao_retirada' => 'bg-primary',
            'retirado' => 'bg-secondary'
        ];

        return $classes[$this->status] ?? 'bg-light';
    }
}
