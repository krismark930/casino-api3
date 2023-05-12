<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KaTan extends Model
{
    use HasFactory;
    
    protected $table = "ka_tan";

    protected $fillable = [        
        "num",
        "username",
        "kithe",
        "adddate",
        "class1",
        "class2",
        "class3",
        "rate",
        "sum_m",
        "user_ds",
        "dai_ds",
        "zong_ds",
        "guan_ds",
        "dai_zc",
        "zong_zc",
        "guan_zc",
        "dagu_zc",
        "bm",
        "dai",
        "zong",
        "guan",
        "danid",
        "zongid",
        "guanid",
        "abcd",
        "lx",
    ];
    public $timestamps = false;
}
