<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawalModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_withdrawal';
    protected $fillable = [
        'id', 'user_id', 'amount', 'is_status', 'is_account',
        'created_at', 'updated_at', 'invoice_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function user()
    {
        return $this->hasMany(AccountModel::class, 'id', 'user_id');
    }
    
    public function rekening()
    {
        return $this->hasMany(RekeningModel::class, 'id', 'is_account');
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }
}
