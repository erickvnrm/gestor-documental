<?php
 
namespace App\Models;
 
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    const ROLES = [
        self::ROLE_ADMIN => 'Administrador',
        self::ROLE_USER => 'Usuario',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->state === 'activo' && ($this->isAdmin() || $this->isUser());
    }

    public function isAdmin()
    {
        return  $this->role === self::ROLE_ADMIN;
    }

    public function isUser()
    {
        return  $this->role === self::ROLE_USER;
    }

    public function area()
    {
        return $this->belongsTo(Areas::class, 'area_id')->withTrashed();
    }

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'document',
        'phone',
        'email',
        'role',
        'state',
        'area_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }
 
}

