<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function refund()
    {
        return view('user.policy.refund_policy');
    }

    public function security()
    {
        return view('user.policy.security_and_privacy');
    }

    public function freight()
    {
        return view('user.policy.freight_and_deliveries');
    }
}
