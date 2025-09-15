<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AlteracaoEletrica extends Model
{
    protected $fillable = [
        'numero_documento',
        'versao',
        'data_publicacao',
        'solicitante',
        'departamento',
        'data_solicitacao',
        'descricao_alteracao',
        'motivo_alteracao',
        'status',
        'gerente_manutencao',
        'data_aprovacao_gerente',
        'coordenador_manutencao',
        'data_aprovacao_coordenador',
        'tecnico_especialista',
        'data_aprovacao_tecnico',
        'comentarios_rejeicao',
        'arquivos_anexos',
        'user_id',
        'unit_id'
    ];

    protected $casts = [
        'data_publicacao' => 'date',
        'data_solicitacao' => 'date',
        'data_aprovacao_gerente' => 'datetime',
        'data_aprovacao_coordenador' => 'datetime',
        'data_aprovacao_tecnico' => 'datetime',
        'arquivos_anexos' => 'array'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Scopes
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovada');
    }

    public function scopeRejeitadas($query)
    {
        return $query->where('status', 'rejeitada');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pendente' => 'warning',
            'em_analise' => 'info',
            'aprovada' => 'success',
            'rejeitada' => 'danger',
            'implementada' => 'primary'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextoAttribute()
    {
        $textos = [
            'pendente' => 'Pendente',
            'em_analise' => 'Em Análise',
            'aprovada' => 'Aprovada',
            'rejeitada' => 'Rejeitada',
            'implementada' => 'Implementada'
        ];

        return $textos[$this->status] ?? 'Desconhecido';
    }

    public function getDataFormatadaAttribute()
    {
        return $this->data_solicitacao->format('d/m/Y');
    }

    public function getNumeroCompletoAttribute()
    {
        return $this->numero_documento . ' - Versão ' . $this->versao;
    }

    // Mutators
    public function setNumeroDocumentoAttribute($value)
    {
        // Gera número automático se não fornecido ou vazio
        if (empty($value) || $value === '') {
            $ultimo = self::where('numero_documento', 'like', 'BR-RE-%')->orderBy('id', 'desc')->first();
            if ($ultimo) {
                $ultimoNumero = intval(substr($ultimo->numero_documento, 6));
                $proximoNumero = $ultimoNumero + 1;
            } else {
                $proximoNumero = 1030;
            }
            $value = 'BR-RE-' . str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);
        }
        
        $this->attributes['numero_documento'] = $value;
    }

    // Métodos
    public function podeSerAprovada()
    {
        return in_array($this->status, ['pendente', 'em_analise']);
    }

    public function podeSerRejeitada()
    {
        return in_array($this->status, ['pendente', 'em_analise']);
    }
    
    public function podeSerAprovadaPor($tipo)
    {
        // Verifica se já foi aprovado por este tipo
        switch ($tipo) {
            case 'gerente':
                return $this->gerente_manutencao === null;
            case 'coordenador':
                return $this->coordenador_manutencao === null;
            case 'tecnico':
                return $this->tecnico_especialista === null;
            default:
                return false;
        }
    }

    public function podeSerImplementada()
    {
        // Só pode ser implementada se:
        // 1. Status for 'aprovada' 
        // 2. TODOS os aprovadores tiverem aprovado
        return $this->status === 'aprovada' && 
               $this->gerente_manutencao !== null &&
               $this->coordenador_manutencao !== null &&
               $this->tecnico_especialista !== null;
    }

    public function aprovar($aprovador, $tipo = 'gerente')
    {
        // Registra a aprovação do tipo específico
        switch ($tipo) {
            case 'gerente':
                $this->gerente_manutencao = $aprovador;
                $this->data_aprovacao_gerente = now();
                break;
            case 'coordenador':
                $this->coordenador_manutencao = $aprovador;
                $this->data_aprovacao_coordenador = now();
                break;
            case 'tecnico':
                $this->tecnico_especialista = $aprovador;
                $this->data_aprovacao_tecnico = now();
                break;
        }
        
        // Verifica se TODOS aprovaram para mudar status
        if ($this->todosAprovaram()) {
            $this->status = 'aprovada';
        } else {
            $this->status = 'em_analise'; // Ainda aguardando outras aprovações
        }
        
        $this->save();
    }
    
    public function todosAprovaram()
    {
        return $this->gerente_manutencao !== null &&
               $this->coordenador_manutencao !== null &&
               $this->tecnico_especialista !== null;
    }
    
    public function aprovaçõesPendentes()
    {
        $pendentes = 0;
        if ($this->gerente_manutencao === null) $pendentes++;
        if ($this->coordenador_manutencao === null) $pendentes++;
        if ($this->tecnico_especialista === null) $pendentes++;
        return $pendentes;
    }

    public function rejeitar($motivo)
    {
        $this->status = 'rejeitada';
        $this->comentarios_rejeicao = $motivo;
        $this->save();
    }
}
