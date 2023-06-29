<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sys800 extends Model {
    use  HasFactory;

    protected $fillable = [
        'Payway',
        'previousAmount',
        'currentAmount',
        'Gold',
        'AddDate',
        'Type',
        'Type2',
        'UserName',
        'Agents',
        'World',
        'Corprator',
        'Super',
        'Admin',
        'CurType',
        'Date',
        'Phone',
        'Contact',
        'User',
        'Name',
        'Bank',
        'Bank_Address',
        'Bank_Account',
        'Order_Code',
        'Checked',
        'Music',
        'Notes',
        'created_at'
    ];

    protected $table = 'web_sys800_data';

    public $timestamps = false;
}
