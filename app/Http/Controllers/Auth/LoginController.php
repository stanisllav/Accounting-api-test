<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LoginController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Handle an incoming authentication request',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(
                    required: ['email', 'password'],
                    properties: [
                        new OA\Property(property: 'email', type: 'string', format: 'email'),
                        new OA\Property(property: 'password', type: 'string', format: 'password'),
                    ]
                )
            )
        ),
        tags: ['Authentication'])]
    #[OA\Response(
        response: 200,
        description: 'Returns User Model And Access Token',
        content: new OA\JsonContent(
            schema: 'array',
            title: 'data',
            properties: [
                new OA\Property(property: 'user', ref: '#/components/schemas/User', type: 'object'),
                new OA\Property(property: 'token', type: 'string', example: 'jK4vOOBBGy1tM0NOQWTY745xdPddS4IyvHRVNbDU34093fa7'),
            ]
        )
    )]
    #[OA\Response(response: 422, description: 'Unprocessable content', content: new OA\JsonContent())]
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->authenticate();

        $user = auth()->user();

        return response()->json(['user' => $user, 'token' => $user->createToken('auth:register')->plainTextToken]);
    }

    #[OA\Post(path: '/api/logout', security: [['sanctum' => []]], tags: ['Authentication'])]
    #[OA\Response(response: 204, description: 'Destroy session', content: new OA\JsonContent())]
    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {

        auth()->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
