<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class GUser extends Model {

    protected $fillable = ['g_money', 'g_money_yes'];
    protected $table = 'g_user';
    public $timestamps = false;
}
