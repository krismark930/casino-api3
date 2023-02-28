<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class KYLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];

    protected $table = 'ky_logs';
    public $timestamps = false;
}
