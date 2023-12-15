<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function me(Request $request): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();

        $user = Auth::user();

        if ($request->has('name')) {
            $user->name = $data['name'];
        }

        if ($request->has('email') && $user->email !== $data['email']) {
            if (User::where('email', $data['email'])->exists()) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "email" => [
                            "email already registered"
                        ]
                    ]
                ], 400));
            }
            $user->email = $data['email'];
        }

        if ($request->has('old_password')) {
            if (!Hash::check($data['old_password'], $user->password)) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "password" => [
                            "password is wrong"
                        ]
                    ]
                ], 400));
            }

            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        return new UserResource($user);
    }
}
