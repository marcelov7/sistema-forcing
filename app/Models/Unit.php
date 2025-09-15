<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'company',
        'description',
        'address',
        'city',
        'state',
        'phone',
        'email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relacionamento com usuários
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relacionamento com forcings
     */
    public function forcings()
    {
        return $this->hasMany(Forcing::class);
    }

    /**
     * Scope para unidades ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Accessor para nome completo
     */
    public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
}
