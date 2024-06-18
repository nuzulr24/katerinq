<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentTag extends Model
{
    use HasFactory;
    protected $table = 'tbl_contents_tags';
    protected $fillable = ['name', 'slug'];
    public $timestamps = false;
}
