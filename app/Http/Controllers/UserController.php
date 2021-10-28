<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return array
     */
    public function login(LoginRequest $request): array
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (empty($user)) {
            return [
                'success' => false,
                'message' => 'Invalid E-mail'
            ];
        }

        if (Hash::check($data['password'], $user->password)) {
            $token = $user->createToken(Str::random(20))->plainTextToken;
            return [
                'success' => true,
                'data' => [
                    'token' => $token
                ]
            ];
        }


        return [
            'success' => false,
            'message' => 'Invalid Password'
        ];
    }

    /**
     * @return array
     */
    public function logout(): array
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return [
            'success' => true,
            'message' => 'User has been logged out'
        ];
    }

    /**
     * @param UserUpdateRequest $request
     * @return array
     */
    public function update(UserUpdateRequest $request): array
    {
        $data = $request->validated();
        $user = auth()->user();

        if (!empty($data['image'])) {
            if (!empty($user->image->url)) {
                Storage::delete($user->image->url);
                $user->image->delete();
            }

            $image = $data['image']->store('public/images');
            $user->image()->create(['url' => $image]);
            unset($data['image']);
        }

        $user->update($data);

        return [
            'success' => true,
            'message' => 'User data has been updated'
        ];
    }
}
