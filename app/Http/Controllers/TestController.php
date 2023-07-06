<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Test;

class TestController extends Controller
{
    public function addTest() {
        $test = new Test;
        $test->name = "11111";
        $test->save();
    }
}
