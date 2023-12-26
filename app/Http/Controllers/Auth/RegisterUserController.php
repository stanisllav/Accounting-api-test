<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/register',
    summary: 'Register new User',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(
        content: new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                ]
            )
        )
    ),
    tags: ['Authentication'])]
#[OA\Response(
    response: 201,
    description: 'Returns User Model And Access Token',
    content: new OA\JsonContent(
        schema: 'array',
        title: 'data',
        properties: [
            new OA\Property(property: 'user', ref: '#/components/schemas/UserResource', type: 'object'),
            new OA\Property(property: 'token', type: 'string', example: 'jK4vOOBBGy1tM0NOQWTY745xdPddS4IyvHRVNbDU34093fa7'),
        ]
    )
)]
#[OA\Response(response: 422, description: 'Unprocessable content', content: new OA\JsonContent())]
class RegisterUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => UserResource::make($user), 'token' => $user->createToken('auth:register')->plainTextToken], 201);
    }
}
