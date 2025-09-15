<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Aplicar middlewares no construtor
     */
    public function __construct()
    {
        // Middleware de autenticação para todas as rotas
        $this->middleware('auth');
        
        // Middleware de admin para operações administrativas
        $this->middleware('check.profile:admin')->only([
            'index', 'create', 'store', 'destroy'
        ]);
    }
    /**
     * Exibe a lista de usuários (protegido por middleware admin)
     */
    public function index()
    {
        try {
            $users = User::with('unit')->orderBy('name')->paginate(20);
            
            Log::info('Lista de usuários acessada', [
                'admin_id' => Auth::id(),
                'admin_email' => Auth::user()->email
            ]);
            
            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar lista de usuários', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao carregar a lista de usuários.');
        }
    }

    /**
     * Exibe o formulário para criar novo usuário (protegido por middleware admin)
     */
    public function create()
    {
        try {
            // Buscar unidades ativas para o dropdown
            $units = Unit::where('active', true)->orderBy('name')->get();
            
            return view('users.create', compact('units'));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar formulário de criação de usuário', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->route('users.index')
                ->with('error', 'Erro ao carregar o formulário.');
        }
    }

    /**
     * Armazena um novo usuário (protegido por middleware admin)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'empresa' => 'required|string|max:255',
                'setor' => 'required|string|max:255',
                'perfil' => 'required|in:user,liberador,executante,admin',
                'unit_id' => 'required|exists:units,id',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'empresa' => $validated['empresa'],
                'setor' => $validated['setor'],
                'perfil' => $validated['perfil'],
                'unit_id' => $validated['unit_id'],
                'is_super_admin' => false, // Garantir que não seja Super Admin
            ]);

            Log::info('Usuário criado', [
                'created_user_id' => $user->id,
                'created_user_email' => $user->email,
                'created_by_admin' => Auth::id(),
                'admin_email' => Auth::user()->email
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Usuário criado com sucesso!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao criar usuário', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'request_data' => $request->except(['password'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao criar usuário.')
                ->withInput();
        }
    }

    /**
     * Exibe os detalhes de um usuário específico
     */
    public function show(User $user)
    {
        // Autorização: admin pode ver qualquer usuário, usuário pode ver apenas a si mesmo
        if (!$this->canAccessUser($user)) {
            abort(403, 'Acesso negado');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Exibe o formulário para editar um usuário
     */
    public function edit(User $user)
    {
        // Autorização: admin pode editar qualquer usuário, usuário pode editar apenas a si mesmo
        if (!$this->canAccessUser($user)) {
            abort(403, 'Acesso negado');
        }

        // Carregar unidades para o dropdown (apenas para admins)
        $units = collect();
        if (Auth::user()->isAdmin()) {
            $units = Unit::where('active', true)->orderBy('name')->get();
        }

        return view('users.edit', compact('user', 'units'));
    }

    /**
     * Atualiza um usuário
     */
    public function update(Request $request, User $user)
    {
        // Autorização: admin pode editar qualquer usuário, usuário pode editar apenas a si mesmo
        if (!$this->canAccessUser($user)) {
            abort(403, 'Acesso negado');
        }

        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'empresa' => 'required|string|max:255',
                'setor' => 'required|string|max:255',
            ];

            // Apenas admin pode alterar perfil e unidade
            if (Auth::user()->isAdmin()) {
                $rules['perfil'] = 'required|in:user,liberador,executante,admin';
                $rules['unit_id'] = 'required|exists:units,id';
            }

            // Se senha foi fornecida
            if ($request->filled('password')) {
                $rules['password'] = 'string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'empresa' => $validated['empresa'],
                'setor' => $validated['setor'],
            ];

            // Apenas admin pode alterar perfil e unidade
            if (Auth::user()->isAdmin()) {
                $data['perfil'] = $validated['perfil'];
                $data['unit_id'] = $validated['unit_id'];
            }

            // Se senha foi fornecida, atualize
            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            Log::info('Usuário atualizado', [
                'updated_user_id' => $user->id,
                'updated_by' => Auth::id(),
                'is_self_update' => Auth::id() === $user->id,
                'updated_fields' => array_keys($data)
            ]);

            $redirectRoute = Auth::user()->isAdmin() ? 'users.index' : 'profile.show';
            return redirect()->route($redirectRoute)
                ->with('success', 'Usuário atualizado com sucesso!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar usuário', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'updated_by' => Auth::id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao atualizar usuário.')
                ->withInput();
        }
    }

    /**
     * Remove um usuário (protegido por middleware admin)
     */
    public function destroy(User $user)
    {
        try {
            // Não permite deletar o próprio usuário
            if (Auth::id() === $user->id) {
                return redirect()->route('users.index')
                    ->with('error', 'Você não pode deletar sua própria conta!');
            }

            // Não permite deletar super admin
            if ($user->isSuperAdmin()) {
                return redirect()->route('users.index')
                    ->with('error', 'Não é possível deletar um Super Administrador!');
            }

            $deletedUserInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'perfil' => $user->perfil
            ];

            $user->delete();

            Log::warning('Usuário deletado', [
                'deleted_user' => $deletedUserInfo,
                'deleted_by_admin' => Auth::id(),
                'admin_email' => Auth::user()->email
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Usuário removido com sucesso!');
                
        } catch (\Exception $e) {
            Log::error('Erro ao deletar usuário', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'admin_id' => Auth::id()
            ]);
            
            return redirect()->route('users.index')
                ->with('error', 'Erro ao remover usuário.');
        }
    }

    /**
     * Exibe o perfil do usuário autenticado
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Exibe o formulário para editar o perfil do usuário autenticado
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Atualiza o perfil do usuário autenticado
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'empresa' => 'required|string|max:255',
                'setor' => 'required|string|max:255',
            ];

            // Adicionar validação de senha apenas se fornecida
            if ($request->filled('password')) {
                $rules['password'] = 'required|string|min:8|confirmed';
            }

            $validated = $request->validate($rules);

            $data = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'empresa' => $validated['empresa'],
                'setor' => $validated['setor'],
            ];

            // Atualizar senha apenas se fornecida
            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            $user->update($data);

            Log::info('Perfil atualizado pelo próprio usuário', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($data)
            ]);

            return redirect()->route('profile.show')
                ->with('success', 'Perfil atualizado com sucesso!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return redirect()->back()
                ->with('error', 'Erro ao atualizar perfil.')
                ->withInput();
        }
    }

    /**
     * Verifica se o usuário atual pode acessar/editar outro usuário
     */
    private function canAccessUser(User $user): bool
    {
        $currentUser = Auth::user();
        
        // Admin pode acessar qualquer usuário
        if ($currentUser->isAdmin()) {
            return true;
        }
        
        // Usuário pode acessar apenas a si mesmo
        return $currentUser->id === $user->id;
    }
}
