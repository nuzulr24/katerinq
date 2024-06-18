<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_notifications';
    protected $primaryKey = 'uid';
    protected $fillable = [
        'uid', 'user_id', 'notifyAs',
        'onUpdateProduct', 'onUpdateNews',
        'onUpdateOrders', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $keyType = 'string';
    public $incrementing = false;
}
