<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sys800 extends Model {
    use  HasFactory;

    protected $fillable = ['Payway', 'Gold', 'AddDate', 'Type', 'Type2', 'UserName', 'Agents', 'World', 'Corprator', 'Super'
    , 'Admin', 'CurType', 'Name', 'Bank', 'Bank_Address', 'Bank_Account', 'Order_Code'];

    protected $table = 'web_sys800_data';

    public $timestamps = false;
}
