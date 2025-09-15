@extends('layouts.app')

@section('title', 'Novo Forcing')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus"></i> Novo Forcing</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('forcing.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="tag" class="form-label">TAG <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tag') is-invalid @enderror" 
                               id="tag" name="tag" value="{{ old('tag') }}" required 
                               placeholder="Ex: TAG-001, PUMP-01...">
                        @error('tag')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campos do equipamento -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="situacao_equipamento" class="form-label">Situação do Equipamento <span class="text-danger">*</span></label>
                                <select class="form-select @error('situacao_equipamento') is-invalid @enderror" 
                                        id="situacao_equipamento" name="situacao_equipamento" required>
                                    <option value="">Selecione a situação...</option>
                                    <option value="desativado" {{ old('situacao_equipamento') == 'desativado' ? 'selected' : '' }}>Desativado</option>
                                    <option value="ativacao_futura" {{ old('situacao_equipamento') == 'ativacao_futura' ? 'selected' : '' }}>Ativação Futura</option>
                                    <option value="em_atividade" {{ old('situacao_equipamento') == 'em_atividade' ? 'selected' : '' }}>Em Atividade</option>
                                </select>
                                @error('situacao_equipamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('area') is-invalid @enderror" 
                                       id="area" name="area" value="{{ old('area') }}" required 
                                       placeholder="Ex: Produção, Manutenção, Utilidades...">
                                @error('area')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao_equipamento" class="form-label">Descrição do Equipamento <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('descricao_equipamento') is-invalid @enderror" 
                                  id="descricao_equipamento" name="descricao_equipamento" rows="3" required 
                                  placeholder="Descreva detalhadamente o equipamento...">{{ old('descricao_equipamento') }}</textarea>
                        @error('descricao_equipamento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" name="observacoes" rows="3" 
                                  placeholder="Observações adicionais (opcional)...">{{ old('observacoes') }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="liberador_id" class="form-label">
                            Liberador Responsável <span class="text-danger">*</span>
                            <button type="button" class="btn btn-link p-0 ms-2 text-primary" 
                                    data-bs-toggle="popover" 
                                    data-bs-placement="right"
                                    data-bs-trigger="hover focus"
                                    data-bs-html="true"
                                    data-bs-title="<i class='fas fa-info-circle'></i> Tipos de Forcing"
                                    data-bs-content="
                                        <div class='text-start'>
                                            <h6 class='text-primary mb-2'><i class='fas fa-cogs'></i> FORCING DE PROCESSO</h6>
                                            <p class='small mb-2'>Ou também chamado de <strong>INTERTRAVAMENTO DE PROCESSO</strong>, são forcing voltados para área de processo, tais como pressões, vazões, temperaturas, sensores de entupimento, sensores de velocidade, intertravamentos entre motores, que interferem diretamente na <strong>segurança do processo</strong>.</p>
                                            
                                            <h6 class='text-warning mb-2'><i class='fas fa-wrench'></i> FORCING DE MANUTENÇÃO</h6>
                                            <p class='small mb-0'>Ou também chamado de <strong>INTERTRAVAMENTO DE EQUIPAMENTOS</strong>, são forcing voltados para garantia da preservação e do bom funcionamento dos equipamentos, tais como pressões, vazões, temperaturas, sensores de entupimento, sensores de velocidade, intertravamentos entre motores, reles de proteção, botoeiras, chaves fim de curso e chaves de desalinhamento, que interferem diretamente na <strong>segurança dos equipamentos</strong>.</p>
                                        </div>
                                    "
                                    title="Ajuda sobre tipos de Forcing">
                                <i class="fas fa-question-circle" style="font-size: 16px;"></i>
                            </button>
                        </label>
                        <select class="form-select @error('liberador_id') is-invalid @enderror" 
                                id="liberador_id" name="liberador_id" required>
                            <option value="">Selecione o liberador responsável...</option>
                            @foreach($liberadores as $liberador)
                                <option value="{{ $liberador->id }}" {{ old('liberador_id') == $liberador->id ? 'selected' : '' }}>
                                    {{ $liberador->name }} - {{ $liberador->empresa }}
                                    @if($liberador->setor)
                                        ({{ $liberador->setor }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> 
                            O liberador selecionado será notificado por email e será o responsável por aprovar este forcing.
                            <br>
                            <i class="fas fa-lightbulb text-warning"></i> 
                            <strong>Dica:</strong> Clique no ícone <i class="fas fa-question-circle text-primary"></i> acima para ver os tipos de forcing e escolher o liberador adequado.
                        </div>
                        @error('liberador_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-user"></i> Informações do Solicitante</h6>
                                    <p class="mb-1"><strong>Nome:</strong> {{ auth()->user()->name }}</p>
                                    <p class="mb-1"><strong>Empresa:</strong> {{ auth()->user()->empresa }}</p>
                                    <p class="mb-1"><strong>Setor:</strong> {{ auth()->user()->setor }}</p>
                                    <p class="mb-0"><strong>E-mail:</strong> {{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-warning">
                                <div class="card-body">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Atenção</h6>
                                    <p class="mb-0">Este forcing será criado com status <strong>"FORÇADO"</strong> e ficará ativo até que um liberador ou administrador o retire do sistema.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('forcing.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Criar Forcing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Ativar popovers do Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos os popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                container: 'body',
                sanitize: false, // Permitir HTML
                customClass: 'forcing-help-popover'
            });
        });
        
        console.log('✅ Popovers de ajuda inicializados:', popoverList.length);
    });
</script>

<style>
    /* Estilização personalizada para o popover de ajuda */
    .forcing-help-popover {
        max-width: 400px;
        font-size: 13px;
    }
    
    .forcing-help-popover .popover-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-weight: bold;
        color: #495057;
    }
    
    .forcing-help-popover .popover-body {
        padding: 12px;
        line-height: 1.4;
    }
    
    .forcing-help-popover h6 {
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .forcing-help-popover .small {
        font-size: 11px;
        line-height: 1.3;
    }
    
    /* Ícone de ajuda com hover effect */
    .btn-link:hover .fas.fa-question-circle {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
</style>
@endsection
