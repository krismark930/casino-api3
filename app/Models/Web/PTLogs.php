<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class PTLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];

    protected $table = 'pt_logs';
    public $timestamps = false;
}
