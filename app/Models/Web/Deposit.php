<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model {
    use  HasFactory;

    //protected $fillable = ['username', 'password'];

    protected $table = 'web_sys800_data';

    //public $timestamps = false;
}
