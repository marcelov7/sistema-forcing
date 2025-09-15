<?php

namespace App\Http\Controllers;

use App\Models\Forcing;
use App\Models\User;
use App\Models\TermsAcceptance;
use App\Services\ForcingNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcingController extends Controller
{
    protected $notificationService;

    public function __construct(ForcingNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Exibe a lista de todos os forcing
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Forcing::with(['user', 'liberador', 'executante', 'retiradoPor', 'solicitadoRetiradaPor', 'unit']);

        // Filtro por unidade (multi-tenant)
        if ($user->perfil === 'admin') {
            // Admin vê todos os forcings de todas as unidades
            // Não aplicar filtro de unidade
        } else {
            // Usuários normais veem apenas forcings da sua unidade (se aplicável)
            if (isset($user->unit_id) && $user->unit_id) {
                $query->where('unit_id', $user->unit_id);
            }
        }

        // Filtro por status
        if ($request->filled('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        // Filtro por área
        if ($request->filled('area') && $request->area !== 'todas') {
            $query->where('area', $request->area);
        }

        // Filtro por situação do equipamento
        if ($request->filled('situacao') && $request->situacao !== 'todas') {
            $query->where('situacao_equipamento', $request->situacao);
        }

        // Filtro por criador
        if ($request->filled('criador') && $request->criador !== 'todos') {
            $query->where('user_id', $request->criador);
        }

        // Filtro por data
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_forcing', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_forcing', '<=', $request->data_fim);
        }

        // Busca por TAG ou descrição
        if ($request->filled('busca')) {
            $query->where(function($q) use ($request) {
                $q->where('tag', 'like', '%' . $request->busca . '%')
                  ->orWhere('descricao_equipamento', 'like', '%' . $request->busca . '%');
            });
        }

        $forcings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Dados para os filtros
        $areas = Forcing::distinct('area')->orderBy('area')->pluck('area');
        $situacoes = Forcing::distinct('situacao_equipamento')->orderBy('situacao_equipamento')->pluck('situacao_equipamento');
        $criadores = User::orderBy('name')->get(['id', 'name']);
        $allUsers = User::orderBy('name')->get(['id', 'name']);

        return view('forcing.index', compact('forcings', 'areas', 'situacoes', 'criadores', 'allUsers'));
    }

    /**
     * Retorna apenas a tabela atualizada via AJAX
     */
    public function refreshTable(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Forcing::with(['user', 'liberador', 'executante', 'retiradoPor', 'solicitadoRetiradaPor', 'unit']);

            // Filtro por unidade (multi-tenant)
            if ($user->perfil === 'admin') {
                // Admin vê todos os forcings
            } else {
                // Usuários normais veem apenas forcings da sua unidade (se aplicável)
                if (method_exists($user, 'unit_id') && $user->unit_id) {
                    $query->where('unit_id', $user->unit_id);
                }
            }

            // Aplicar os mesmos filtros do método index
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

            $forcings = $query->orderBy('created_at', 'desc')->paginate(15);

            // Sempre retornar JSON se for requisição AJAX
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
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
                        'message' => 'Erro ao renderizar as views: ' . $viewException->getMessage()
                    ], 500);
                }
            }

            return redirect()->route('forcing.index');
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('forcing.index')->with('error', 'Erro ao atualizar dados: ' . $e->getMessage());
        }
    }

    /**
     * Exibe o termo de responsabilidade
     */
    public function showTerms()
    {
        return view('forcing.terms');
    }

    /**

        /**
     * Mostra o formulário para criar um novo forcing
     */
    public function create(Request $request)
    {
        // Verificar se o usuário aceitou os termos
        if (!$request->has('terms_accepted')) {
            return redirect()->route('forcing.terms');
        }
        
        // Registrar o aceite dos termos
        TermsAcceptance::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accepted_at' => now(),
            'procedure_version' => 'SMC-MAN-PR-014 V.4',
            'procedure_summary' => 'Controle de Forcing - Diretrizes para alterações em PLC e Supervisório',
        ]);
        
        // Buscar liberadores disponíveis
        $liberadores = User::where('perfil', 'liberador')
            ->orWhere('perfil', 'admin')
            ->orderBy('name')
            ->get();
        
        return view('forcing.create', compact('liberadores'));
    }

    /**
     * Armazena um novo forcing
     */
    public function store(Request $request)
    {
        $request->validate([
            'tag' => 'required|string|max:100',
            'situacao_equipamento' => 'required|in:desativado,ativacao_futura,em_atividade',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:100',
            'liberador_id' => 'required|exists:users,id',
        ]);

        // Verificar se o liberador selecionado é realmente um liberador ou admin
        $liberador = User::find($request->liberador_id);
        if (!in_array($liberador->perfil, ['liberador', 'admin'])) {
            return back()->withErrors(['liberador_id' => 'O usuário selecionado não é um liberador válido.']);
        }

        $forcing = Forcing::create([
            'tag' => $request->tag,
            'situacao_equipamento' => $request->situacao_equipamento,
            'descricao_equipamento' => $request->descricao_equipamento,
            'area' => $request->area,
            'user_id' => Auth::id(),
            'unit_id' => Auth::user()->unit_id, // Associar à unidade do usuário
            'liberador_id' => $request->liberador_id, // Atribuir liberador específico
            'status' => 'pendente', // Novo forcing inicia como pendente
            'data_forcing' => now(),
        ]);

        // Enviar notificação apenas para o liberador específico
        $this->notificationService->notificarForcingCriadoParaLiberador($forcing, $liberador);

        return redirect()->route('forcing.index')
            ->with('success', "Forcing criado com sucesso! O liberador {$liberador->name} foi notificado por email.");
    }

    /**
     * Exibe os detalhes de um forcing específico
     */
    public function show(Forcing $forcing)
    {
        $forcing->load(['user', 'liberador', 'executante']);
        return view('forcing.show', compact('forcing'));
    }

    /**
     * Exibe o formulário para editar um forcing
     */
    public function edit(Forcing $forcing)
    {
        // Apenas o criador ou admin pode editar
        if (Auth::user()->perfil !== 'admin' && $forcing->user_id !== Auth::id()) {
            abort(403, 'Não autorizado');
        }

        return view('forcing.edit', compact('forcing'));
    }

    /**
     * Atualiza um forcing
     */
    public function update(Request $request, Forcing $forcing)
    {
        // Apenas o criador ou admin pode editar
        if (Auth::user()->perfil !== 'admin' && $forcing->user_id !== Auth::id()) {
            abort(403, 'Não autorizado');
        }

        $request->validate([
            'tag' => 'required|string|max:100',
            'situacao_equipamento' => 'required|in:desativado,ativacao_futura,em_atividade',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:100',
        ]);

        $forcing->update([
            'tag' => $request->tag,
            'situacao_equipamento' => $request->situacao_equipamento,
            'descricao_equipamento' => $request->descricao_equipamento,
            'area' => $request->area,
        ]);

        return redirect()->route('forcing.index')
            ->with('success', 'Forcing atualizado com sucesso!');
    }

    /**
     * Remove um forcing
     */
    public function destroy(Forcing $forcing)
    {
        // Apenas admin pode deletar
        if (Auth::user()->perfil !== 'admin') {
            abort(403, 'Não autorizado');
        }

        $forcing->delete();

        return redirect()->route('forcing.index')
            ->with('success', 'Forcing removido com sucesso!');
    }

    /**
     * Libera um forcing (apenas o liberador designado ou admin)
     */
    public function liberar(Request $request, Forcing $forcing)
    {
        $user = Auth::user();
        
        // Verificar se o usuário é admin ou o liberador específico designado para este forcing
        if ($user->perfil !== 'admin' && ($user->perfil !== 'liberador' || $forcing->liberador_id !== $user->id)) {
            abort(403, 'Apenas o liberador designado para este forcing ou administradores podem liberá-lo');
        }

        $request->validate([
            'observacoes' => 'nullable|string',
        ]);

        $forcing->liberar($user->id, $request->observacoes);

        // Enviar notificação para executantes
        $this->notificationService->notificarForcingLiberado($forcing);

        return redirect()->route('forcing.index')
            ->with('success', 'Forcing liberado com sucesso!');
    }

    /**
     * Força novamente um forcing (reativa) - removido pois não é mais necessário
     */
    public function forcar(Forcing $forcing)
    {
        // Método removido - agora o forcing é forçado automaticamente na execução
        return redirect()->route('forcing.index')
            ->with('info', 'O forcing será forçado automaticamente quando executado.');
    }

    /**
     * Registra a execução de um forcing (apenas executantes e admin)
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

        // Enviar notificação para criador, liberadores e executante
        $this->notificationService->notificarForcingExecutado($forcing);

        return redirect()->route('forcing.index')
            ->with('success', 'Execução registrada com sucesso!');
    }

    /**
     * Solicita a retirada de um forcing (qualquer usuário pode solicitar para qualquer forcing)
     */
    public function solicitarRetirada(Request $request, Forcing $forcing)
    {
        $user = Auth::user();
        
        // Qualquer usuário pode solicitar retirada de qualquer forcing
        // Removida a verificação de propriedade
        
        // Verifica se o forcing está forçado
        if (!$forcing->isForcado()) {
            return redirect()->route('forcing.index')
                ->with('error', 'Só é possível solicitar retirada de forcings que estão forçados');
        }

        $request->validate([
            'observacoes' => 'nullable|string',
            'descricao_resolucao' => 'required|string',
        ]);

        $forcing->solicitarRetirada($user->id, $request->observacoes);
        
        // Adicionar descrição da resolução
        $forcing->update([
            'descricao_resolucao' => $request->descricao_resolucao
        ]);

        // Enviar notificação para executantes
        $this->notificationService->notificarSolicitacaoRetirada($forcing);

        return redirect()->route('forcing.index')
            ->with('success', 'Solicitação de retirada enviada com sucesso!');
    }

    /**
     * Retira definitivamente um forcing (apenas executantes e admin)
     */
    public function retirar(Request $request, Forcing $forcing)
    {
        $user = Auth::user();
        
        if ($user->perfil !== 'executante' && $user->perfil !== 'admin') {
            abort(403, 'Apenas executantes e administradores podem retirar forcings');
        }

        // Verifica se há solicitação de retirada
        if (!$forcing->isSolicitacaoRetirada()) {
            return redirect()->route('forcing.index')
                ->with('error', 'Só é possível retirar forcings com solicitação de retirada');
        }

        $request->validate([
            'observacoes' => 'nullable|string',
        ]);

        $forcing->retirar($user->id, $request->observacoes);

        // Enviar confirmação de retirada apenas para o solicitante e admin
        $this->notificationService->notificarConfirmacaoRetirada($forcing);

        return redirect()->route('forcing.index')
            ->with('success', 'Forcing retirado com sucesso! O solicitante foi notificado por email.');
    }
}
