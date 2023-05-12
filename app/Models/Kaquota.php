<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kaquota extends Model
{
    use HasFactory;

    protected $table = "ka_quota";
    public $timestamps = false;
}
