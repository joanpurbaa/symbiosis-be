<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
  use HasFactory, Notifiable;

  protected $fillable = [
    'name',
    'email',
    'username',
    'password',
    'position',
    'company_address',
    'work_field',
    'role',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function documents()
  {
    return $this->hasMany(Document::class);
  }

  public function isAdmin()
  {
    return $this->role === 'admin';
  }

  public function isUser()
  {
    return $this->role === 'user';
  }
}
