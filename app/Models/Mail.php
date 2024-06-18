<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    use HasFactory;

    protected $table = "tbl_smtp";
    protected $fillable = ['id','protocol','host','port','username','password','sender','is_active'];
    protected $hidden = ['updated_at'];
    public $timestamps = false;
}
