<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'email',
        'password',
        'role',
        'company_id',
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
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCompanyUser(): bool
    {
        return $this->role === 'company';
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }


    public function scopeCompanyUsers($query)
    {
        return $query->where('role', 'company');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    protected static function boot()
    {
        parent::boot();

        // Ensure admin users don't have a company_id
        static::saving(function ($user) {
            if ($user->role === 'admin') {
                $user->company_id = null;
            }
        });
    }
}
