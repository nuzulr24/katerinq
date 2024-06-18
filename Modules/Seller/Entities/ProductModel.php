<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Seller;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_product';
    protected $fillable = [
        'id', 'user_id', 'name', 'description',
        'is_role', 'is_type', 'is_delivery_time', 'is_price',
        'is_status', 'thumbnail'
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function user()
    {
        return $this->hasOne(AccountModel::class, 'id', 'user_id');
    }

    public function merchant()
    {
        return $this->hasOne(Seller::class, 'user_id', 'user_id');
    }
}
