<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;

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
        $this->userRepository->update($user->id, $request->validated());

        return new UserResource($user->refresh());
    }
}
