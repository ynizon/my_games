<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * Authentification de l'application mobile par jeton (Sanctum).
 * Le jeton est envoye ensuite dans l'en-tete "Authorization: Bearer <token>".
 */
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:gam_users,email',
            'password' => 'required|min:5|max:255',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
        ]);
        $user->attachRole(Role::where('name', 'User')->firstOrFail());

        return $this->tokenResponse($user, $attributes['device_name'], 201);
    }

    public function login(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $attributes['email'])->first();
        if (!$user || !Hash::check($attributes['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.',
            ]);
        }

        if ($user->status != 1) {
            throw ValidationException::withMessages([
                'email' => "Votre compte a été suspendu.",
            ]);
        }

        //On memorise sa date de connexion
        $user->logged_date = date("Y-m-d");
        $user->save();

        return $this->tokenResponse($user, $attributes['device_name']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Suppression definitive de son compte (exigence Google Play), confirmee par le mot de passe.
     */
    public function destroy(Request $request, UserRepository $userRepository)
    {
        $request->validate(['password' => 'required']);

        $user = $request->user();
        if (!Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Mot de passe incorrect.',
            ]);
        }

        $userRepository->destroy($user->id);

        return response()->noContent();
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        // Meme reponse que l'adresse existe ou non
        return response()->json(['message' => __(Password::RESET_LINK_SENT)], $status === Password::RESET_THROTTLED ? 429 : 200);
    }

    private function tokenResponse(User $user, string $deviceName, int $status = 200)
    {
        return response()->json([
            'token' => $user->createToken($deviceName)->plainTextToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], $status);
    }
}
