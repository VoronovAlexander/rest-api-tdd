<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignControllerTest extends TestCase
{

    /**
     * /api/auth/signup
     * Проверка регистрации
     */
    public function testSignup()
    {
        $uniqueUser = factory(User::class)->create();
        $errorCases = [
            [
                // email required
                'password' => $this->faker->password(6, 20),
            ],
            [
                // email is has type "email"
                'email' => $this->faker->userName(),
                'password' => $this->faker->password(6, 20),
            ],
            [
                // email unique
                'email' => $uniqueUser->email,
                'password' => $this->faker->password(6, 20),
            ],
            [
                // password required
                'email' => $this->faker->unique()->safeEmail,
            ],
            [
                // password string
                'email' => $this->faker->unique()->safeEmail,
                'password' => (int) $this->faker->numberBetween(100000, 9999999),
            ],
            [
                // password min:6
                'email' => $this->faker->unique()->safeEmail,
                'password' => $this->faker->password(1, 5),
            ],
            [
                // password max:20
                'email' => $this->faker->unique()->safeEmail,
                'password' => $this->faker->password(21, 255),
            ],
        ];

        foreach ($errorCases as $errorCase) {
            $response = $this->json('POST', '/api/auth/signup', $errorCase);
            $this->responseValidationFailedTest($response);
        }

        $user = factory(User::class)->make()
            ->toArray();
        $user['password'] = 'password';

        $response = $this->json('POST', '/api/auth/signup', $user);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', ['email' => $user['email']]);
    }

    /**
     * /api/auth/signin
     * Проверка успешного входа
     */
    public function testSignin()
    {
        $uniqueUser = factory(User::class)->create();
        $errorCases = [
            [
                // email required
                'password' => 'password',
            ],
            [
                // email is has type "email"
                'email' => $this->faker->userName(),
                'password' => 'password',
            ],
            [
                // email exists
                'email' => $this->faker->unique()->safeEmail,
                'password' => 'password',
            ],
            [
                // password required
                'email' => $uniqueUser->email,
            ],
            [
                // password string
                'email' => $uniqueUser->email,
                'password' => (int) $this->faker->numberBetween(100000, 9999999),
            ],
            [
                // password min:6
                'email' => $uniqueUser->email,
                'password' => $this->faker->password(1, 5),
            ],
            [
                // password max:20
                'email' => $uniqueUser->email,
                'password' => $this->faker->password(21, 255),
            ],
        ];

        foreach ($errorCases as $errorCase) {
            $response = $this->json('POST', '/api/auth/signin', $errorCase);
            $this->responseValidationFailedTest($response);
        }

        $user = factory(User::class)->create()
            ->toArray();
        $user['password'] = 'password';

        $response = $this->json('POST', '/api/auth/signin', $user);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'refresh_token',
            ]);

        $data = $response->decodeResponseJson();

        $this->assertNotNull($data['access_token']);
        $this->assertIsString($data['access_token']);
        $this->assertNotNull($data['refresh_token']);
        $this->assertIsString($data['refresh_token']);
    }

    /**
     * /api/auth/signin
     * Проверка ошибки входа из-за неверного пароля
     */
    public function testIncorrectSignin()
    {
        $user = factory(User::class)->create()
            ->toArray();
        $user['password'] = 'incorrect';

        $response = $this->json('POST', '/api/auth/signin', $user);
        $response->assertStatus(403);
    }

    /**
     * /api/auth/refresh
     * Проверка обновления токена
     */
    public function testRefreshToken()
    {
        $user = factory(User::class)->create();

        $userArray = $user->toArray();
        $userArray['password'] = 'password';

        $response = $this->json('POST', '/api/auth/signin', $userArray);

        $refreshToken = $response->decodeResponseJson('refresh_token');

        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/auth/refresh', [
                'refresh_token' => $refreshToken,
            ]);

        $data = $response->decodeResponseJson();

        $this->assertNotNull($data['access_token']);
        $this->assertIsString($data['access_token']);
        $this->assertNotNull($data['refresh_token']);
        $this->assertIsString($data['refresh_token']);
    }
}
