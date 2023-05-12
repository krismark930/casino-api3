<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SysConfig extends Model
{
    protected $table = 'sys_config';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
