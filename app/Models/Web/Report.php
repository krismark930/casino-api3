<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    protected $table = 'web_report_data';
    public function sport() {
        return $this->belongsTo('App\Models\Sport', 'MID', 'MID');
    }
}
