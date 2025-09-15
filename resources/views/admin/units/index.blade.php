@extends('layouts.app')

@section('title', 'Gerenciar Unidades')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building"></i> 
                        Gerenciar Unidades
                        <span class="badge badge-primary">{{ $units->count() }}</span>
                    </h4>
                    <a href="{{ route('admin.units.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nova Unidade
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($units->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Empresa</th>
                                        <th>Cidade/Estado</th>
                                        <th>Usuários</th>
                                        <th>Forcings</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($units as $unit)
                                        <tr>
                                            <td>
                                                <strong>{{ $unit->code }}</strong>
                                            </td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->company }}</td>
                                            <td>
                                                @if($unit->city || $unit->state)
                                                    {{ $unit->city }}@if($unit->city && $unit->state)/{{ $unit->state }}@endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.units.users', $unit) }}" class="badge badge-info">
                                                    {{ $unit->users_count }} usuários
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.units.forcings', $unit) }}" class="badge badge-secondary">
                                                    {{ $unit->forcings_count }} forcings
                                                </a>
                                            </td>
                                            <td>
                                                @if($unit->active)
                                                    <span class="badge badge-success">Ativa</span>
                                                @else
                                                    <span class="badge badge-danger">Inativa</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.units.show', $unit) }}" 
                                                       class="btn btn-sm btn-info" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.units.edit', $unit) }}" 
                                                       class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.units.destroy', $unit) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir esta unidade?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        @if($units->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Mostrando {{ $units->firstItem() }} a {{ $units->lastItem() }} de {{ $units->total() }} unidades
                                </div>
                                <div>
                                    {{ $units->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma unidade cadastrada</h5>
                            <p class="text-muted">Clique no botão "Nova Unidade" para começar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
