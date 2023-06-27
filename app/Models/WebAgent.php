<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        "FT_Turn_RE_A",
    ];

    protected $table = 'web_agents_data';
}
