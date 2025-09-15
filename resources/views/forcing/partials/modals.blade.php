<!-- Modais para as ações dos forcings -->
@foreach($forcings as $forcing)
    <!-- Modal para liberar forcing -->
    @if($forcing->status === 'pendente' && ((auth()->user()->perfil === 'liberador' && $forcing->liberado_por == auth()->id()) || auth()->user()->perfil === 'admin'))
        <div class="modal fade" id="liberarModal{{ $forcing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('forcing.liberar', $forcing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Liberar Forcing</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>TAG:</strong> {{ $forcing->tag }}</p>
                            <p><strong>Descrição:</strong> {{ $forcing->descricao_equipamento }}</p>
                            <div class="mb-3">
                                <label for="observacoes_liberacao{{ $forcing->id }}" class="form-label">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    Observações da Liberação
                                </label>
                                <textarea class="form-control form-control-lg" id="observacoes_liberacao{{ $forcing->id }}" 
                                          name="observacoes_liberacao" rows="4" 
                                          placeholder="Descreva observações sobre a liberação do forcing (opcional)"></textarea>
                                <div class="form-text">Informações adicionais sobre a liberação</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Liberar Forcing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para registrar execução -->
    @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status_execucao === 'pendente')
        <div class="modal fade" id="execucaoModal{{ $forcing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('forcing.registrar-execucao', $forcing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Registrar Execução</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>TAG:</strong> {{ $forcing->tag }}</p>
                            <p><strong>Descrição:</strong> {{ $forcing->descricao_equipamento }}</p>
                            
                            <div class="mb-3">
                                <label for="local_execucao{{ $forcing->id }}" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    Local de Execução <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="local_execucao{{ $forcing->id }}" name="local_execucao" required>
                                    <option value="">Selecione o local...</option>
                                    <option value="supervisorio">🖥️ Supervisório</option>
                                    <option value="plc">⚙️ PLC</option>
                                    <option value="local">📍 Local</option>
                                </select>
                                <div class="form-text">Escolha onde o forcing será executado</div>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes_execucao{{ $forcing->id }}" class="form-label">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    Observações da Execução
                                </label>
                                <textarea class="form-control form-control-lg" id="observacoes_execucao{{ $forcing->id }}" 
                                          name="observacoes_execucao" rows="4" 
                                          placeholder="Descreva detalhes sobre a execução do forcing (opcional)"></textarea>
                                <div class="form-text">Informações adicionais sobre como foi executado o forcing</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-tools"></i> Registrar Execução
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para solicitar retirada -->
    @if($forcing->status === 'forcado')
        <div class="modal fade" id="solicitarRetiradaModal{{ $forcing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('forcing.solicitar-retirada', $forcing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Solicitar Retirada do Forcing</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>TAG:</strong> {{ $forcing->tag }}</p>
                            <p><strong>Área:</strong> {{ $forcing->area }}</p>
                            <p class="text-muted">Você está solicitando a retirada deste forcing. O executante será notificado.</p>
                            
                            <div class="mb-3">
                                <label for="descricao_resolucao{{ $forcing->id }}" class="form-label">
                                    <i class="fas fa-clipboard-check me-1"></i>
                                    Descrição da Resolução <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control form-control-lg" id="descricao_resolucao{{ $forcing->id }}" 
                                          name="descricao_resolucao" rows="5" required 
                                          placeholder="Descreva detalhadamente como foi resolvido o problema que ocasionou o forcing..."></textarea>
                                <div class="form-text">Explique passo a passo como o problema foi solucionado</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observacoes_solicitacao{{ $forcing->id }}" class="form-label">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    Observações Adicionais
                                </label>
                                <textarea class="form-control form-control-lg" id="observacoes_solicitacao{{ $forcing->id }}" 
                                          name="observacoes" rows="3" 
                                          placeholder="Observações adicionais sobre a solicitação de retirada (opcional)"></textarea>
                                <div class="form-text">Informações complementares sobre a solicitação</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-paper-plane"></i> Solicitar Retirada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para retirar forcing definitivamente -->
    @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status === 'solicitacao_retirada')
        <div class="modal fade" id="retirarModal{{ $forcing->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('forcing.retirar', $forcing) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Retirar Forcing</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>TAG:</strong> {{ $forcing->tag }}</p>
                            <p><strong>Descrição:</strong> {{ $forcing->descricao_equipamento }}</p>
                            <p class="text-muted">Esta ação finalizará o ciclo do forcing, marcando-o como retirado definitivamente.</p>
                            <div class="mb-3">
                                <label for="observacoes_retirada{{ $forcing->id }}" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes_retirada{{ $forcing->id }}" 
                                          name="observacoes" rows="3" placeholder="Observações sobre a retirada (opcional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-check-double"></i> Retirar Forcing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
