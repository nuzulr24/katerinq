<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Roles extends Model
{
    use HasFactory;
    protected $table = 'tbl_role';
    protected $fillable = ['id','name'];
    public $timestamps = false;
}
