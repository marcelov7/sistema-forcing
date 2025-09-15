@extends('layouts.app')

@section('title', 'Estatísticas de Emails')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Estatísticas de Emails do Sistema</h5>
                    <div>
                        <button class="btn btn-primary btn-sm" onclick="atualizarStats()">
                            <i class="fas fa-refresh"></i> Atualizar
                        </button>
                        @if(auth()->user()->perfil === 'admin')
                        <form method="POST" action="{{ route('admin.email-stats.reset') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm" 
                                    onclick="return confirm('Tem certeza que deseja resetar os contadores?')">
                                <i class="fas fa-undo"></i> Resetar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Card Hoje -->
                        <div class="col-md-6 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title">Hoje</h4>
                                            <h2 id="emails-hoje">{{ $stats['hoje'] }}</h2>
                                            <p class="mb-0">emails enviados</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Este Mês -->
                        <div class="col-md-6 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title">Este Mês</h4>
                                            <h2 id="emails-mes">{{ $stats['mes'] }}</h2>
                                            <p class="mb-0">emails enviados</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico por Tipo -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">📈 Emails por Tipo (Hoje)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($stats['por_tipo'] as $tipo => $quantidade)
                                        <div class="col-md-2 mb-3">
                                            <div class="text-center">
                                                <div class="tipo-icon mb-2">
                                                    @switch($tipo)
                                                        @case('criacao')
                                                            <i class="fas fa-plus-circle fa-2x text-info"></i>
                                                            <h6 class="mt-2">Criação</h6>
                                                            @break
                                                        @case('liberacao')
                                                            <i class="fas fa-unlock fa-2x text-warning"></i>
                                                            <h6 class="mt-2">Liberação</h6>
                                                            @break
                                                        @case('execucao')
                                                            <i class="fas fa-tools fa-2x text-primary"></i>
                                                            <h6 class="mt-2">Execução</h6>
                                                            @break
                                                        @case('solicitacao')
                                                            <i class="fas fa-hand-paper fa-2x text-orange"></i>
                                                            <h6 class="mt-2">Solicitação</h6>
                                                            @break
                                                        @case('confirmacao')
                                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                                            <h6 class="mt-2">Confirmação</h6>
                                                            @break
                                                    @endswitch
                                                </div>
                                                <h4 class="emails-tipo" data-tipo="{{ $tipo }}">{{ $quantidade }}</h4>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Projeções -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">💰 Projeções de Custo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Cenário</th>
                                                    <th>Emails/Mês</th>
                                                    <th>Amazon SES</th>
                                                    <th>SendGrid</th>
                                                    <th>Mailgun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Atual (projeção)</strong></td>
                                                    <td id="projecao-mes">{{ $stats['mes'] * 30 / date('j') }}</td>
                                                    <td class="text-success">$<span id="custo-ses">0.00</span></td>
                                                    <td>Free/Paid</td>
                                                    <td>Free/Paid</td>
                                                </tr>
                                                <tr>
                                                    <td>1.000 emails/mês</td>
                                                    <td>1.000</td>
                                                    <td class="text-success">$0.10</td>
                                                    <td>Free</td>
                                                    <td>Free</td>
                                                </tr>
                                                <tr>
                                                    <td>5.000 emails/mês</td>
                                                    <td>5.000</td>
                                                    <td class="text-success">$0.50</td>
                                                    <td>$19.95</td>
                                                    <td>$35.00</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function atualizarStats() {
    fetch('/admin/email-stats/api')
        .then(response => response.json())
        .then(data => {
            document.getElementById('emails-hoje').textContent = data.hoje;
            document.getElementById('emails-mes').textContent = data.mes;
            
            // Atualizar contadores por tipo
            Object.keys(data.por_tipo).forEach(tipo => {
                const elemento = document.querySelector(`[data-tipo="${tipo}"]`);
                if (elemento) {
                    elemento.textContent = data.por_tipo[tipo];
                }
            });
            
            // Atualizar projeção
            const diasMes = new Date().getDate();
            const projecaoMes = Math.round(data.mes * 30 / diasMes);
            document.getElementById('projecao-mes').textContent = projecaoMes;
            document.getElementById('custo-ses').textContent = (projecaoMes * 0.0001).toFixed(2);
        })
        .catch(error => console.error('Erro:', error));
}

// Atualizar automaticamente a cada 30 segundos
setInterval(atualizarStats, 30000);
</script>

<style>
.text-orange {
    color: #fd7e14 !important;
}
.opacity-50 {
    opacity: 0.5;
}
</style>
@endsection
