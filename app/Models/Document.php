<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'file_path',
    'file_type',
    'document_type',
    'size',
    'upload_date',
    'status',
    'user_id',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getUploaderNameAttribute()
  {
    return $this->user->name;
  }

  public function getUploaderUsernameAttribute()
  {
    return $this->user->username;
  }

  public function getUploadedByAttribute()
  {
    return $this->user->email;
  }
}
