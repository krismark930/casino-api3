<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dz2 extends Model
{
    use HasFactory;
    protected $table = "dz2";

    protected $fillable = [
        "GameName",
        "GameName_EN",
        "PlatformType",
        "GameClass",
        "GameType_H5",
        "GameType",
        "ZH_Logo_File",
        "H5_Logo_File",
        "Date",
    ];

    public $timestamps = false;
}
