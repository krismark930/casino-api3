<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Config extends Model
{
    protected $table = 'config';

    protected $primaryKey = 'id';

    public $timestamps = false;

}
