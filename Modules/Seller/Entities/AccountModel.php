<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_users';
    protected $fillable = ['id','password','username','name','email','email_verified_token','email_verified_at','phone','income','balance','campaign','remember_token','is_close_guide','is_buzzer','thumbnail','level', 'is_buzzer_gender', 'is_advertiser'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public $timestamps = false;
}
