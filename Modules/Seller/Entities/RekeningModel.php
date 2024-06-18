<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekeningModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_rekening';
    protected $fillable = ['id', 'rid', 'account_number', 'is_active',
        'created_at', 'updated_at', 'user_id', 'name'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $keyType = 'string';
    public $incrementing = false;
}
