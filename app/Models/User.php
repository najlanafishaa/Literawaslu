<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
// Removed unsupported Eloquent attributes for Laravel 11
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'security_question', 'security_answer'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the member reset requests.
     */
    public function memberResetRequests()
    {
        return $this->hasMany(MemberResetRequest::class);
    }

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

    /**
     * Get the member profile associated with the user (if any).
     */
    public function member()
    {
        return $this->hasOne(Member::class);
    }
}
