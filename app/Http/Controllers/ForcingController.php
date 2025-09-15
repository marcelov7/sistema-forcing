<?php

namespace App\Http\Controllers;

use App\Models\Forcing;
use App\Models\User;
use App\Models\Unit;
use App\Models\TermsAcceptance;
use App\Services\ForcingNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForcingController extends Controller
{
    protected $notificationService;

    public function __construct(ForcingNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Sistema de Controle de Forcing - Exibe a lista de todos os forcing
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Forcing::with(['user', 'liberador', 'executante', 'unit']);

        // Filtro por unidade (multi-tenant) - Sistema de Controle de Forcing
        if ($user->perfil === 'admin') {
            // Admin vê todos os forcings de todas as unidades
        } else {
            // Usuários normais veem apenas forcings da sua unidade
            if ($user->unit_id) {
                $query->where('unit_id', $user->unit_id);
            }
        }

        // Aplicar filtros
        $this->aplicarFiltros($query, $request);

        $forcings = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calcular estatísticas totais de TODOS os forcings (sem filtros de paginação)
        $totalStatsQuery = Forcing::query();
        
        // Aplicar mesmo filtro de unidade para as estatísticas
        if ($user->perfil !== 'admin' && $user->unit_id) {
            $totalStatsQuery->where('unit_id', $user->unit_id);
        }

        // Calcular contadores por status
        $totalStats = [
            'pendente' => $totalStatsQuery->clone()->where('status', 'pendente')->count(),
            'liberado' => $totalStatsQuery->clone()->where('status', 'liberado')->count(),
            'forcado' => $totalStatsQuery->clone()->where('status', 'forcado')->count(),
            'solicitacao_retirada' => $totalStatsQuery->clone()->where('status', 'solicitacao_retirada')->count(),
            'retirado' => $totalStatsQuery->clone()->where('status', 'retirado')->count(),
            'executado' => $totalStatsQuery->clone()->where('status_execucao', 'executado')->count(),
            'total' => $totalStatsQuery->count(), // Total geral de todos os forcings
        ];

        // Dados para os filtros
        $filtroData = $this->obterDadosFiltros();

        return view('forcing.index', array_merge(compact('forcings', 'totalStats'), $filtroData));
    }

    /**
     * Sistema de Controle de Forcing - Retorna tabela atualizada via AJAX
     */
    public function refreshTable(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Forcing::with(['user', 'liberador', 'executante', 'unit']);

            // Filtro por unidade
            if ($user->perfil !== 'admin' && $user->unit_id) {
                $query->where('unit_id', $user->unit_id);
            }

            // Aplicar filtros
            $this->aplicarFiltros($query, $request);

            $forcings = $query->orderBy('created_at', 'desc')->paginate(12);

            if ($request->ajax() || $request->wantsJson()) {
                try {
                    $tableHtml = view('forcing.partials.table', compact('forcings'))->render();
                    $paginationHtml = view('forcing.partials.pagination', compact('forcings'))->render();
                    $modalsHtml = view('forcing.partials.modals', compact('forcings'))->render();

                    return response()->json([
                        'success' => true,
                        'html' => $tableHtml,
                        'pagination' => $paginationHtml,
                        'modals' => $modalsHtml,
                        'total' => $forcings->total(),
                        'current_page' => $forcings->currentPage(),
                        'last_page' => $forcings->lastPage(),
                        'timestamp' => now()->format('d/m/Y H:i:s')
                    ]);
                } catch (\Exception $viewException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao renderizar views: ' . $viewException->getMessage()
                    ], 500);
                }
            }

            return redirect()->route('forcing.index');
            
        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao atualizar tabela: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('forcing.index')->with('error', 'Erro ao atualizar dados.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Exibe termo de responsabilidade
     */
    public function showTerms()
    {
        return view('forcing.terms');
    }

    /**
     * Sistema de Controle de Forcing - Formulário para criar novo forcing
     */
    public function create(Request $request)
    {
        // Verificar aceite dos termos
        if (!$request->has('terms_accepted')) {
            return redirect()->route('forcing.terms');
        }
        
        // Registrar aceite dos termos
        TermsAcceptance::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accepted_at' => now(),
            'procedure_version' => 'SMC-MAN-PR-014 V.4',
            'procedure_summary' => 'Controle de Forcing - Diretrizes para alterações em PLC e Supervisório',
        ]);
        
        // Buscar liberadores disponíveis da mesma unidade
        $user = Auth::user();
        $liberadores = User::where(function($query) use ($user) {
                $query->where('perfil', 'liberador')
                      ->where('unit_id', $user->unit_id);
            })
            ->orWhere('perfil', 'admin') // Admins podem ser selecionados de qualquer unidade
            ->orderBy('name')
            ->get();

        $units = Unit::orderBy('name')->get();
        
        return view('forcing.create', compact('liberadores', 'units'));
    }

    /**
     * Sistema de Controle de Forcing - Armazena novo forcing
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tag' => 'required|string|max:100',
            'situacao_equipamento' => 'required|string',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:100',
            'observacoes' => 'nullable|string',
            'liberador_id' => 'nullable|exists:users,id',
        ]);

        try {
            $forcing = Forcing::create([
                'tag' => $validatedData['tag'],
                'situacao_equipamento' => $validatedData['situacao_equipamento'],
                'descricao_equipamento' => $validatedData['descricao_equipamento'],
                'area' => $validatedData['area'],
                'observacoes' => $validatedData['observacoes'] ?? null, // Campo opcional
                'user_id' => Auth::id(),
                'unit_id' => Auth::user()->unit_id, // Usar unit_id do usuário logado
                'liberado_por' => $validatedData['liberador_id'] ?? null, // Liberador selecionado
                'status' => 'pendente',
                'status_execucao' => 'pendente',
                'data_forcing' => now(),
            ]);

            // Enviar notificação para liberadores (se habilitado)
            if (env('ENABLE_EMAIL_NOTIFICATIONS', true)) {
                $this->notificationService->notificarForcingCriado($forcing);
            } else {
                Log::info('Notificações de email desabilitadas', [
                    'forcing_id' => $forcing->id
                ]);
            }

            Log::info('Sistema de Controle de Forcing: Novo forcing criado', [
                'forcing_id' => $forcing->id,
                'user_id' => Auth::id(),
                'tag' => $forcing->tag
            ]);

            return redirect()->route('forcing.index')
                ->with('success', 'Forcing criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao criar forcing: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar forcing.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Exibe detalhes de um forcing
     */
    public function show(Forcing $forcing)
    {
        $forcing->load(['user', 'liberador', 'executante', 'unit']);
        return view('forcing.show', compact('forcing'));
    }

    /**
     * Sistema de Controle de Forcing - Formulário para editar forcing
     */
    public function edit(Forcing $forcing)
    {
        $this->authorize('update', $forcing);

        $units = Unit::orderBy('name')->get();
        
        return view('forcing.edit', compact('forcing', 'units'));
    }

    /**
     * Sistema de Controle de Forcing - Atualiza forcing
     */
    public function update(Request $request, Forcing $forcing)
    {
        $this->authorize('update', $forcing);

        $validatedData = $request->validate([
            'tag' => 'required|string|max:255',
            'situacao_equipamento' => 'required|string',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'observacoes_liberacao' => 'nullable|string',
            'observacoes_execucao' => 'nullable|string',
            'descricao_resolucao' => 'nullable|string',
            'status' => 'nullable|in:pendente,liberado,forcado,solicitacao_retirada,retirado',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        try {
            $updateData = $this->prepararDadosAtualizacao($forcing, $validatedData);
            $forcing->update($updateData);

            Log::info('Sistema de Controle de Forcing: Forcing atualizado', [
                'forcing_id' => $forcing->id,
                'updated_by' => auth()->id(),
                'changed_fields' => array_keys($updateData)
            ]);

            return redirect()->route('forcing.index')
                ->with('success', 'Forcing atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao atualizar: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar forcing.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Remove forcing (apenas admin)
     */
    public function destroy(Forcing $forcing)
    {
        $this->authorize('delete', $forcing);

        try {
            $forcing->delete();

            Log::info('Sistema de Controle de Forcing: Forcing removido', [
                'forcing_id' => $forcing->id,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('forcing.index')
                ->with('success', 'Forcing removido com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao remover: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao remover forcing.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Libera forcing
     */
    public function liberar(Request $request, Forcing $forcing)
    {
        $this->authorize('liberar', $forcing);

        $request->validate([
            'observacoes_liberacao' => 'nullable|string',
        ]);

        try {
            $forcing->update([
                'status' => 'liberado',
                'data_liberacao' => now(),
                'liberado_por' => Auth::id(),
                'observacoes_liberacao' => $request->observacoes_liberacao,
            ]);

            // Enviar notificação para criador e executantes
            $this->notificationService->notificarForcingLiberado($forcing);

            Log::info('Sistema de Controle de Forcing: Forcing liberado', [
                'forcing_id' => $forcing->id,
                'liberado_por' => Auth::id()
            ]);

            return redirect()->route('forcing.show', $forcing->id)
                ->with('success', 'Forcing liberado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao liberar: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao liberar forcing.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Executa forcing
     */
    public function executar(Request $request, Forcing $forcing)
    {
        $this->authorize('executar', $forcing);

        $request->validate([
            'observacoes_execucao' => 'nullable|string',
        ]);

        try {
            $forcing->update([
                'status' => 'forcado',
                'status_execucao' => 'executado',
                'data_execucao' => now(),
                'executante_id' => Auth::id(),
                'observacoes_execucao' => $request->observacoes_execucao,
            ]);

            Log::info('Sistema de Controle de Forcing: Forcing executado', [
                'forcing_id' => $forcing->id,
                'executante_id' => Auth::id()
            ]);

            return redirect()->route('forcing.show', $forcing->id)
                ->with('success', 'Forcing executado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao executar: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao executar forcing.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Solicita retirada
     */
    public function solicitarRetirada(Request $request, Forcing $forcing)
    {
        $this->authorize('solicitarRetirada', $forcing);

        $request->validate([
            'descricao_resolucao' => 'required|string',
        ]);

        try {
            $forcing->update([
                'status' => 'solicitacao_retirada',
                'data_solicitacao_retirada' => now(),
                'solicitado_retirada_por' => Auth::id(),
                'descricao_resolucao' => $request->descricao_resolucao,
            ]);

            Log::info('Sistema de Controle de Forcing: Solicitação de retirada', [
                'forcing_id' => $forcing->id,
                'solicitado_por' => Auth::id()
            ]);

            return redirect()->route('forcing.show', $forcing->id)
                ->with('success', 'Solicitação de retirada enviada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao solicitar retirada: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao solicitar retirada.');
        }
    }

    /**
     * Sistema de Controle de Forcing - Retira forcing definitivamente
     */
    public function retirar(Request $request, Forcing $forcing)
    {
        $this->authorize('retirar', $forcing);

        try {
            $forcing->update([
                'status' => 'retirado',
                'retirado_por' => Auth::id(),
                'data_retirada' => now(),
            ]);

            // Enviar notificação para criador e equipe
            $this->notificationService->notificarForcingRetirado($forcing);

            Log::info('Sistema de Controle de Forcing: Forcing retirado', [
                'forcing_id' => $forcing->id,
                'retirado_por' => Auth::id()
            ]);

            return redirect()->route('forcing.show', $forcing->id)
                ->with('success', 'Forcing retirado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Sistema de Controle de Forcing - Erro ao retirar: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao retirar forcing.');
        }
    }

    /**
     * Métodos auxiliares privados do Sistema de Controle de Forcing
     */
    private function aplicarFiltros($query, Request $request)
    {
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        if ($request->filled('area') && $request->area !== 'todas') {
            $query->where('area', $request->area);
        }

        if ($request->filled('situacao') && $request->situacao !== 'todas') {
            $query->where('situacao_equipamento', $request->situacao);
        }

        if ($request->filled('criador') && $request->criador !== 'todos') {
            $query->where('user_id', $request->criador);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_forcing', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_forcing', '<=', $request->data_fim);
        }

        if ($request->filled('busca')) {
            $query->where(function($q) use ($request) {
                $q->where('tag', 'like', '%' . $request->busca . '%')
                  ->orWhere('descricao_equipamento', 'like', '%' . $request->busca . '%');
            });
        }
    }

    private function obterDadosFiltros()
    {
        return [
            'areas' => Forcing::distinct('area')->orderBy('area')->pluck('area'),
            'situacoes' => Forcing::distinct('situacao_equipamento')->orderBy('situacao_equipamento')->pluck('situacao_equipamento'),
            'criadores' => User::orderBy('name')->get(['id', 'name']),
            'allUsers' => User::orderBy('name')->get(['id', 'name'])
        ];
    }

    private function prepararDadosAtualizacao(Forcing $forcing, array $validatedData)
    {
        $updateData = [];
        
        // Campos que o próprio usuário ou admin podem editar
        if ($forcing->user_id === auth()->id() || auth()->user()->perfil === 'admin') {
            $updateData = array_merge($updateData, [
                'tag' => $validatedData['tag'],
                'situacao_equipamento' => $validatedData['situacao_equipamento'],
                'descricao_equipamento' => $validatedData['descricao_equipamento'],
                'area' => $validatedData['area'],
                'observacoes' => $validatedData['observacoes'],
            ]);
        }

        // Campos específicos para liberadores/admins
        if (auth()->user()->perfil === 'liberador' || auth()->user()->perfil === 'admin') {
            if (isset($validatedData['status'])) {
                $updateData['status'] = $validatedData['status'];
            }
        }

        // Observações de liberação - apenas o liberador responsável ou admin
        if (isset($validatedData['observacoes_liberacao'])) {
            $podeEditarObservacoes = false;
            
            // Admin sempre pode editar
            if (auth()->user()->perfil === 'admin') {
                $podeEditarObservacoes = true;
            }
            // Liberador responsável pode editar
            elseif ($forcing->liberado_por && $forcing->liberado_por === auth()->id()) {
                $podeEditarObservacoes = true;
            }
            
            if ($podeEditarObservacoes) {
                $updateData['observacoes_liberacao'] = $validatedData['observacoes_liberacao'];
            }
        }

        // Campos de execução e resolução
        if (isset($validatedData['observacoes_execucao'])) {
            $updateData['observacoes_execucao'] = $validatedData['observacoes_execucao'];
        }
        if (isset($validatedData['descricao_resolucao'])) {
            $updateData['descricao_resolucao'] = $validatedData['descricao_resolucao'];
        }

        // Admin pode alterar unidade
        if (auth()->user()->perfil === 'admin' && isset($validatedData['unit_id'])) {
            $updateData['unit_id'] = $validatedData['unit_id'];
        }

        return $updateData;
    }

    /**
     * Registra a execução de um forcing
     */
    public function registrarExecucao(Request $request, Forcing $forcing)
    {
        $user = Auth::user();
        
        if ($user->perfil !== 'executante' && $user->perfil !== 'admin') {
            abort(403, 'Apenas executantes e administradores podem registrar execução');
        }

        $request->validate([
            'local_execucao' => 'required|in:supervisorio,plc,local',
            'observacoes_execucao' => 'nullable|string',
        ]);

        $forcing->registrarExecucao(
            $user->id, 
            $request->local_execucao, 
            $request->observacoes_execucao
        );

        // Enviar notificação para criador e liberador
        $this->notificationService->notificarForcingExecutado($forcing);

        Log::info('Sistema de Controle de Forcing: Execução registrada', [
            'forcing_id' => $forcing->id,
            'executante_id' => $user->id,
            'local_execucao' => $request->local_execucao
        ]);

        return redirect()->route('forcing.show', $forcing->id)
            ->with('success', 'Execução registrada com sucesso!');
    }

    /**
     * Redireciona para a listagem filtrada pelo forcing específico (vindo do email)
     */
    public function fromEmail(Forcing $forcing)
    {
        // Redirecionar para a listagem com filtro aplicado para mostrar apenas este forcing
        return redirect()->route('forcing.index', [
            'busca' => $forcing->tag,
            'highlight' => $forcing->id
        ])->with('info', "Forcing #{$forcing->id} destacado vindo do email.");
    }

    /**
     * Página para liberar forcing (mobile)
     */
    public function showLiberarPage(Forcing $forcing)
    {
        $this->authorize('liberar', $forcing);
        
        return view('forcing.mobile.liberar', compact('forcing'));
    }

    /**
     * Página para executar forcing (mobile)
     */
    public function showExecutarPage(Forcing $forcing)
    {
        $this->authorize('executar', $forcing);
        
        return view('forcing.mobile.executar', compact('forcing'));
    }

    /**
     * Página para solicitar retirada (mobile)
     */
    public function showSolicitarRetiradaPage(Forcing $forcing)
    {
        $this->authorize('solicitarRetirada', $forcing);
        
        return view('forcing.mobile.solicitar-retirada', compact('forcing'));
    }

    /**
     * Página para retirar forcing (mobile)
     */
    public function showRetirarPage(Forcing $forcing)
    {
        $this->authorize('retirar', $forcing);
        
        return view('forcing.mobile.retirar', compact('forcing'));
    }
}
