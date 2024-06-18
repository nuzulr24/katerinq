<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class LogActivites extends Model
{
    use HasFactory;
    protected $table = 'tbl_log_activity';
    protected $fillable = ['uid','logType','causedBy','performedOn','withContent','created_at'];
    public $timestamps = false;

    public static function default($args = [])
    {
        self::create([
            'uid' => Uuid::uuid4()->toString(),
            'logType' => $args['logType'],
            'causedBy' => $args['causedBy'],
            'performedOn' => Carbon::now(),
            'withContent' => json_encode($args['withContent'], true),
        ]);
    }

    public function user()
    {
        return $this->hasMany(User::class, 'id', 'causedBy');
    }
}
