<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model {

    protected $fillable = [
        'user_id',
        'bank',
        'bank_account',
        'bank_address',
        'bank_type'
        ];

    protected $table = 'user_account';
}
