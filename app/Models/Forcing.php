<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Forcing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'forcing';

    protected $fillable = [
        'tag',
        'situacao_equipamento',
        'descricao_equipamento',
        'area',
        'observacoes',
        'user_id',
        'unit_id',
        'liberado_por',
        'executante_id',
        'retirado_por',
        'solicitado_retirada_por',
        'status',
        'status_execucao',
        'local_execucao',
        'data_forcing',
        'data_liberacao',
        'data_execucao',
        'data_solicitacao_retirada',
        'data_retirada',
        'observacoes_liberacao',
        'observacoes_execucao',
        'descricao_resolucao',
    ];

    protected $dates = [
        'data_forcing',
        'data_liberacao',
        'data_execucao',
        'data_solicitacao_retirada',
        'data_retirada',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'data_forcing' => 'datetime',
        'data_liberacao' => 'datetime',
        'data_execucao' => 'datetime',
        'data_solicitacao_retirada' => 'datetime',
        'data_retirada' => 'datetime',
    ];

    /**
     * Sistema de Controle de Forcing - Relacionamentos
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function liberador()
    {
        return $this->belongsTo(User::class, 'liberado_por');
    }

    public function executante()
    {
        return $this->belongsTo(User::class, 'executante_id');
    }

    public function retiradoPor()
    {
        return $this->belongsTo(User::class, 'retirado_por');
    }

    public function solicitadoRetiradaPor()
    {
        return $this->belongsTo(User::class, 'solicitado_retirada_por');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Sistema de Controle de Forcing - Accessors para Texto dos Status
     */
    public function getStatusTextoAttribute()
    {
        $statusMap = [
            'pendente' => 'Pendente',
            'liberado' => 'Liberado',
            'forcado' => 'Forçado',
            'solicitacao_retirada' => 'Solicitação de Retirada',
            'retirado' => 'Retirado'
        ];

        return $statusMap[$this->status] ?? 'Desconhecido';
    }

    public function getStatusCorAttribute()
    {
        $statusCores = [
            'pendente' => 'warning',
            'liberado' => 'info',
            'forcado' => 'success',
            'solicitacao_retirada' => 'primary',
            'retirado' => 'secondary'
        ];

        return $statusCores[$this->status] ?? 'dark';
    }

    public function getStatusExecucaoTextoAttribute()
    {
        $statusMap = [
            'pendente' => 'Pendente',
            'executado' => 'Executado'
        ];

        return $statusMap[$this->status_execucao] ?? 'Pendente';
    }

    public function getLocalExecucaoTexto()
    {
        $localMap = [
            'supervisorio' => 'Supervisório',
            'plc' => 'PLC',
            'local' => 'Local',
            'campo' => 'Campo'
        ];

        return $localMap[$this->local_execucao] ?? 'Não informado';
    }

    public function getSituacaoEquipamentoTextoAttribute()
    {
        $situacaoMap = [
            'desativado' => 'Desativado',
            'ativacao_futura' => 'Ativação Futura',
            'em_atividade' => 'Em Atividade',
        ];

        return $situacaoMap[$this->situacao_equipamento] ?? $this->situacao_equipamento;
    }

    /**
     * Método público para obter situação do equipamento em texto
     * Mantém compatibilidade com views existentes
     */
    public function getSituacaoEquipamentoTexto()
    {
        return $this->situacao_equipamento_texto;
    }

    /**
     * Sistema de Controle de Forcing - Accessors para Datas Formatadas
     */
    public function getDataForcingFormatadaAttribute()
    {
        return $this->data_forcing ? $this->data_forcing->format('d/m/Y H:i:s') : null;
    }

    public function getDataLiberacaoFormatadaAttribute()
    {
        return $this->data_liberacao ? $this->data_liberacao->format('d/m/Y H:i:s') : null;
    }

    public function getDataExecucaoFormatadaAttribute()
    {
        return $this->data_execucao ? $this->data_execucao->format('d/m/Y H:i:s') : null;
    }

    public function getDataSolicitacaoRetiradaFormatadaAttribute()
    {
        return $this->data_solicitacao_retirada ? $this->data_solicitacao_retirada->format('d/m/Y H:i:s') : null;
    }

    public function getDataRetiradaFormatadaAttribute()
    {
        return $this->data_retirada ? $this->data_retirada->format('d/m/Y H:i:s') : null;
    }

    /**
     * Sistema de Controle de Forcing - Métodos de Status
     */
    public function isPendente()
    {
        return $this->status === 'pendente';
    }

    public function isLiberado()
    {
        return $this->status === 'liberado';
    }

    public function isForcado()
    {
        return $this->status === 'forcado';
    }

    public function isSolicitacaoRetirada()
    {
        return $this->status === 'solicitacao_retirada';
    }

    public function isRetirado()
    {
        return $this->status === 'retirado';
    }

    public function isExecutado()
    {
        return $this->status_execucao === 'executado';
    }

    /**
     * Sistema de Controle de Forcing - Métodos de Ação
     */
    public function liberar($liberadorId, $observacoes = null)
    {
        $this->update([
            'status' => 'liberado',
            'data_liberacao' => now(),
            'liberado_por' => $liberadorId,
            'observacoes_liberacao' => $observacoes,
        ]);
    }

    public function executar($executanteId, $localExecucao = null, $observacoes = null)
    {
        $this->update([
            'status' => 'forcado',
            'status_execucao' => 'executado',
            'data_execucao' => now(),
            'executante_id' => $executanteId,
            'local_execucao' => $localExecucao,
            'observacoes_execucao' => $observacoes,
        ]);
    }

    public function solicitarRetirada($solicitanteId, $descricaoResolucao)
    {
        $this->update([
            'status' => 'solicitacao_retirada',
            'data_solicitacao_retirada' => now(),
            'solicitado_retirada_por' => $solicitanteId,
            'descricao_resolucao' => $descricaoResolucao,
        ]);
    }

    public function retirar($executanteId, $observacoes = null)
    {
        $this->update([
            'status' => 'retirado',
            'data_retirada' => now(),
            'retirado_por' => $executanteId,
            'observacoes_execucao' => $observacoes,
        ]);
    }

    /**
     * Sistema de Controle de Forcing - Scopes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeLiberados($query)
    {
        return $query->where('status', 'liberado');
    }

    public function scopeForcados($query)
    {
        return $query->where('status', 'forcado');
    }

    public function scopeRetirados($query)
    {
        return $query->where('status', 'retirado');
    }

    public function scopePorArea($query, $area)
    {
        return $query->where('area', $area);
    }

    public function scopePorUnidade($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Sistema de Controle de Forcing - Métodos Utilitários
     */
    public function tempoDecorrido()
    {
        if (!$this->data_forcing) {
            return 'N/A';
        }

        $agora = now();
        $dataForcamento = $this->data_forcing;
        
        $diff = $agora->diff($dataForcamento);
        
        if ($diff->days > 0) {
            return $diff->days . ' dia(s)';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora(s)';
        } else {
            return $diff->i . ' minuto(s)';
        }
    }

    public function podeSerLiberado()
    {
        return $this->status === 'pendente';
    }

    public function podeSerExecutado()
    {
        return $this->status === 'liberado';
    }

    public function podeSolicitarRetirada()
    {
        return $this->status === 'forcado';
    }

    public function podeSerRetirado()
    {
        return $this->status === 'solicitacao_retirada';
    }

    public function podeSerEditado()
    {
        return in_array($this->status, ['pendente', 'liberado']);
    }

    /**
     * Sistema de Controle de Forcing - Validações de Negócio
     */
    public function validarTransicaoStatus($novoStatus)
    {
        $transicoesPermitidas = [
            'pendente' => ['liberado'],
            'liberado' => ['forcado'],
            'forcado' => ['solicitacao_retirada'],
            'solicitacao_retirada' => ['retirado', 'forcado'], // Pode voltar a forcado se negada
            'retirado' => [] // Status final
        ];

        return in_array($novoStatus, $transicoesPermitidas[$this->status] ?? []);
    }

    /**
     * Sistema de Controle de Forcing - Observadores de Eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($forcing) {
            if (!$forcing->data_forcing) {
                $forcing->data_forcing = now();
            }
        });

        static::updating(function ($forcing) {
            // Log automático de mudanças de status
            if ($forcing->isDirty('status')) {
                \Log::info('Sistema de Controle de Forcing: Status alterado', [
                    'forcing_id' => $forcing->id,
                    'status_anterior' => $forcing->getOriginal('status'),
                    'status_novo' => $forcing->status,
                    'alterado_por' => auth()->id(),
                    'timestamp' => now()
                ]);
            }
        });
    }

    /**
     * Registra a execução do forcing
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
}
