<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;

    protected $table = 'tbl_services';
    protected $fillable = ['slug', 'title', 'description', 'is_attachment', 'is_status', 'is_created', 'is_thumbnail'];
    public $timestamps = false;
}
