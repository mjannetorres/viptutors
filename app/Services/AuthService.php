<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register user
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_admin' => $data['is_admin'] ?? false,
        ]);

        return [
            'user' => $user,
            'token' => $this->generateToken($user),
        ];
    }

    /**
     * Attempt to log in a user by email and password.
     * @param string $email
     * @param string $password
     *
     * @return array
     *
     * @throws ValidationException If authentication fails.
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        return [
            'user' => $user,
            'token' => $this->generateToken($user),
        ];
    }

    /** Logout user
     * @return void
     */
    public function logout(): void
    {
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });
    }

    /**
     * Generate token for user
     * @param User $user
     * @return string
     */
    private function generateToken(User $user): string
    {
        return $user->createToken('my-app-token')->plainTextToken;
    }
}
