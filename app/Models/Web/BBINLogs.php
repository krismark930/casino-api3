<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class BBINLogs extends Model {

    protected $fillable = [
        'Username',
        'Type',
        'Gold',
        'Billno',
        'DateTime',
        'Result',
        'Checked'];

    protected $table = 'bbin_logs';

}
