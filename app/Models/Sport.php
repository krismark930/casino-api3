<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sport extends Model
{
    use HasFactory;

    protected $table = 'match_sports';

    protected $primaryKey = 'MID';


}
