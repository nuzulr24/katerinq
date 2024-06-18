<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ProjectStatusEnum;
use App\Models\Services;

class Project extends Model
{
    use HasFactory;

    protected $table = 'tbl_projects';
    protected $fillable = ['id_service', 'slug', 'title', 'description', 'link', 'is_status', 'is_created', 'is_thumbnail'];
    public $timestamps = false;
    protected $cast = [
        'is_status' => ProjectStatusEnum::class
    ];

    public function services()
    {
        return $this->hasMany(Services::class);
    }
}
