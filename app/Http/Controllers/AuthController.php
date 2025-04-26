<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     *
     * User Register
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json(
            [
                'message' => 'User registered successfully.',
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ]
        );
    }

    /**
     * Handle user login and return a token.
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated('email'),
                $request->validated('password')
            );
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
            ], 404);
        }

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    /**
     * Logout user
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(
            [
                'message' => 'logged out',
            ]
        );
    }
}
