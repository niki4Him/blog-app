<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @param UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password)
        ]);

        $token = $user->createToken(uniqid(base64_encode(Str::random(60))))->plainTextToken;
        $data = [
            'user' => $user,
            'token' => $token,
        ];
        return $this->success($data, 201);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        $data = [
            'message' => 'Successfully logout!'
        ];
        return $this->success($data, 201);
    }

    /**
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        $user = User::whereEmail($request->email)->first();
        if (!$user or !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Bad creds'
            ], 422);
        }
        $token = $user->createToken(uniqid(base64_encode(Str::random(60))))->plainTextToken;
        $data = [
            'user' => $user,
            'token' => $token,
        ];
        return $this->success($data, 201);
    }
}
