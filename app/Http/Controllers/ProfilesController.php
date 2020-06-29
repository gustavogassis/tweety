<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfilesController extends Controller
{
    public function show(User $user) {
        
        return view('profiles.show', [
            'user' => $user,
            'tweets' => $user->tweets()->withLikes()->paginate(20)
        ]);
    }

    public function edit(User $user) {

        // abort_if ($user->isNot(current_user()), 404);
        //    pode ser substituido por  
        $this->authorize('edit', $user);

        return view('profiles.edit', compact('user'));
    }

    public function update(User $user) {

        $atrributes = request()->validate([
            'username' => [
                'string',
                'required',
                'max:255',
                'alpha_dash',
                Rule::unique('users')->ignore($user),
            ],
            'name' => ['string', 'required', 'max:255'],
            'avatar' => ['file'],
            'email' => [
                'string',
                'required',
                'max:255',
                'email',
                Rule::unique('users')->ignore($user),
            ],
            'password' => [
                'string',
                'required',
                'max:255',
                'min:8',
                'confirmed'
            ]
        ]);

        if (request('avatar')) {

            $atrributes['avatar'] = request('avatar')->store('avatars');
        }


        $user->update($atrributes);

        return redirect($user->path());
    }
}
