<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class AGLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];

    protected $table = 'ag_logs';

}
