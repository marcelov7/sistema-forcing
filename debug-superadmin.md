# 🔍 Investigar Problema do SuperAdmin

Execute estes comandos no servidor para investigar:

## 1. Verificar se o banco existe e tem dados
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# Verificar arquivo do banco
ls -la database/database.sqlite

# Verificar se as tabelas existem
php artisan tinker --execute="
echo 'Verificando tabelas...' . PHP_EOL;
try {
    echo 'Users: ' . DB::table('users')->count() . ' registros' . PHP_EOL;
    echo 'Units: ' . DB::table('units')->count() . ' registros' . PHP_EOL;
    echo 'Forcing: ' . DB::table('forcing')->count() . ' registros' . PHP_EOL;
} catch(Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . PHP_EOL;
}
"
```

## 2. Listar TODOS os usuários existentes
```bash
php artisan tinker --execute="
\$users = App\Models\User::all();
echo 'Total de usuários: ' . \$users->count() . PHP_EOL;
foreach(\$users as \$user) {
    echo 'ID: ' . \$user->id . ' | Username: ' . \$user->username . ' | Email: ' . \$user->email . ' | Perfil: ' . \$user->perfil . ' | Unit: ' . \$user->unit_id . PHP_EOL;
}
"
```

## 3. Verificar seeders padrão
```bash
php artisan tinker --execute="
echo 'Verificando usuários específicos:' . PHP_EOL;
\$usuarios = ['admin', 'liberador', 'executante', 'usuario', 'superadmin'];
foreach(\$usuarios as \$username) {
    \$user = App\Models\User::where('username', \$username)->first();
    if(\$user) {
        echo \$username . ': EXISTE - Perfil: ' . \$user->perfil . PHP_EOL;
    } else {
        echo \$username . ': NÃO EXISTE' . PHP_EOL;
    }
}
"
```

## 4. Verificar unidades
```bash
php artisan tinker --execute="
\$units = App\Models\Unit::all();
echo 'Total de unidades: ' . \$units->count() . PHP_EOL;
foreach(\$units as \$unit) {
    echo 'ID: ' . \$unit->id . ' | Code: ' . \$unit->code . ' | Name: ' . \$unit->name . ' | Company: ' . \$unit->company . PHP_EOL;
}
"
```

## 5. Tentar criar SuperAdmin de forma mais simples
```bash
php artisan tinker --execute="
try {
    // Primeiro garantir que existe uma unidade
    \$unit = App\Models\Unit::first();
    if(!\\$unit) {
        echo 'Criando unidade...' . PHP_EOL;
        \$unit = App\Models\Unit::create([
            'code' => 'DEV001',
            'name' => 'DevAxis Principal',
            'company' => 'DevAxis Tecnologia',
            'city' => 'São Paulo',
            'state' => 'SP'
        ]);
        echo 'Unidade criada: ID ' . \$unit->id . PHP_EOL;
    } else {
        echo 'Unidade encontrada: ID ' . \$unit->id . PHP_EOL;
    }
    
    // Tentar criar o superadmin
    \$existingUser = App\Models\User::where('username', 'superadmin')->first();
    if(\$existingUser) {
        echo 'SuperAdmin já existe! Atualizando...' . PHP_EOL;
        \$existingUser->perfil = 'superadmin';
        \$existingUser->save();
        echo 'SuperAdmin atualizado.' . PHP_EOL;
    } else {
        echo 'Criando novo SuperAdmin...' . PHP_EOL;
        \$superadmin = App\Models\User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@devaxis.com.br',
            'username' => 'superadmin',
            'password' => Hash::make('SuperAdmin123'),
            'empresa' => 'DevAxis',
            'setor' => 'TI',
            'perfil' => 'superadmin',
            'unit_id' => \$unit->id
        ]);
        echo 'SuperAdmin criado com ID: ' . \$superadmin->id . PHP_EOL;
    }
} catch(Exception \$e) {
    echo 'ERRO: ' . \$e->getMessage() . PHP_EOL;
}
"
```

---

## 🎯 EXECUTE NA ORDEM:
1. Comando 1: Verificar banco
2. Comando 2: Listar usuários  
3. Comando 4: Verificar unidades
4. Comando 5: Criar SuperAdmin de forma robusta
