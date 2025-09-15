<div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>TAG/Descrição</th>
                <th>Área</th>
                <th>Status</th>
                <th>Criado por</th>
                <th>Empresa/Setor</th>
                <th>Data do Forcing</th>
                <th>Liberador</th>
                <th>Data Liberação/Retirada</th>
                <th>Executante</th>
                <th>Local Execução</th>
                <th>Status Execução</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($forcings as $forcing)
                <tr>
                    <td>{{ $forcing->id }}</td>
                    <td>
                        <code class="text-primary">{{ $forcing->tag }}</code>
                        <br><small class="text-muted">{{ Str::limit($forcing->descricao_equipamento, 50) }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $forcing->area }}</span>
                    </td>
                    <td>
                        @if($forcing->status === 'pendente')
                            <span class="badge bg-secondary">
                                <i class="fas fa-clock"></i> Pendente
                            </span>
                        @elseif($forcing->status === 'liberado')
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Liberado
                            </span>
                        @elseif($forcing->status === 'forcado')
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-exclamation-triangle"></i> Forçado
                            </span>
                        @elseif($forcing->status === 'solicitacao_retirada')
                            <span class="badge bg-info">
                                <i class="fas fa-paper-plane"></i> Sol. Retirada
                            </span>
                        @else
                            <span class="badge bg-dark">
                                <i class="fas fa-check-double"></i> Retirado
                            </span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $forcing->user->name }}</strong>
                        <br><small class="text-muted">{{ $forcing->user->username }}</small>
                    </td>
                    <td>
                        <strong>{{ $forcing->user->empresa }}</strong>
                        <br><small class="text-muted">{{ $forcing->user->setor }}</small>
                    </td>
                    <td>
                        <small>{{ $forcing->data_forcing->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        @if($forcing->liberador)
                            <strong>{{ $forcing->liberador->name }}</strong>
                            <br><small class="text-muted">{{ $forcing->liberador->username }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($forcing->status === 'liberado' && $forcing->data_liberacao)
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> 
                                {{ $forcing->data_liberacao->format('d/m/Y H:i') }}
                            </small>
                            <br><small class="text-muted">Liberado</small>
                        @elseif($forcing->status === 'retirado' && $forcing->data_retirada)
                            <small class="text-dark">
                                <i class="fas fa-check-double"></i> 
                                {{ $forcing->data_retirada->format('d/m/Y H:i') }}
                            </small>
                            <br><small class="text-muted">Retirado</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($forcing->executante)
                            <strong>{{ $forcing->executante->name }}</strong>
                            <br><small class="text-muted">{{ $forcing->executante->username }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($forcing->local_execucao)
                            <span class="badge bg-info">{{ $forcing->getLocalExecucaoTexto() }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($forcing->status_execucao === 'executado')
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Executado
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock"></i> Pendente
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('forcing.show', $forcing) }}" class="btn btn-sm btn-outline-info" title="Visualizar">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if(auth()->user()->perfil === 'admin' || $forcing->user_id === auth()->id())
                                <a href="{{ route('forcing.edit', $forcing) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            
                            @if($forcing->status === 'pendente')
                                @if((auth()->user()->perfil === 'liberador' && $forcing->liberado_por == auth()->id()) || auth()->user()->perfil === 'admin')
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            data-bs-toggle="modal" data-bs-target="#liberarModal{{ $forcing->id }}" title="Liberar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                            @endif

                            @if(auth()->user()->perfil === 'executante' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" data-bs-target="#execucaoModal{{ $forcing->id }}" title="Registrar Execução">
                                    <i class="fas fa-tools"></i>
                                </button>
                            @elseif(auth()->user()->perfil === 'admin' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-bs-toggle="modal" data-bs-target="#execucaoModal{{ $forcing->id }}" title="Registrar Execução">
                                    <i class="fas fa-tools"></i>
                                </button>
                            @endif

                            @if($forcing->status === 'forcado')
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="modal" data-bs-target="#solicitarRetiradaModal{{ $forcing->id }}" title="Solicitar Retirada">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            @endif

                            @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status === 'solicitacao_retirada')
                                <button type="button" class="btn btn-sm btn-outline-dark" 
                                        data-bs-toggle="modal" data-bs-target="#retirarModal{{ $forcing->id }}" title="Retirar Forcing">
                                    <i class="fas fa-check-double"></i>
                                </button>
                            @endif
                            
                            @if(auth()->user()->perfil === 'admin')
                                <form action="{{ route('forcing.destroy', $forcing) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Tem certeza que deseja excluir?')" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
