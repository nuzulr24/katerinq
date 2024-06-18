<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Seller\Entities\ProductModel;
use App\Models\User;

class OrderHistoryModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_order_detail';
    protected $fillable = [
        'id', 'seller_id', 'buy_id', 'order_id', 'product_id', 'is_status',
        'comment', 'price', 'tanggal'
    ];

    protected $keyType = "string";
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function product()
    {
        return $this->hasOne(ProductModel::class, 'id', 'product_id');
    }

    public function client()
    {
        return $this->hasOne(User::class, 'id', 'buy_id');
    }

    public static function getTotalIncomeSeller()
    {
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
            'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        $result = collect($months)->mapWithKeys(function ($month, $monthName) {
            $totalAmount = self::whereMonth('created_at', $month)
                ->whereYear('created_at', now()->year)
                ->where('seller_id', user()->id)
                ->sum('price');

            return [$monthName => $totalAmount];
        });

        return $result;
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M Y H:i', strtotime($value));
    }
}
