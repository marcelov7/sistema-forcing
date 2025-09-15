@extends('layouts.app')

@section('title', 'Usuários da Unidade')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users"></i> 
                        Usuários da {{ $unit->name }}
                        <span class="badge badge-primary">{{ $users->count() }}</span>
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

                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Username</th>
                                        <th>E-mail</th>
                                        <th>Perfil</th>
                                        <th>Setor</th>
                                        <th>Cadastrado em</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->is_super_admin)
                                                    <span class="badge badge-warning ml-1">Super Admin</span>
                                                @endif
                                            </td>
                                            <td><code>{{ $user->username }}</code></td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->perfil === 'admin' ? 'danger' : ($user->perfil === 'liberador' ? 'success' : ($user->perfil === 'executante' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($user->perfil) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->setor }}</td>
                                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-success">Ativo</span>
                                                @else
                                                    <span class="badge badge-warning">Pendente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Estatísticas por Perfil</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @php
                                                $perfis = $users->groupBy('perfil');
                                            @endphp
                                            @foreach(['admin' => 'danger', 'liberador' => 'success', 'executante' => 'warning', 'user' => 'secondary'] as $perfil => $cor)
                                                @if(isset($perfis[$perfil]))
                                                    <div class="col-md-3">
                                                        <div class="card border-{{ $cor }}">
                                                            <div class="card-body text-center">
                                                                <h3 class="text-{{ $cor }}">{{ $perfis[$perfil]->count() }}</h3>
                                                                <p class="mb-0">{{ ucfirst($perfil) }}{{ $perfis[$perfil]->count() > 1 ? 's' : '' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum usuário cadastrado</h5>
                            <p class="text-muted">Esta unidade ainda não possui usuários cadastrados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
