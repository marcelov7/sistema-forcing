<?php

// Exemplo de uso do SuperAdminMiddleware em rotas
// Este arquivo é apenas para demonstração

/*
// Em routes/web.php

// Rota individual com middleware super admin
Route::get('/super-admin/dashboard', function () {
    return view('super-admin.dashboard');
})->middleware('super.admin');

// Grupo de rotas para super admin
Route::middleware(['auth', 'super.admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super.admin.dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('super.admin.users');
    Route::get('/system', [SuperAdminController::class, 'system'])->name('super.admin.system');
    Route::get('/logs', [SuperAdminController::class, 'logs'])->name('super.admin.logs');
});

// Verificação em controladores
class SomeController extends Controller
{
    public function someMethod()
    {
        // Verificar se o usuário é super admin
        if (!SuperAdminMiddleware::canAccess()) {
            abort(403, 'Acesso negado');
        }
        
        // Código para super admin...
    }
}
*/

echo "📝 Exemplo de configuração de rotas criado!\n";
echo "Veja o arquivo exemplo_rotas_super_admin.php para referência.\n";
