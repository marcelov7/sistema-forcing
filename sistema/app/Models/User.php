<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
}
