@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-user-edit"></i> Editar Usuário</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nome de Usuário <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('empresa') is-invalid @enderror" 
                                       id="empresa" name="empresa" value="{{ old('empresa', $user->empresa) }}" required>
                                @error('empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setor" class="form-label">Setor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('setor') is-invalid @enderror" 
                                       id="setor" name="setor" value="{{ old('setor', $user->setor) }}" required>
                                @error('setor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <!-- Campo de Unidade - Novo -->
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unidade <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                <option value="">Selecione a unidade...</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $user->unit_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->code }} - {{ $unit->name }} ({{ $unit->company }})
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                O usuário só poderá ver e gerenciar forcings desta unidade
                            </small>
                        </div>
                    @else
                        <!-- Mostrar unidade atual se não for admin -->
                        <div class="mb-3">
                            <label class="form-label">Unidade</label>
                            <div class="form-control-plaintext">
                                @if($user->unit)
                                    <span class="badge badge-info">
                                        {{ $user->unit->code }} - {{ $user->unit->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Nenhuma unidade definida</span>
                                @endif
                            </div>
                            <input type="hidden" name="unit_id" value="{{ $user->unit_id }}">
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <div class="mb-3">
                            <label for="perfil" class="form-label">Perfil <span class="text-danger">*</span></label>
                            <select class="form-select @error('perfil') is-invalid @enderror" id="perfil" name="perfil" required>
                                <option value="">Selecione o perfil...</option>
                                <option value="user" {{ old('perfil', $user->perfil) === 'user' ? 'selected' : '' }}>
                                    Usuário - Acesso básico
                                </option>
                                <option value="liberador" {{ old('perfil', $user->perfil) === 'liberador' ? 'selected' : '' }}>
                                    Liberador - Pode liberar forcing
                                </option>
                                <option value="executante" {{ old('perfil', $user->perfil) === 'executante' ? 'selected' : '' }}>
                                    Executante - Pode executar forcing
                                </option>
                                <option value="admin" {{ old('perfil', $user->perfil) === 'admin' ? 'selected' : '' }}>
                                    Administrador - Controle da unidade
                                </option>
                            </select>
                            @error('perfil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Apenas administradores podem alterar perfis de usuários da unidade
                            </small>
                            <div class="mt-2 p-2 bg-warning rounded">
                                <small><strong>⚠️ Nota:</strong> Super Administrador é único e não pode ser alterado.</small>
                            </div>
                        </div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Perfil Atual</label>
                            <div class="form-control-plaintext">
                                @if($user->perfil === 'admin')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-crown"></i> Administrador
                                    </span>
                                @elseif($user->perfil === 'liberador')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Liberador
                                    </span>
                                @elseif($user->perfil === 'executante')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-tools"></i> Executante
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user"></i> Usuário
                                    </span>
                                @endif
                                <small class="form-text text-muted d-block">
                                    Entre em contato com um administrador para alterar seu perfil
                                </small>
                            </div>
                            <input type="hidden" name="perfil" value="{{ $user->perfil }}">
                        </div>
                    @endif

                    <!-- Seção para alterar senha -->
                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-lock"></i> Alterar Senha 
                                <small class="text-muted">(opcional)</small>
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Nova Senha</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" minlength="8">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Deixe em branco para manter a senha atual</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" minlength="8">
                                        <small class="form-text text-muted">Confirme a nova senha</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informações importantes:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Campos marcados com <span class="text-danger">*</span> são obrigatórios</li>
                            <li>A senha deve ter no mínimo 8 caracteres</li>
                            @if(!auth()->user()->isAdmin())
                                <li>Apenas administradores podem alterar perfis de usuários</li>
                            @endif
                            <li>O email deve ser único no sistema</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Atualizar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
