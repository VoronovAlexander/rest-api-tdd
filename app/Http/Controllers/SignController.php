<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Auth\SigninRequest;
use App\Http\Requests\Api\Auth\SignupRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignController extends Controller
{

    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return response(null, 201);
    }

    public function signin(SigninRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])
            ->first();

        if(!Hash::check($data['password'], $user->password)) {
            return response(null, 403);
        }

        $token = $user->createToken('asd');

        return response($token, 200);
    }
}
