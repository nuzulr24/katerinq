<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $table = 'tbl_contents';
    protected $fillable = ['slug', 'title', 'description', 'is_tags', 'is_category', 'is_status', 'is_created', 'is_thumbnail'];
    public $timestamps = false;
    
    public function category()
    {
        return $this->hasMany(ContentCategories::class, 'id', 'is_category');
    }
}