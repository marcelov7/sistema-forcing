<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlteracaoEletrica;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AlteracaoEletricaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AlteracaoEletrica::with(['user', 'unit'])->orderBy('created_at', 'desc');

        // Filtro por unidade (multi-tenant) - Sistema de Controle de Alterações Elétricas
        if ($user->perfil === 'admin' || $user->is_super_admin) {
            // Admin vê todas as alterações de todas as unidades
        } else {
            // Usuários normais veem apenas alterações da sua unidade
            if ($user->unit_id) {
                $query->where('unit_id', $user->unit_id);
            }
        }

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('solicitante')) {
            $query->where('solicitante', 'like', '%' . $request->solicitante . '%');
        }

        if ($request->filled('departamento')) {
            $query->where('departamento', 'like', '%' . $request->departamento . '%');
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_solicitacao', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_solicitacao', '<=', $request->data_fim);
        }

        $alteracoes = $query->paginate(15);

        // Estatísticas totais de TODAS as alterações (sem filtros de paginação)
        $totalStatsQuery = AlteracaoEletrica::query();
        
        // Aplicar mesmo filtro de unidade para as estatísticas
        if ($user->perfil !== 'admin' && !$user->is_super_admin && $user->unit_id) {
            $totalStatsQuery->where('unit_id', $user->unit_id);
        }

        $stats = [
            'total' => $totalStatsQuery->count(),
            'pendentes' => $totalStatsQuery->clone()->where('status', 'pendente')->count(),
            'aprovadas' => $totalStatsQuery->clone()->where('status', 'aprovada')->count(),
            'rejeitadas' => $totalStatsQuery->clone()->where('status', 'rejeitada')->count(),
        ];

        return view('alteracoes.index', compact('alteracoes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('alteracoes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'departamento' => 'required|string|max:255',
            'data_solicitacao' => 'required|date',
            'descricao_alteracao' => 'required|string',
            'motivo_alteracao' => 'required|string',
        ]);

        $alteracao = AlteracaoEletrica::create([
            'numero_documento' => '', // Será preenchido automaticamente pelo mutator
            'versao' => '1.0',
            'solicitante' => Auth::user()->name, // Sempre usar o nome do usuário logado
            'departamento' => $request->departamento,
            'data_solicitacao' => $request->data_solicitacao,
            'data_publicacao' => now(),
            'descricao_alteracao' => $request->descricao_alteracao,
            'motivo_alteracao' => $request->motivo_alteracao,
            'user_id' => Auth::id(),
            'unit_id' => Auth::user()->unit_id, // Associa à unidade do usuário
        ]);

        return redirect()->route('alteracoes.show', $alteracao)
            ->with('success', 'Alteração elétrica criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode visualizar esta alteração
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para visualizar esta alteração.');
            }
        }
        
        $alteracao->load(['user', 'unit']);
        return view('alteracoes.show', compact('alteracao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode editar esta alteração
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para editar esta alteração.');
            }
        }
        
        return view('alteracoes.edit', compact('alteracao'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode atualizar esta alteração
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para atualizar esta alteração.');
            }
        }

        $validationRules = [
            'departamento' => 'required|string|max:255',
            'data_solicitacao' => 'required|date',
            'descricao_alteracao' => 'required|string',
            'motivo_alteracao' => 'required|string',
        ];

        // Apenas administradores podem alterar o nome do solicitante
        if (Auth::user()->perfil === 'admin' || Auth::user()->is_super_admin) {
            $validationRules['solicitante'] = 'required|string|max:255';
        }

        $request->validate($validationRules);

        $updateData = [
            'departamento' => $request->departamento,
            'data_solicitacao' => $request->data_solicitacao,
            'descricao_alteracao' => $request->descricao_alteracao,
            'motivo_alteracao' => $request->motivo_alteracao,
        ];

        // Apenas administradores podem alterar o nome do solicitante
        if (Auth::user()->perfil === 'admin' || Auth::user()->is_super_admin) {
            $updateData['solicitante'] = $request->solicitante;
        }

        $alteracao->update($updateData);

        return redirect()->route('alteracoes.show', $alteracao)
            ->with('success', 'Alteração elétrica atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode excluir esta alteração
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para excluir alterações de outras unidades.');
            }
        }
        
        $alteracao->delete();
        
        return redirect()->route('alteracoes.index')
            ->with('success', 'Alteração elétrica excluída com sucesso!');
    }

    /**
     * Aprovar alteração
     */
    public function aprovar(Request $request, AlteracaoEletrica $alteracao)
    {
        $request->validate([
            'tipo_aprovador' => 'required|in:gerente,coordenador,tecnico',
            'nome_aprovador' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        // Verificar se o usuário pode aprovar esta alteração (mesma unidade)
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para aprovar alterações de outras unidades.');
            }
        }

        // Verifica se o usuário pode aprovar como este tipo baseado no setor
        switch ($request->tipo_aprovador) {
            case 'gerente':
                if (!$user->podeAprovarComoGerente()) {
                    return redirect()->route('alteracoes.show', $alteracao)
                        ->with('error', 'Você não tem permissão para aprovar como Gerente de Manutenção. Apenas usuários do setor "Manutenção" podem realizar esta aprovação.');
                }
                break;
            case 'coordenador':
                if (!$user->podeAprovarComoCoordenador()) {
                    return redirect()->route('alteracoes.show', $alteracao)
                        ->with('error', 'Você não tem permissão para aprovar como Coordenador de Manutenção. Apenas usuários do setor "Manutenção" podem realizar esta aprovação.');
                }
                break;
            case 'tecnico':
                if (!$user->podeAprovarComoTecnico()) {
                    return redirect()->route('alteracoes.show', $alteracao)
                        ->with('error', 'Você não tem permissão para aprovar como Técnico Especialista. Apenas usuários dos setores "Automação", "Elétrica", "Instrumentação" ou "Técnico Eletricista" podem realizar esta aprovação.');
                }
                break;
        }

        // Verifica se já foi aprovado por este tipo
        if (!$alteracao->podeSerAprovadaPor($request->tipo_aprovador)) {
            return redirect()->route('alteracoes.show', $alteracao)
                ->with('error', 'Esta alteração já foi aprovada por este responsável!');
        }

        $alteracao->aprovar($request->nome_aprovador, $request->tipo_aprovador);

        $message = 'Alteração aprovada com sucesso!';
        if ($alteracao->todosAprovaram()) {
            $message .= ' Todas as aprovações foram concluídas. A alteração pode ser implementada.';
        } else {
            $message .= ' Aguardando aprovação de outros responsáveis.';
        }

        return redirect()->route('alteracoes.show', $alteracao)
            ->with('success', $message);
    }

    /**
     * Rejeitar alteração
     */
    public function rejeitar(Request $request, AlteracaoEletrica $alteracao)
    {
        $request->validate([
            'comentarios_rejeicao' => 'required|string',
        ]);

        $alteracao->rejeitar($request->comentarios_rejeicao);

        return redirect()->route('alteracoes.show', $alteracao)
            ->with('success', 'Alteração rejeitada com sucesso!');
    }

    /**
     * Marcar como implementada
     */
    public function implementar(AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode implementar esta alteração
        if (!$user->podeImplementarAlteracoes()) {
            return redirect()->route('alteracoes.show', $alteracao)
                ->with('error', 'Você não tem permissão para implementar alterações. Apenas administradores e usuários dos setores técnicos podem implementar.');
        }

        if (!$alteracao->podeSerImplementada()) {
            return redirect()->route('alteracoes.show', $alteracao)
                ->with('error', 'Esta alteração não pode ser marcada como implementada!');
        }

        $alteracao->update(['status' => 'implementada']);

        return redirect()->route('alteracoes.show', $alteracao)
            ->with('success', 'Alteração marcada como implementada!');
    }

    /**
     * Gerar PDF da alteração
     */
    public function pdf(AlteracaoEletrica $alteracao)
    {
        $user = Auth::user();
        
        // Verificar se o usuário pode visualizar esta alteração
        if ($user->perfil !== 'admin' && !$user->is_super_admin) {
            if ($user->unit_id && $alteracao->unit_id !== $user->unit_id) {
                abort(403, 'Você não tem permissão para visualizar esta alteração.');
            }
        }
        
        // Carregar relacionamentos necessários
        $alteracao->load(['user', 'unit']);
        
        // Renderizar a view PDF
        $html = view('alteracoes.pdf', compact('alteracao'))->render();
        
        // Retornar como HTML para impressão/PDF em nova aba
        return response($html)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="' . $alteracao->numero_documento . '.html"')
            ->header('X-Frame-Options', 'SAMEORIGIN');
    }
}
