<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['logout']);
    }

    public function register(AuthRegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "email already registered"
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        if ($user->email == "admin@localhost") {
            $user->token = $user->createToken('auth_token', ['admin'])->plainTextToken;
        } else {
            $user->token = $user->createToken('auth_token', [''])->plainTextToken;
        }

        return (new UserResource($user))->response()->setStatusCode(201);

    }

    public function login(AuthLoginRequest $request)
    {
        $data = $request->validated();

        if (!Auth::attempt($data)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "email or password is wrong"
                    ]
                ]
            ], 400));
        }

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($user->email == "admin@localhost") {
            $user->token = $user->createToken('auth_token', ['admin'])->plainTextToken;
        } else {
            $user->token = $user->createToken('auth_token', [''])->plainTextToken;
        }

        return new UserResource($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

}
