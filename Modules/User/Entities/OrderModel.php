<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Seller\Entities\AccountModel;
use Modules\Seller\Entities\SitesModel;
use Modules\User\Entities\OrderHistoryModel;
use Carbon\Carbon;

class OrderModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_order';
    protected $fillable = [
        'id', 'user_id', 'invoice_number', 'price',
        'url_payment', 'is_status'
    ];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->hasOne(AccountModel::class, 'id', 'buy_id');
    }

    public function seller()
    {
        return $this->hasOne(AccountModel::class, 'id', 'sell_id');
    }

    public function history()
    {
        return $this->hasMany(OrderHistoryModel::class, 'order_id', 'id')->orderBy('created_at', 'desc')->limit(5);
    }

    public static function getTotalAmountByMonth()
    {
        $months = [
            'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
            'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        ];

        $result = collect($months)->mapWithKeys(function ($month, $monthName) {
            $totalAmount = self::whereMonth('created_at', $month)
                ->whereYear('created_at', now()->year)
                ->sum('price');

            return [$monthName => $totalAmount];
        });

        return $result;
    }

    protected $appends = ['seller_email']; // Kolom virtual

    public function getSellerEmailAttribute()
    {
        // Di sini Anda dapat mengembalikan nilai yang sesuai
        // berdasarkan logika bisnis Anda
        // Contoh sederhana, jika ada $isEmailSeller, maka gunakan itu
        return $this->attributes['seller_email'] ?? $this->isEmailSeller;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
