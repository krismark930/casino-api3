<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model {

    protected $fillable = ['MD5String', 'DateTime'];

    protected $table = 'web_update_log';

    public $timestamps = false;
}
