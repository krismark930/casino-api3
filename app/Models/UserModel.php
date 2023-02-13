<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    public $timestamps = false;
    protected $table='web_member_data';
    use HasFactory;
    public $fillable=[
        'UserName',
        'PassWord',
        'E_Mail',
    ];
}
