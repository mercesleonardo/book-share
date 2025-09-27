<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'description',
        'role',
        'password_set_at',
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
            'password'          => 'hashed',
            'role'              => \App\Enums\UserRole::class,
            'password_set_at'   => 'datetime',
        ];
    }

    /**
     * Indicates if the user has set their own password.
     */
    public function hasSetPassword(): bool
    {
        return !is_null($this->password_set_at);
    }

    /**
     * Users posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Whether user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === \App\Enums\UserRole::ADMIN;
    }

    /**
     * Whether user has moderator role.
     */
    public function isModerator(): bool
    {
        return $this->role === \App\Enums\UserRole::MODERATOR;
    }
}
