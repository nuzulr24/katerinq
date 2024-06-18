<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;
    protected $table = 'tbl_sellers';
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'uuid', 'user_id', 'name', 'address', 'phone', 'description',
        'alias', 'is_active', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    // Define the primary key type
    protected $keyType = 'string';
    public $incrementing = false;

    public function user()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }
}
