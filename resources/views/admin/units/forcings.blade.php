@extends('layouts.app')

@section('title', 'Forcings da Unidade')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Forcings da {{ $unit->name }}
                        <span class="badge badge-primary">{{ $forcings->count() }}</span>
                    </h4>
                    <div>
                        <a href="{{ route('admin.units.show', $unit) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver Unidade
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> Informações da Unidade:</strong><br>
                                <strong>Código:</strong> {{ $unit->code }} | 
                                <strong>Empresa:</strong> {{ $unit->company }} | 
                                <strong>Localização:</strong> {{ $unit->city }}/{{ $unit->state }}
                            </div>
                        </div>
                    </div>

                    @if($forcings->count() > 0)
                        <!-- Estatísticas -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas dos Forcings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @php
                                                $total = $forcings->count();
                                                $forcados = $forcings->where('status', 'forcado')->count();
                                                $retirados = $forcings->where('status', 'retirado')->count();
                                            @endphp
                                            <div class="col-md-4">
                                                <div class="card border-primary">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-primary">{{ $total }}</h3>
                                                        <p class="mb-0">Total de Forcings</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-danger">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-danger">{{ $forcados }}</h3>
                                                        <p class="mb-0">Forçados</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="card border-success">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-success">{{ $retirados }}</h3>
                                                        <p class="mb-0">Retirados</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Forcing</th>
                                        <th>Status</th>
                                        <th>Equipamento</th>
                                        <th>Local de Execução</th>
                                        <th>Executante</th>
                                        <th>Liberador</th>
                                        <th>Data/Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($forcings as $forcing)
                                        <tr>
                                            <td>
                                                <strong>{{ $forcing->forcing }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($forcing->descricao, 50) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $forcing->status === 'forcado' ? 'danger' : 'success' }}">
                                                    {{ $forcing->getStatusTexto() }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $forcing->equipamento }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $forcing->getSituacaoEquipamentoTexto() }}</small>
                                            </td>
                                            <td>{{ $forcing->getLocalExecucaoTexto() }}</td>
                                            <td>
                                                @if($forcing->executante)
                                                    <strong>{{ $forcing->executante->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $forcing->executante->setor }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($forcing->liberador)
                                                    <strong>{{ $forcing->liberador->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $forcing->liberador->setor }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $forcing->created_at->format('d/m/Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $forcing->created_at->format('H:i') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Forcings por Local de Execução -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Forcings por Local de Execução</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @php
                                                $locais = $forcings->groupBy('local_execucao');
                                                $locaisTexto = [
                                                    'campo' => 'Campo',
                                                    'sala_controle' => 'Sala de Controle',
                                                    'ambos' => 'Ambos'
                                                ];
                                            @endphp
                                            @foreach($locais as $local => $forcingsLocal)
                                                <div class="col-md-4">
                                                    <div class="card border-info">
                                                        <div class="card-body text-center">
                                                            <h4 class="text-info">{{ $forcingsLocal->count() }}</h4>
                                                            <p class="mb-0">{{ $locaisTexto[$local] ?? $local }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum forcing registrado</h5>
                            <p class="text-muted">Esta unidade ainda não possui forcings registrados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
