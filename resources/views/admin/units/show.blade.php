@extends('layouts.app')

@section('title', 'Detalhes da Unidade')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building"></i> 
                        {{ $unit->name }}
                        @if($unit->active)
                            <span class="badge badge-success ml-2">Ativa</span>
                        @else
                            <span class="badge badge-danger ml-2">Inativa</span>
                        @endif
                    </h4>
                    <div>
                        <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle"></i> Informações Básicas</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Código:</strong></td>
                                    <td>{{ $unit->code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $unit->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Empresa:</strong></td>
                                    <td>{{ $unit->company }}</td>
                                </tr>
                                @if($unit->description)
                                <tr>
                                    <td><strong>Descrição:</strong></td>
                                    <td>{{ $unit->description }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($unit->active)
                                            <span class="badge badge-success">Ativa</span>
                                        @else
                                            <span class="badge badge-danger">Inativa</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-map-marker-alt"></i> Localização</h5>
                            <table class="table table-borderless">
                                @if($unit->address)
                                <tr>
                                    <td><strong>Endereço:</strong></td>
                                    <td>{{ $unit->address }}</td>
                                </tr>
                                @endif
                                @if($unit->city || $unit->state)
                                <tr>
                                    <td><strong>Cidade/Estado:</strong></td>
                                    <td>
                                        {{ $unit->city }}@if($unit->city && $unit->state), {{ $unit->state }}@endif
                                    </td>
                                </tr>
                                @endif
                                @if($unit->phone)
                                <tr>
                                    <td><strong>Telefone:</strong></td>
                                    <td>{{ $unit->phone }}</td>
                                </tr>
                                @endif
                                @if($unit->email)
                                <tr>
                                    <td><strong>E-mail:</strong></td>
                                    <td>{{ $unit->email }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Criado em:</strong></td>
                                    <td>{{ $unit->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 class="mb-0">
                                        <i class="fas fa-users"></i> 
                                        Usuários ({{ $unit->users->count() }})
                                    </h5>
                                    <a href="{{ route('admin.units.users', $unit) }}" class="btn btn-sm btn-info">
                                        Ver Todos
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($unit->users->count() > 0)
                                        @foreach($unit->users->take(5) as $user)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong>{{ $user->name }}</strong><br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                                <span class="badge badge-{{ $user->perfil === 'admin' ? 'danger' : ($user->perfil === 'liberador' ? 'success' : ($user->perfil === 'executante' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($user->perfil) }}
                                                </span>
                                            </div>
                                            @if(!$loop->last)<hr class="my-2">@endif
                                        @endforeach
                                        
                                        @if($unit->users->count() > 5)
                                            <div class="text-center mt-3">
                                                <small class="text-muted">
                                                    E mais {{ $unit->users->count() - 5 }} usuário(s)...
                                                </small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <p>Nenhum usuário cadastrado</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Forcings Recentes ({{ $unit->forcings->count() }})
                                    </h5>
                                    <a href="{{ route('admin.units.forcings', $unit) }}" class="btn btn-sm btn-info">
                                        Ver Todos
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($unit->forcings->count() > 0)
                                        @foreach($unit->forcings as $forcing)
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong>{{ $forcing->forcing }}</strong><br>
                                                    <small class="text-muted">{{ Str::limit($forcing->descricao, 40) }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-{{ $forcing->status === 'forcado' ? 'danger' : 'success' }}">
                                                        {{ $forcing->getStatusTexto() }}
                                                    </span><br>
                                                    <small class="text-muted">{{ $forcing->created_at->format('d/m/Y') }}</small>
                                                </div>
                                            </div>
                                            @if(!$loop->last)<hr class="my-2">@endif
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                            <p>Nenhum forcing registrado</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
