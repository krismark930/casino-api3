<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSystemData extends Model
{
    use HasFactory;

    protected $fillable = [        
        "Level",
        "UserName",
        "LoginName",
        "passWord",
        "Passwd",
        "Alias",
        "Status",
        "Competence",
        "SubUser",
        "SubName",
        "AddDate",
    ];

    protected $table = 'web_system_data';

    public $timestamps = false;
}
