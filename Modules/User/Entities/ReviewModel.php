<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Modules\Seller\Entities\AccountModel as Account;
use Modules\Seller\Entities\SitesModel as Sites;

class ReviewModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_reviews';
    protected $fillable = [
        'id', 'buy_id', 'sell_id', 'website_id', 'review',
        'rating', 'created_at', 'updated_at'
    ];
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    public function user()
    {
        return $this->hasMany(Account::class, 'id', 'buy_id');
    }
    
    public function seller()
    {
        return $this->hasMany(Account::class, 'id', 'sell_id');
    }
    
    public function website()
    {
        return $this->hasMany(Sites::class, 'id', 'website_id');
    }
}
