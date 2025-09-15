<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Http\Middleware\CheckProfile;
use App\Http\Middleware\SuperAdminMiddleware;

/**
 * 🎨 UserController com Acessibilidade para Daltonicos
 * Sistema de Forcing - Devaxis
 * 
 * Funcionalidades:
 * - Gerenciamento completo de usuários
 * - Acessibilidade para daltonicos
 * - Middleware de autorização
 * - Logs de auditoria
 */
class UserController extends Controller
{
    /**
     * 🔧 Configurar middlewares no construtor
     */
    public function __construct()
    {
        // Middleware para todas as ações
        $this->middleware('auth');
        
        // Middleware específico por ação
        $this->middleware([CheckProfile::class . ':admin'])->only([
            'index', 'show', 'edit', 'update', 'destroy'
        ]);
        
        $this->middleware([SuperAdminMiddleware::class])->only([
            'create', 'store', 'promote', 'demote'
        ]);
    }

    /**
     * 📋 Exibir lista de usuários com acessibilidade
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // 🔍 Filtros de pesquisa
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('empresa', 'like', "%{$search}%");
                });
            }
            
            if ($request->filled('perfil')) {
                $query->where('perfil', $request->perfil);
            }
            
            if ($request->filled('empresa')) {
                $query->where('empresa', $request->empresa);
            }
            
            // 📊 Ordenação padrão
            $query->orderBy('name');
            
            // 📄 Paginação
            $users = $query->paginate(15);
            
            // 📈 Estatísticas para dashboard
            $stats = [
                'total' => User::count(),
                'admins' => User::where('perfil', 'admin')->count(),
                'liberadores' => User::where('perfil', 'liberador')->count(),
                'executantes' => User::where('perfil', 'executante')->count(),
                'usuarios' => User::where('perfil', 'user')->count(),
                'ativos' => User::whereNotNull('email_verified_at')->count(),
                'inativos' => User::whereNull('email_verified_at')->count(),
            ];
            
            // 🎨 Preferências de acessibilidade do usuário
            $accessibilityPrefs = $this->getAccessibilityPreferences();
            
            Log::info('Lista de usuários acessada', [
                'user_id' => Auth::id(),
                'filters' => $request->only(['search', 'perfil', 'empresa']),
                'total_results' => $users->total(),
                'accessibility_mode' => $accessibilityPrefs['colorblind_mode'] ?? false
            ]);
            
            return view('users.index', compact('users', 'stats', 'accessibilityPrefs'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao listar usuários', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', '❌ Erro ao carregar lista de usuários: ' . $e->getMessage());
        }
    }

    /**
     * 👤 Exibir detalhes do usuário
     */
    public function show(User $user)
    {
        try {
            // 📊 Estatísticas do usuário
            $userStats = [
                'forcings_solicitados' => $user->forcingsSolicitados()->count(),
                'forcings_liberados' => $user->forcingsLiberados()->count(),
                'forcings_executados' => $user->forcingsExecutados()->count(),
                'ultimo_acesso' => $user->last_login_at ?? $user->created_at,
                'membro_desde' => $user->created_at->format('d/m/Y'),
                'dias_ativo' => $user->created_at->diffInDays(now()),
            ];
            
            // 🎨 Preferências de acessibilidade
            $accessibilityPrefs = $this->getAccessibilityPreferences();
            
            Log::info('Detalhes do usuário visualizados', [
                'viewed_user_id' => $user->id,
                'viewer_user_id' => Auth::id(),
                'accessibility_mode' => $accessibilityPrefs['colorblind_mode'] ?? false
            ]);
            
            return view('users.show', compact('user', 'userStats', 'accessibilityPrefs'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao exibir usuário', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'viewer_id' => Auth::id()
            ]);
            
            return redirect()->route('users.index')
                ->with('error', '❌ Erro ao carregar usuário: ' . $e->getMessage());
        }
    }

    /**
     * ✏️ Formulário de edição do usuário
     */
    public function edit(User $user)
    {
        try {
            // 🛡️ Verificar se pode editar este usuário
            if (!$this->canEditUser($user)) {
                Log::warning('Tentativa de edição não autorizada', [
                    'target_user_id' => $user->id,
                    'editor_user_id' => Auth::id()
                ]);
                
                return redirect()->route('users.index')
                    ->with('error', '❌ Você não tem permissão para editar este usuário.');
            }
            
            // 📋 Opções de perfil baseadas no usuário logado
            $perfilOptions = $this->getAvailableProfiles();
            
            // 🏢 Lista de empresas para seleção
            $empresas = User::distinct('empresa')
                ->whereNotNull('empresa')
                ->pluck('empresa')
                ->sort()
                ->values();
            
            // 🎨 Preferências de acessibilidade
            $accessibilityPrefs = $this->getAccessibilityPreferences();
            
            Log::info('Formulário de edição acessado', [
                'target_user_id' => $user->id,
                'editor_user_id' => Auth::id(),
                'accessibility_mode' => $accessibilityPrefs['colorblind_mode'] ?? false
            ]);
            
            return view('users.edit', compact('user', 'perfilOptions', 'empresas', 'accessibilityPrefs'));
            
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de edição', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'editor_id' => Auth::id()
            ]);
            
            return redirect()->route('users.index')
                ->with('error', '❌ Erro ao carregar formulário: ' . $e->getMessage());
        }
    }

    /**
     * 💾 Atualizar usuário
     */
    public function update(Request $request, User $user)
    {
        try {
            // 🛡️ Verificar se pode editar este usuário
            if (!$this->canEditUser($user)) {
                Log::warning('Tentativa de atualização não autorizada', [
                    'target_user_id' => $user->id,
                    'editor_user_id' => Auth::id()
                ]);
                
                return redirect()->route('users.index')
                    ->with('error', '❌ Você não tem permissão para editar este usuário.');
            }
            
            // ✅ Validação dos dados
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'empresa' => ['required', 'string', 'max:255'],
                'setor' => ['required', 'string', 'max:255'],
                'perfil' => ['required', Rule::in($this->getAvailableProfiles())],
                'password' => ['nullable', 'min:8', 'confirmed'],
                'accessibility_preferences' => ['nullable', 'array'],
                'accessibility_preferences.colorblind_mode' => ['boolean'],
                'accessibility_preferences.high_contrast' => ['boolean'],
                'accessibility_preferences.show_icons' => ['boolean'],
                'accessibility_preferences.font_size' => [Rule::in(['small', 'normal', 'large'])],
            ]);
            
            // 📝 Preparar dados para atualização
            $updateData = collect($validated)->except(['password', 'accessibility_preferences'])->toArray();
            
            // 🔐 Atualizar senha se fornecida
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }
            
            // 🎨 Atualizar preferências de acessibilidade
            if ($request->has('accessibility_preferences')) {
                $updateData['preferences'] = array_merge(
                    $user->preferences ?? [],
                    $request->accessibility_preferences
                );
            }
            
            // 💾 Atualizar usuário
            $user->update($updateData);
            
            Log::info('Usuário atualizado com sucesso', [
                'updated_user_id' => $user->id,
                'editor_user_id' => Auth::id(),
                'changes' => array_keys($updateData),
                'accessibility_updated' => $request->has('accessibility_preferences')
            ]);
            
            return redirect()->route('users.show', $user)
                ->with('success', '✅ Usuário atualizado com sucesso!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Erro de validação na atualização do usuário', [
                'user_id' => $user->id,
                'errors' => $e->errors(),
                'editor_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'editor_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', '❌ Erro ao atualizar usuário: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 🎨 Obter preferências de acessibilidade do usuário atual
     */
    private function getAccessibilityPreferences(): array
    {
        $user = Auth::user();
        
        return [
            'colorblind_mode' => $user->preferences['colorblind_mode'] ?? false,
            'high_contrast' => $user->preferences['high_contrast'] ?? false,
            'show_icons' => $user->preferences['show_icons'] ?? true,
            'font_size' => $user->preferences['font_size'] ?? 'normal',
            'accessibility_enabled' => $user->preferences['accessibility_enabled'] ?? false,
        ];
    }

    /**
     * 🎨 Atualizar preferências de acessibilidade (AJAX)
     */
    public function updateAccessibilityPreferences(Request $request)
    {
        try {
            $user = Auth::user();
            
            $preferences = $request->validate([
                'colorblind_mode' => 'boolean',
                'high_contrast' => 'boolean',
                'show_icons' => 'boolean',
                'font_size' => Rule::in(['small', 'normal', 'large']),
                'accessibility_enabled' => 'boolean',
            ]);
            
            // 💾 Mesclar com preferências existentes
            $user->preferences = array_merge($user->preferences ?? [], $preferences);
            $user->save();
            
            Log::info('Preferências de acessibilidade atualizadas', [
                'user_id' => $user->id,
                'preferences' => $preferences
            ]);
            
            return response()->json([
                'success' => true,
                'message' => '✅ Preferências de acessibilidade salvas com sucesso!',
                'preferences' => $this->getAccessibilityPreferences()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar preferências de acessibilidade', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Erro ao salvar preferências: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🧪 Teste de daltonismo (AJAX)
     */
    public function colorblindnessTest(Request $request)
    {
        try {
            $results = $request->validate([
                'test_results' => 'required|array',
                'test_results.*' => 'integer|min:0|max:10'
            ]);
            
            // 📊 Analisar resultados do teste
            $totalCorrect = array_sum($results['test_results']);
            $totalQuestions = count($results['test_results']);
            $percentage = ($totalCorrect / $totalQuestions) * 100;
            
            // 🎯 Determinar tipo de daltonismo
            $colorblindType = 'normal';
            $recommendations = [];
            
            if ($percentage < 70) {
                if ($results['test_results'][0] + $results['test_results'][1] < 2) {
                    $colorblindType = 'protanopia';
                    $recommendations = [
                        'Ativar modo de alto contraste',
                        'Usar símbolos junto com cores',
                        'Preferir azul e amarelo'
                    ];
                } elseif ($results['test_results'][2] + $results['test_results'][3] < 2) {
                    $colorblindType = 'deuteranopia';
                    $recommendations = [
                        'Ativar padrões visuais',
                        'Usar ícones descritivos',
                        'Preferir azul e laranja'
                    ];
                } else {
                    $colorblindType = 'tritanopia';
                    $recommendations = [
                        'Ativar modo de alto contraste',
                        'Usar vermelho e verde',
                        'Evitar azul e amarelo'
                    ];
                }
            }
            
            // 💾 Salvar resultado no perfil do usuário
            $user = Auth::user();
            $user->preferences = array_merge($user->preferences ?? [], [
                'colorblind_test_result' => $colorblindType,
                'colorblind_test_date' => now(),
                'colorblind_mode' => $colorblindType !== 'normal',
                'accessibility_enabled' => true
            ]);
            $user->save();
            
            Log::info('Teste de daltonismo realizado', [
                'user_id' => $user->id,
                'test_score' => $percentage,
                'colorblind_type' => $colorblindType,
                'auto_enabled_accessibility' => $colorblindType !== 'normal'
            ]);
            
            return response()->json([
                'success' => true,
                'colorblind_type' => $colorblindType,
                'percentage' => $percentage,
                'recommendations' => $recommendations,
                'accessibility_enabled' => $colorblindType !== 'normal'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro no teste de daltonismo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '❌ Erro ao processar teste: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🛡️ Verificar se pode editar o usuário
     */
    private function canEditUser(User $user): bool
    {
        $currentUser = Auth::user();
        
        // Super admin pode editar qualquer um
        if ($currentUser->is_super_admin) {
            return true;
        }
        
        // Admin pode editar usuários não-admin
        if ($currentUser->perfil === 'admin' && !$user->is_super_admin && $user->perfil !== 'admin') {
            return true;
        }
        
        // Usuário pode editar apenas a si mesmo
        return $currentUser->id === $user->id;
    }

    /**
     * 📋 Obter perfis disponíveis baseado no usuário atual
     */
    private function getAvailableProfiles(): array
    {
        $currentUser = Auth::user();
        
        if ($currentUser->is_super_admin) {
            return ['admin', 'liberador', 'executante', 'user'];
        }
        
        if ($currentUser->perfil === 'admin') {
            return ['liberador', 'executante', 'user'];
        }
        
        // Usuário comum só pode manter seu próprio perfil
        return [$currentUser->perfil];
    }
}
