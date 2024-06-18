<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserManager extends Model
{
    use HasFactory;
    protected $table = 'password_resets';
    protected $fillable = [
        'uuid',
        'email',
        'token',
        'created_at',
        'isUsed'
    ];
    protected $primaryKey = 'uuid';
    protected $hidden = ['created_at'];
    public $timestamps = false;
}
