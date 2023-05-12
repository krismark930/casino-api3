<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kadrop extends Model
{
    use HasFactory;
    protected $table = "ka_drop";
    public $timestamps = false;
}
