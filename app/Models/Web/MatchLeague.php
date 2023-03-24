<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class MatchLeague extends Model {

    protected $fillable = ['R', 'RB', 'M', 'EO',
    'OU', 'ROU', 'VM', 'PD',
    'VR', 'VRB', 'RM', 'T',
    'VOU', 'VROU', 'VRM', 'F'];
    protected $table = 'match_league';
    public $timestamps = false;
}
