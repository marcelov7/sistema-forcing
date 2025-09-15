# 🔧 Criar SuperAdmin no Sistema

Execute estes comandos no servidor para criar um superadmin:

## 0. Verificar unidades existentes
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# Verificar unidades disponíveis
php artisan tinker --execute="
\$units = App\Models\Unit::all();
if(\$units->count() > 0) {
    echo 'Unidades encontradas:' . PHP_EOL;
    foreach(\$units as \$unit) {
        echo 'ID: ' . \$unit->id . ' - Código: ' . \$unit->code . ' - Nome: ' . \$unit->name . PHP_EOL;
    }
} else {
    echo 'Criando unidade padrão...' . PHP_EOL;
    \$unit = App\Models\Unit::create([
        'code' => 'UND001',
        'name' => 'Unidade Principal',
        'company' => 'DevAxis',
        'city' => 'DevAxis',
        'state' => 'SP'
    ]);
    echo 'Unidade criada com ID: ' . \$unit->id . PHP_EOL;
}
"
```

## 1. Criar SuperAdmin via Tinker
```bash
# Criar superadmin (corrigindo nome das colunas)
php artisan tinker --execute="
\$unit = App\Models\Unit::first();
if(is_null(\$unit)) {
    \$unit = App\Models\Unit::create([
        'code' => 'UND001',
        'name' => 'Unidade Principal',
        'company' => 'DevAxis', 
        'city' => 'DevAxis',
        'state' => 'SP'
    ]);
}

\$user = new App\Models\User();
\$user->name = 'Super Administrador';
\$user->email = 'superadmin@devaxis.com.br';
\$user->username = 'superadmin';
\$user->password = Hash::make('SuperAdmin123');
\$user->empresa = 'DevAxis';
\$user->setor = 'TI';
\$user->perfil = 'superadmin';
\$user->unit_id = \$unit->id;
\$user->save();
echo 'SuperAdmin criado com sucesso na unidade: ' . \$unit->name . ' (ID: ' . \$unit->id . ')';
"
```

## 2. Verificar se foi criado
```bash
php artisan tinker --execute="
\$superadmin = App\Models\User::where('perfil', 'superadmin')->first();
if(\$superadmin) {
    echo 'SuperAdmin encontrado: ' . \$superadmin->username . ' (' . \$superadmin->email . ')';
} else {
    echo 'SuperAdmin não encontrado!';
}
"
```

## 3. Listar todos os usuários
```bash
php artisan tinker --execute="
\$users = App\Models\User::all();
foreach(\$users as \$user) {
    echo \$user->username . ' - ' . \$user->perfil . ' - ' . \$user->email . PHP_EOL;
}
"
```

---

## 📋 CREDENCIAIS DO SUPERADMIN:
- **Username:** `superadmin`
- **Email:** `superadmin@devaxis.com.br`
- **Senha:** `SuperAdmin123`
- **Perfil:** `superadmin`
- **Empresa:** `DevAxis`
- **Setor:** `TI`

## 🎯 TESTE DE LOGIN:
1. Acesse: `https://forcing.devaxis.com.br/login`
2. Use as credenciais acima
3. Verifique se tem acesso total ao sistema

## 📝 COMANDOS ALTERNATIVOS:

### Criar via comando direto:
```bash
php artisan tinker --execute="App\Models\User::create(['name' => 'Super Admin', 'email' => 'superadmin@devaxis.com.br', 'username' => 'superadmin', 'password' => Hash::make('SuperAdmin123!'), 'empresa' => 'DevAxis', 'setor' => 'TI', 'perfil' => 'superadmin', 'unit_id' => 1]);"
```

### Alterar perfil de usuário existente:
```bash
php artisan tinker --execute="App\Models\User::where('username', 'admin')->update(['perfil' => 'superadmin']);"
```
