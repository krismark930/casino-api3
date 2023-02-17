<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Web\Bank;

class DepositController extends Controller {
    /* Get user info. */
    public function getBank(Request $request) {
        $user = Bank::all();
        return response()->json(['success'=>true, 'backList' => $user, 'user' => $request->user()]);
    }
    public function addMoney(Request $request) {

    }
}
