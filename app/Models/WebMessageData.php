<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebMessageData extends Model
{
    use HasFactory;

    protected $fillable = [
        "UserName",
        "Subject",
        "Message",
        "Time",
        "Date"
    ];

    protected $table = "web_message_data";

    public $timestamps = false;
}
