<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    public function createTokenByPassword(string $password)
    {
        $oauth_client = \DB::table('oauth_clients')
            ->where('personal_access_client', 0)
            ->first();

        if (!$oauth_client) {
            throw new \Exception('oauth_client not found');
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $oauth_client->id,
            'client_secret' => $oauth_client->secret,
            'username' => $this->email,
            'password' => $password,
        ];

        $request = Request::create('/oauth/token', 'POST', $data);
        $response = app()->handle($request);
        $tokenResponse = $response->getContent();

        return json_decode($tokenResponse);
    }

    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

}
