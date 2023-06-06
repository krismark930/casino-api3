<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebMarqueeData extends Model
{
    use HasFactory;

    protected $fillable = [
        "Level",
        "Message",
        "Message_tw",
        "Message_en",
        "Time",
        "Date",
        "Admin",
    ];

    protected $table = "web_marquee_data";

    public $timestamps = false;
}
