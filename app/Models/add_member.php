<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class add_member extends Model
{
    use HasFactory;

    protected $table = 'web_member_data';
    protected $fillable = [
        'UserName',
        'PassWord',
        'E_Mail',
    ];
}
