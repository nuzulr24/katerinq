<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponse extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket_reply';
    protected $fillable = [
        'id', 'id_ticket', 'user_id',
        'message', 'created_at', 'updated_at',
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public $keyType = 'string';
    public $incrementing = false;
}
