@extends('layouts.app')

@section('title', 'Liberar Forcing')

@section('content')
<div class="container-fluid px-3">
    <!-- Header Mobile -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('forcing.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0">
                <i class="fas fa-check text-success me-2"></i>
                Liberar Forcing
            </h1>
            <small class="text-muted">Confirme a liberação do forcing</small>
        </div>
    </div>

    <!-- Informações do Forcing -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informações do Forcing
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">TAG:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->tag }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Descrição:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->descricao_equipamento }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Área:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->area }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Status:</label>
                    <span class="badge bg-warning fs-6">{{ ucfirst($forcing->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Liberação -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Dados da Liberação
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('forcing.liberar', $forcing) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="observacoes_liberacao" class="form-label">
                        <i class="fas fa-comment-alt me-1"></i>
                        Observações da Liberação
                    </label>
                    <textarea class="form-control form-control-lg" id="observacoes_liberacao" 
                              name="observacoes_liberacao" rows="5" 
                              placeholder="Descreva observações sobre a liberação do forcing (opcional)"></textarea>
                    <div class="form-text">Informações adicionais sobre a liberação</div>
                </div>

                <!-- Botões de Ação -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg" id="btnLiberar">
                        <i class="fas fa-check me-2"></i>
                        <span class="btn-text">Confirmar Liberação</span>
                    </button>
                    <a href="{{ route('forcing.index') }}" class="btn btn-outline-secondary btn-lg" id="btnCancelar">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Aviso de Confirmação -->
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Atenção:</strong> Ao confirmar a liberação, o forcing será disponibilizado para execução e o executante será notificado.
    </div>
</div>

<style>
/* Estilos específicos para mobile */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem !important;
    }
    
    .card {
        border-radius: 1rem !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }
    
    .card-header {
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1rem !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        padding: 1rem 1.5rem !important;
        font-size: 1.1rem !important;
        border-radius: 0.75rem !important;
    }
    
    .form-control-lg {
        font-size: 16px !important; /* Previne zoom no iOS */
        padding: 1rem !important;
        border-radius: 0.75rem !important;
        border: 2px solid #dee2e6 !important;
    }
    
    .form-control-lg:focus {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
    }
    
    .form-label {
        font-size: 1rem !important;
        font-weight: 600 !important;
        margin-bottom: 0.75rem !important;
    }
    
    .form-text {
        font-size: 0.9rem !important;
        margin-top: 0.5rem !important;
    }
    
    .badge {
        font-size: 0.9rem !important;
        padding: 0.5rem 1rem !important;
    }
    
    .alert {
        border-radius: 0.75rem !important;
        padding: 1rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="/liberar"]');
    const btnLiberar = document.getElementById('btnLiberar');
    const btnCancelar = document.getElementById('btnCancelar');
    
    if (form && btnLiberar) {
        form.addEventListener('submit', function(e) {
            // Mostrar loading no botão
            btnLiberar.disabled = true;
            btnLiberar.classList.add('btn-loading');
            btnLiberar.innerHTML = `
                <div class="loading-spinner loading-liberar"></div>
                <span class="btn-text">Liberando...</span>
            `;
            
            // Desabilitar botão de cancelar
            btnCancelar.style.pointerEvents = 'none';
            btnCancelar.style.opacity = '0.5';
            
            // Mostrar overlay de loading
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay show';
            overlay.innerHTML = `
                <div class="loading-overlay-content">
                    <div class="loading-overlay-spinner loading-liberar"></div>
                    <div class="loading-overlay-text">Liberando forcing...</div>
                    <div class="mt-2">
                        <small class="text-muted">Por favor, aguarde...</small>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
            
            // Simular processamento e redirecionamento
            setTimeout(() => {
                // Mostrar mensagem de sucesso
                const successOverlay = document.createElement('div');
                successOverlay.className = 'loading-overlay show';
                successOverlay.innerHTML = `
                    <div class="loading-overlay-content">
                        <div class="text-success mb-3">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                        <div class="loading-overlay-text">Forcing liberado com sucesso!</div>
                        <div class="mt-2">
                            <small class="text-muted">Redirecionando para detalhes...</small>
                        </div>
                    </div>
                `;
                
                // Substituir overlay de loading pelo de sucesso
                overlay.remove();
                document.body.appendChild(successOverlay);
                
                // Redirecionar após 800ms
                setTimeout(() => {
                    window.location.href = "{{ route('forcing.show', $forcing->id) }}";
                }, 800);
            }, 500); // Simular processamento de 500ms
        });
    }
});
</script>
@endsection
