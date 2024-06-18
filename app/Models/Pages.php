<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    use HasFactory;
    protected $table = 'tbl_pages';
    protected $fillable = [
        'id', 'slug', 'title', 'description',
        'is_thumbnail', 'is_attachment', 'is_status',
        'created_at', 'updated_at', 'markAsUnique'
    ];
}
