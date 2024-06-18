<?php

namespace Modules\Seller\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekeningBankModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_rekeningbank';
    protected $fillable = ['id', 'nama', 'kodebank'];
    protected $hidden = ['id', 'kodebank'];

    public $timestamps = false;
}
