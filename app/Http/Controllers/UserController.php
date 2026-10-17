<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $this->checkPermission();

        $users = $this->userRepository->get();

        return view('user/index', compact('users'));
    }

    public function create()
    {
        $this->checkPermission();

        $users_roles = config('app.users_roles');

        return view('user/create', compact('users_roles'));
    }

    public function show($id)
    {
        return redirect('/users/'.$id."/edit");
    }

    public function edit($id)
    {
        $this->checkPermission();

        $users = $this->userRepository->get();
        $user = $this->userRepository->getById($id);
        $role = optional($user->roles->first())->name ?? "";
        $roles = config('app.users_roles');

        return view('user/edit', compact('users', 'user', 'role', 'roles'));
    }

    public function profile()
    {
        $user = Auth::user();

        return view('user/profile', compact('user'));
    }

    /**
     * Mise a jour de son propre profil : seuls les champs du profil sont pris en compte
     * (jamais le role ni le statut).
     */
    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();
        $this->userRepository->update($user->id, $request->validated());

        return redirect()->route('profile')->withOk("Le profil de " . $user->name . " a été mis à jour.");
    }

    /**
     * Suppression definitive de son propre compte, confirmee par le mot de passe.
     */
    public function destroyProfile(Request $request)
    {
        $user = Auth::user();
        if (!Hash::check((string) $request->input('delete_password'), $user->password)) {
            return redirect()->route('profile')->withErrors(['delete_password' => 'Mot de passe incorrect.']);
        }

        Auth::logout();
        $this->userRepository->destroy($user->id);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/suppression-compte')->with('deleted', true);
    }

    public function update(UserRequest $request, $id)
    {
        $this->userRepository->update($id, $request->validated());

        return redirect('/users')->withOk("L'utilisateur " . $request->input('name') . " a été modifié.");
    }

    public function destroy($id)
    {
        $this->checkPermission();

        $this->userRepository->destroy($id);
        return redirect()->back();
    }

    public function store(UserRequest $request)
    {
        try {
            $this->userRepository->store($request->validated());

            return redirect('/users')->withOk("L'utilisateur " . $request->input('name') . " a été créé.");
        } catch (\Exception $e) {
            Log::error($e);
            return redirect('/users')->withError("L'utilisateur " . $request->input('name') . " n'a pas pu être créé.");
        }
    }

    private function checkPermission()
    {
        if (!Auth::user()->can("user-edit")) {
            abort(403);
        }
    }
}
