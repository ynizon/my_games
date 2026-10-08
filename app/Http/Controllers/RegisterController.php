<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register.create');
    }

    public function store(){

        $attributes = request()->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:gam_users,email',
            'password' => 'required|min:5|max:255',
        ]);

        $attributes['password'] = Hash::make($attributes['password']);
        $user = User::create($attributes);
        $user->attachRole(3);//Role USER

        auth()->login($user);

        return redirect('/dashboard');
    }
}
