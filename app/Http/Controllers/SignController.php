<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\Auth\SigninRequest;
use App\Http\Requests\Api\Auth\SignupRequest;
use App\Http\Resources\Tokens\TokenResource;
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

        if (!Hash::check($data['password'], $user->password)) {
            return response(null, 403);
        }

        $token = $user->createTokenByPassword($request->password);

        return response(TokenResource::make($token), 200);
    }

    public function refresh(Request $request)
    {
        $oauth_client = \DB::table('oauth_clients')
            ->where('personal_access_client', 0)
            ->first();

        if (!$oauth_client) {
            throw new \Exception('oauth_client not found');
        }

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret,
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $response = app()->handle($request);

        $token = json_decode($response->getContent());

        if (isset($token->error)) {
            return response(null, 403);
        }

        return response(TokenResource::make($token), 200);

    }
}
