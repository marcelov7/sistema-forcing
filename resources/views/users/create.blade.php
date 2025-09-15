@extends('layouts.app')

@section('title', 'Criar Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Criar Novo Usuário</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nome de Usuário <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                       id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required minlength="8">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Mínimo 8 caracteres</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="empresa" class="form-label">Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('empresa') is-invalid @enderror" 
                                       id="empresa" name="empresa" value="{{ old('empresa') }}" required>
                                @error('empresa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setor" class="form-label">Setor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('setor') is-invalid @enderror" 
                                       id="setor" name="setor" value="{{ old('setor') }}" required>
                                @error('setor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Campo de Unidade - Novo -->
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Unidade <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">Selecione a unidade...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
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

                    <div class="mb-3">
                        <label for="perfil" class="form-label">Perfil <span class="text-danger">*</span></label>
                        <select class="form-select @error('perfil') is-invalid @enderror" id="perfil" name="perfil" required>
                            <option value="">Selecione o perfil...</option>
                            <option value="user" {{ old('perfil') === 'user' ? 'selected' : '' }}>
                                <i class="fas fa-user"></i> Usuário - Acesso básico
                            </option>
                            <option value="liberador" {{ old('perfil') === 'liberador' ? 'selected' : '' }}>
                                <i class="fas fa-check-circle"></i> Liberador - Pode liberar forcing
                            </option>
                            <option value="executante" {{ old('perfil') === 'executante' ? 'selected' : '' }}>
                                <i class="fas fa-tools"></i> Executante - Pode executar forcing
                            </option>
                            <option value="admin" {{ old('perfil') === 'admin' ? 'selected' : '' }}>
                                <i class="fas fa-crown"></i> Administrador - Controle da unidade
                            </option>
                        </select>
                        @error('perfil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Escolha o nível de acesso do usuário na unidade selecionada
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Descrição dos Perfis:</strong>
                        <ul class="mb-0 mt-2">
                            <li><strong>Usuário:</strong> Pode criar e visualizar forcing da unidade</li>
                            <li><strong>Liberador:</strong> Pode liberar e gerenciar forcing da unidade</li>
                            <li><strong>Executante:</strong> Pode registrar execução de forcing da unidade</li>
                            <li><strong>Administrador:</strong> Controle total da unidade e usuários</li>
                        </ul>
                        <div class="mt-2 p-2 bg-warning rounded">
                            <strong>⚠️ Nota:</strong> Super Administrador é único no sistema e gerencia todas as unidades.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
