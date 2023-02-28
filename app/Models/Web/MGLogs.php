<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class MGLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];

    protected $table = 'mg_logs';
    public $timestamps = false;
}
