<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TicketResponse;
use App\Models\User;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tbl_ticket';
    protected $fillable = [
        'id', 'user_id', 'subject', 'is_status',
        'is_priority', 'created_at', 'updated_at',
    ];

    public $keyType = 'string';
    public $incrementing = false;

    public function response()
    {
        return $this->hasMany(TicketResponse::class, 'id_ticket', 'id');
    }
    
    public function getCreatedAtAttribute($value)
    {
        return date_formatting(Carbon::parse($value)->format('Y-m-d'), 'indonesia');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date_formatting($value, 'timeago');
    }
}
