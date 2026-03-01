<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';

    protected $primaryKey = 'idusu';

    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'clave',
        'cargo_usu',
        'nombres',
        'email',
        'telefono',
        'fechaingreso',
        'tipo',
        'estado',
    ];

    protected $hidden = [
        'clave',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'idusu';
    }

    /**
     * Get the password for the user (legacy: MD5 or bcrypt).
     */
    public function getAuthPassword(): string
    {
        return $this->clave;
    }

    /**
     * Check if stored password is legacy MD5 (32 hex chars).
     */
    public function hasLegacyPassword(): bool
    {
        $clave = $this->getAuthPassword();
        return strlen($clave) === 32 && ctype_xdigit($clave);
    }

    /**
     * Table usuario has no remember_token column (legacy).
     */
    public function getRememberTokenName(): ?string
    {
        return null;
    }
}
