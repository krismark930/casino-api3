<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class UserBankAccount extends Model {

    protected $fillable = [
        'user_id',
        'bank_card_owner',
        'bank_card_type',
        'bank_account',
        'bank_address',
        'bank_type',
        'status'
        ];
    protected $table = 'user_bank_account';
}
