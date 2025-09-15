<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'empresa',
        'setor',
        'perfil',
        'unit_id',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relacionamento com forcing criados pelo usuário
     */
    public function forcings()
    {
        return $this->hasMany(Forcing::class, 'user_id');
    }

    /**
     * Relacionamento com aceites de termos
     */
    public function termsAcceptances()
    {
        return $this->hasMany(TermsAcceptance::class);
    }

    /**
     * Relacionamento com unidade
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Verifica se é super admin
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Verifica se pode gerenciar unidade específica
     */
    public function canManageUnit($unitId)
    {
        return $this->isSuperAdmin() || $this->unit_id == $unitId;
    }

    /**
     * Relacionamento com forcing criados pelo usuário (alias)
     */
    public function forcingsCreated()
    {
        return $this->hasMany(Forcing::class);
    }

    /**
     * Relacionamento com forcing liberados pelo usuário
     */
    public function forcingsLiberados()
    {
        return $this->hasMany(Forcing::class, 'liberador_id');
    }

    /**
     * Relacionamento com forcing executados pelo usuário
     */
    public function forcingsExecutados()
    {
        return $this->hasMany(Forcing::class, 'executante_id');
    }

    /**
     * Relacionamento com forcing retirados pelo usuário (liberador)
     */
    public function forcingsRetirados()
    {
        return $this->hasMany(Forcing::class, 'liberador_id')->where('status', 'retirado');
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin()
    {
        return $this->perfil === 'admin';
    }

    /**
     * Verifica se o usuário é liberador
     */
    public function isLiberador()
    {
        return $this->perfil === 'liberador';
    }

    /**
     * Verifica se o usuário é executante
     */
    public function isExecutante()
    {
        return $this->perfil === 'executante';
    }

    /**
     * Verifica se o usuário é user comum
     */
    public function isUser()
    {
        return $this->perfil === 'user';
    }

    /**
     * Verifica se o usuário pode aprovar como Gerente de Manutenção
     */
    public function podeAprovarComoGerente()
    {
        // Super admin sempre pode
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Usuários do setor "Manutenção" podem aprovar como gerente
        return strtolower($this->setor) === 'manutenção' || 
               strtolower($this->setor) === 'manutencao';
    }

    /**
     * Verifica se o usuário pode aprovar como Coordenador de Manutenção
     */
    public function podeAprovarComoCoordenador()
    {
        // Super admin sempre pode
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Usuários do setor "Manutenção" podem aprovar como coordenador
        return strtolower($this->setor) === 'manutenção' || 
               strtolower($this->setor) === 'manutencao';
    }

    /**
     * Verifica se o usuário pode aprovar como Técnico Especialista
     */
    public function podeAprovarComoTecnico()
    {
        // Super admin sempre pode
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Usuários dos setores técnicos podem aprovar como técnico
        $setoresTecnicos = [
            'automação', 'automacao', 
            'elétrica', 'eletrica', 
            'instrumentação', 'instrumentacao', 
            'técnico', 'tecnico',
            'técnico eletricista', 'tecnico eletricista'
        ];
        return in_array(strtolower($this->setor), $setoresTecnicos);
    }

    /**
     * Retorna os tipos de aprovação que o usuário pode realizar
     */
    public function tiposAprovacaoPermitidos()
    {
        $tipos = [];
        
        if ($this->podeAprovarComoGerente()) {
            $tipos[] = 'gerente';
        }
        
        if ($this->podeAprovarComoCoordenador()) {
            $tipos[] = 'coordenador';
        }
        
        if ($this->podeAprovarComoTecnico()) {
            $tipos[] = 'tecnico';
        }
        
        return $tipos;
    }

    /**
     * Verifica se o usuário pode implementar alterações elétricas
     */
    public function podeImplementarAlteracoes()
    {
        // Super admin sempre pode
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        // Administradores podem implementar
        if ($this->perfil === 'admin') {
            return true;
        }
        
        // Usuários dos setores técnicos podem implementar
        return $this->podeAprovarComoTecnico();
    }
}
