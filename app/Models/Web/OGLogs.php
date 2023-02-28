<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class OGLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];
    protected $table = 'og_logs';
}
