<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillingModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_billing';
    protected $fillable = [
        'id', 'invoice_id', 'user_id'
    ];

    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
}
