<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class SiteController extends Controller
{
    public function index(){

        return view('frontend.index');
    }

    public function contact()
    {
        return view('frontend.contact');
    }
}
