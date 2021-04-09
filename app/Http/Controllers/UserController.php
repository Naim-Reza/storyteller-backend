<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * index
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\User
     */
    public function index(Request $request)
    {
        return $request->user();
    }
}
