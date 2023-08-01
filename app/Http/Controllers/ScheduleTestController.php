<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Test;

class ScheduleTestController extends Controller
{
    public function scheduleTest() {
        $test = new Test;
        $test->name = "test";
        $test->save();
    }
}
