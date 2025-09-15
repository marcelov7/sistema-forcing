@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-edit"></i> Editar Meu Perfil
                    </h1>
                    <p class="text-muted mb-0">Atualize suas informações pessoais</p>
                </div>
                <div>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Erro!</strong> Corrija os problemas abaixo:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Informações Pessoais -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-card"></i> Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nome Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        Username <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('username') is-invalid @enderror" 
                                           id="username" 
                                           name="username" 
                                           value="{{ old('username', $user->username) }}" 
                                           required>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        E-mail <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa" class="form-label">
                                        Empresa <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('empresa') is-invalid @enderror" 
                                           id="empresa" 
                                           name="empresa" 
                                           value="{{ old('empresa', $user->empresa) }}" 
                                           required>
                                    @error('empresa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="setor" class="form-label">
                                        Setor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('setor') is-invalid @enderror" 
                                           id="setor" 
                                           name="setor" 
                                           value="{{ old('setor', $user->setor) }}" 
                                           required>
                                    @error('setor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alterar Senha -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lock"></i> Alterar Senha
                            <small class="text-muted">(opcional)</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informação:</strong> Deixe os campos em branco se não desejar alterar a senha.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nova Senha</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Mínimo de 8 caracteres</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                    <div class="form-text">Digite a senha novamente</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Informações Laterais -->
        <div class="col-lg-4">
            <!-- Perfil Atual -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tag"></i> Perfil Atual
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($user->perfil === 'admin')
                        <span class="badge bg-danger fs-6 mb-3">
                            <i class="fas fa-crown"></i> Administrador
                        </span>
                        <p class="text-muted mb-0">
                            Acesso completo ao sistema, incluindo gerenciamento de usuários.
                        </p>
                    @elseif($user->perfil === 'liberador')
                        <span class="badge bg-warning fs-6 mb-3">
                            <i class="fas fa-check-circle"></i> Liberador
                        </span>
                        <p class="text-muted mb-0">
                            Responsável por analisar e liberar forcing para execução.
                        </p>
                    @elseif($user->perfil === 'executante')
                        <span class="badge bg-info fs-6 mb-3">
                            <i class="fas fa-tools"></i> Executante
                        </span>
                        <p class="text-muted mb-0">
                            Responsável por executar forcing e pode solicitar retirada.
                        </p>
                    @else
                        <span class="badge bg-secondary fs-6 mb-3">
                            <i class="fas fa-user"></i> Usuário
                        </span>
                        <p class="text-muted mb-0">
                            Pode criar forcing e acompanhar o status.
                        </p>
                    @endif

                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Para alterar seu perfil, entre em contato com um administrador.
                    </small>
                </div>
            </div>

            <!-- Dicas de Segurança -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt"></i> Dicas de Segurança
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use uma senha forte com pelo menos 8 caracteres
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Combine letras maiúsculas, minúsculas e números
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Não compartilhe suas credenciais
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Mantenha seus dados sempre atualizados
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Última Atualização -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock"></i> Informações da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Criado em:</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Última atualização:</td>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.badge {
    font-size: 0.875rem;
}

.alert {
    border-radius: 8px;
    border: none;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação de confirmação de senha
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePassword() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('As senhas não coincidem');
                passwordConfirmation.classList.add('is-invalid');
            } else {
                passwordConfirmation.setCustomValidity('');
                passwordConfirmation.classList.remove('is-invalid');
            }
        }
    }
    
    password.addEventListener('input', validatePassword);
    passwordConfirmation.addEventListener('input', validatePassword);
});
</script>
@endsection
