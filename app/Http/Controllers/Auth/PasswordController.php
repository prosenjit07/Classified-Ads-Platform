<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
 use Illuminate\Http\RedirectResponse;
 use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
 use App\Http\Requests\Auth\UpdatePasswordRequest;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back();
    }
}
