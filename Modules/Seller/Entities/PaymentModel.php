<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Modules\Seller\Entities\AccountModel;

class PaymentModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_deposit';
    protected $fillable = [
        'id', 'user_id', 'deposit_number', 'payment_method',
        'methodWith', 'amount', 'processing_fee', 'total',
        'is_status', 'created_at', 'updated_at', 'urlRedirect',
        'is_reference'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->hasMany(AccountModel::class, 'id', 'user_id');
    }
}
