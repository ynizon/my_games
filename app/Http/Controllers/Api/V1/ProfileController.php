<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function update(ProfileRequest $request)
    {
        $user = $request->user();
        $inputs = $request->validated();

        // Changer d'e-mail ou de mot de passe demande le mot de passe actuel :
        // un jeton vole ne doit pas suffire a prendre le controle du compte.
        $emailChanged = strcasecmp(trim($inputs['email']), (string) $user->email) !== 0;
        $passwordChanged = ($inputs['password'] ?? '') !== '';
        if (($emailChanged || $passwordChanged)
            && !Hash::check((string) $request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Mot de passe actuel incorrect.',
            ]);
        }

        $this->userRepository->update($user->id, $inputs);

        return new UserResource($user->refresh());
    }
}
