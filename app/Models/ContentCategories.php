<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCategories extends Model
{
    use HasFactory;
    protected $table = 'tbl_contents_category';
    protected $fillable = ['name', 'slug'];
    public $timestamps = false;
}
