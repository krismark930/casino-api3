<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebMemberLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        "UserName",
        "Status",
        "LoginIP",
        "DateTime",
        "Contect",
        "Url",
    ];

    protected $table = 'web_member_logs';

    public $timestamps = false;
}
