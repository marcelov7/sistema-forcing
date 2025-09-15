<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForcingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'situacao_equipamento' => $this->situacao_equipamento,
            'situacao_equipamento_texto' => $this->getSituacaoEquipamentoTexto(),
            'descricao_equipamento' => $this->descricao_equipamento,
            'area' => $this->area,
            'observacoes' => $this->observacoes,
            'status' => $this->status,
            'status_texto' => $this->status_texto,
            'status_cor' => $this->status_cor,
            'status_execucao' => $this->status_execucao,
            'status_execucao_texto' => $this->status_execucao_texto,
            'local_execucao' => $this->local_execucao,
            'local_execucao_texto' => $this->getLocalExecucaoTexto(),
            
            // Datas
            'data_forcing' => $this->data_forcing?->toISOString(),
            'data_forcing_formatada' => $this->data_forcing_formatada,
            'data_liberacao' => $this->data_liberacao?->toISOString(),
            'data_liberacao_formatada' => $this->data_liberacao_formatada,
            'data_execucao' => $this->data_execucao?->toISOString(),
            'data_execucao_formatada' => $this->data_execucao_formatada,
            'data_solicitacao_retirada' => $this->data_solicitacao_retirada?->toISOString(),
            'data_solicitacao_retirada_formatada' => $this->data_solicitacao_retirada_formatada,
            'data_retirada' => $this->data_retirada?->toISOString(),
            'data_retirada_formatada' => $this->data_retirada_formatada,
            
            // Observações
            'observacoes_liberacao' => $this->observacoes_liberacao,
            'observacoes_execucao' => $this->observacoes_execucao,
            'descricao_resolucao' => $this->descricao_resolucao,
            
            // Relacionamentos
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'empresa' => $this->user->empresa,
                'setor' => $this->user->setor,
            ],
            
            'liberador' => $this->when($this->liberador, [
                'id' => $this->liberador?->id,
                'name' => $this->liberador?->name,
                'username' => $this->liberador?->username,
            ]),
            
            'executante' => $this->when($this->executante, [
                'id' => $this->executante?->id,
                'name' => $this->executante?->name,
                'username' => $this->executante?->username,
            ]),
            
            'retirado_por' => $this->when($this->retiradoPor, [
                'id' => $this->retiradoPor?->id,
                'name' => $this->retiradoPor?->name,
                'username' => $this->retiradoPor?->username,
            ]),
            
            'solicitado_retirada_por' => $this->when($this->solicitadoRetiradaPor, [
                'id' => $this->solicitadoRetiradaPor?->id,
                'name' => $this->solicitadoRetiradaPor?->name,
                'username' => $this->solicitadoRetiradaPor?->username,
            ]),
            
            'unit' => $this->when($this->unit, [
                'id' => $this->unit?->id,
                'name' => $this->unit?->name,
                'code' => $this->unit?->code,
            ]),
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Métodos de status para facilitar no frontend
            'is_pendente' => $this->isPendente(),
            'is_liberado' => $this->isLiberado(),
            'is_forcado' => $this->isForcado(),
            'is_solicitacao_retirada' => $this->isSolicitacaoRetirada(),
            'is_retirado' => $this->isRetirado(),
            'is_executado' => $this->isExecutado(),
        ];
    }
}

