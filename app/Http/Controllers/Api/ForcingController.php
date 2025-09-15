<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForcingResource;
use App\Models\Forcing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ForcingController extends Controller
{
    /**
     * Listar forcings com filtros
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Forcing::with(['user', 'liberador', 'executante', 'retiradoPor', 'solicitadoRetiradaPor']);

        // Filtro por unidade (multi-tenant)
        if ($user->perfil !== 'super_admin') {
            $query->where('unit_id', $user->unit_id);
        }

        // Filtros
        if ($request->has('status') && $request->status !== 'todos') {
            $query->where('status', $request->status);
        }

        if ($request->has('area') && $request->area !== 'todas') {
            $query->where('area', $request->area);
        }

        if ($request->has('busca')) {
            $busca = $request->busca;
            $query->where(function($q) use ($busca) {
                $q->where('tag', 'like', "%{$busca}%")
                  ->orWhere('descricao_equipamento', 'like', "%{$busca}%");
            });
        }

        if ($request->has('situacao') && $request->situacao !== 'todas') {
            $query->where('situacao_equipamento', $request->situacao);
        }

        if ($request->has('criador') && $request->criador !== 'todos') {
            $query->where('user_id', $request->criador);
        }

        if ($request->has('data_inicio')) {
            $query->whereDate('data_forcing', '>=', $request->data_inicio);
        }

        if ($request->has('data_fim')) {
            $query->whereDate('data_forcing', '<=', $request->data_fim);
        }

        // Ordenação
        $query->orderBy('created_at', 'desc');

        // Paginação
        $perPage = $request->get('per_page', 15);
        $forcings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ForcingResource::collection($forcings),
            'meta' => [
                'current_page' => $forcings->currentPage(),
                'last_page' => $forcings->lastPage(),
                'per_page' => $forcings->perPage(),
                'total' => $forcings->total(),
                'from' => $forcings->firstItem(),
                'to' => $forcings->lastItem(),
            ]
        ]);
    }

    /**
     * Criar novo forcing
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required|string|max:255',
            'situacao_equipamento' => 'required|in:desativado,ativacao_futura,em_atividade',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        $forcing = Forcing::create([
            'tag' => $request->tag,
            'situacao_equipamento' => $request->situacao_equipamento,
            'descricao_equipamento' => $request->descricao_equipamento,
            'area' => $request->area,
            'observacoes' => $request->observacoes,
            'user_id' => $user->id,
            'unit_id' => $user->unit_id,
            'status' => 'pendente',
            'status_execucao' => 'pendente',
            'data_forcing' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Forcing criado com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'unit']))
        ], 201);
    }

    /**
     * Visualizar forcing específico
     */
    public function show($id)
    {
        $user = Auth::user();
        $forcing = Forcing::with(['user', 'liberador', 'executante', 'retiradoPor', 'solicitadoRetiradaPor', 'unit']);

        // Filtro por unidade
        if ($user->perfil !== 'super_admin') {
            $forcing->where('unit_id', $user->unit_id);
        }

        $forcing = $forcing->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new ForcingResource($forcing)
        ]);
    }

    /**
     * Atualizar forcing
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        // Verificar permissões
        if ($user->perfil !== 'admin' && $forcing->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar este forcing'
            ], 403);
        }

        // Não permitir edição se já foi retirado
        if ($forcing->status === 'retirado') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível editar um forcing já retirado'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'tag' => 'required|string|max:255',
            'situacao_equipamento' => 'required|in:desativado,ativacao_futura,em_atividade',
            'descricao_equipamento' => 'required|string',
            'area' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $forcing->update($request->only([
            'tag', 'situacao_equipamento', 'descricao_equipamento', 
            'area', 'observacoes'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Forcing atualizado com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'unit']))
        ]);
    }

    /**
     * Excluir forcing
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if ($user->perfil !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas administradores podem excluir forcings'
            ], 403);
        }

        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        // Não permitir exclusão se já foi retirado
        if ($forcing->status === 'retirado') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um forcing já retirado'
            ], 403);
        }

        $forcing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Forcing excluído com sucesso'
        ]);
    }

    /**
     * Liberar forcing
     */
    public function liberar(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->perfil, ['liberador', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para liberar forcings'
            ], 403);
        }

        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        if ($forcing->status !== 'pendente') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas forcings pendentes podem ser liberados'
            ], 403);
        }

        $forcing->liberar($user->id, $request->observacoes);

        return response()->json([
            'success' => true,
            'message' => 'Forcing liberado com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'liberador', 'unit']))
        ]);
    }

    /**
     * Registrar execução
     */
    public function executar(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->perfil, ['executante', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para executar forcings'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'local_execucao' => 'required|in:supervisorio,plc,local,campo',
            'observacoes_execucao' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        if ($forcing->status !== 'liberado') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas forcings liberados podem ser executados'
            ], 403);
        }

        $forcing->registrarExecucao(
            $user->id,
            $request->local_execucao,
            $request->observacoes_execucao
        );

        return response()->json([
            'success' => true,
            'message' => 'Execução registrada com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'executante', 'unit']))
        ]);
    }

    /**
     * Solicitar retirada
     */
    public function solicitarRetirada(Request $request, $id)
    {
        $user = Auth::user();
        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        if ($forcing->status !== 'forcado') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas forcings forçados podem ter retirada solicitada'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'descricao_resolucao' => 'required|string',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $forcing->solicitarRetirada($user->id, $request->descricao_resolucao);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de retirada enviada com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'solicitadoRetiradaPor', 'unit']))
        ]);
    }

    /**
     * Retirar forcing
     */
    public function retirar(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!in_array($user->perfil, ['executante', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para retirar forcings'
            ], 403);
        }

        $forcing = Forcing::where('unit_id', $user->unit_id)->findOrFail($id);

        if ($forcing->status !== 'solicitacao_retirada') {
            return response()->json([
                'success' => false,
                'message' => 'Apenas forcings com solicitação de retirada podem ser retirados'
            ], 403);
        }

        $forcing->retirar($user->id, $request->observacoes);

        return response()->json([
            'success' => true,
            'message' => 'Forcing retirado com sucesso',
            'data' => new ForcingResource($forcing->load(['user', 'retiradoPor', 'unit']))
        ]);
    }

    /**
     * Estatísticas do dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $query = Forcing::query();

        // Filtro por unidade
        if ($user->perfil !== 'super_admin') {
            $query->where('unit_id', $user->unit_id);
        }

        $stats = [
            'total' => $query->count(),
            'pendentes' => $query->where('status', 'pendente')->count(),
            'liberados' => $query->where('status', 'liberado')->count(),
            'forcados' => $query->where('status', 'forcado')->count(),
            'solicitacao_retirada' => $query->where('status', 'solicitacao_retirada')->count(),
            'retirados' => $query->where('status', 'retirado')->count(),
            'executados' => $query->where('status_execucao', 'executado')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

