<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    //Register user
    public function register(Request $request)
    {
        //validate fields
        $attributes = $request->validate([
            'username' =>    ['required', 'string', 'max:255', Rule::unique('users')],
            'fullname' =>       ['required', 'string', 'max:255'],
            'password' =>   ['required', 'confirmed', Rules\Password::defaults()],
            'email' =>      ['required', 'string', 'email', 'max:255', 'unique:users'],
            'campus_id' =>   ['required', Rule::exists('campuses', 'id')],
            'role_id' =>    ['required', Rule::exists('roles', 'id')],
            'tel1' =>        ['required', 'min:10', 'numeric'],
            'tel2' =>        ['min:10', 'numeric', 'nullable'],
        ]);

        //create user
        $user = User::create($attributes);

        // $this->sendNotification($user, 'Hi '.$user->fullname.', welcome to notAim. You can now create events and share with your friends.');

        //return user & token in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ], 200);
    }

    // login user
    public function login(Request $request)
    {
        //validate fields
        $attrs = $request->validate([
            'email_username' => 'required|string|max:255',
            'password' => 'required|min:6'
        ]);

        //check if email or username
        $login_type = filter_var($request->input('email_username'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';



        // attempt login
        if (!Auth::attempt([$login_type => $attrs['email_username'], 'password' => $attrs['password']])) {
            return response([
                'message' => 'Invalid credentials.'
            ], 403);
        }




        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    // logout user
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logout success.'
        ], 200);
    }

    // get user details
    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }

    // update user
    public function update(Request $request)
    {
        //validate fields
        $attributes = $request->validate([
            'username' =>    ['string', 'max:255', Rule::unique('users')],
            'fullname' =>   ['string', 'max:255'],
            'password' =>   ['confirmed', Rules\Password::defaults()],
            'email' =>      ['string', 'email', 'max:255', 'unique:users'],
            'campus_id' =>   [Rule::exists('campuses', 'id')],
            'role_id' =>    [Rule::exists('roles', 'id')],
            'tel1' =>        ['min:10', 'numeric'],
            'tel2' =>        ['min:10', 'numeric', 'nullable'],
            'avatar' =>      ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $avatar = $this->saveImage($request->avatar, 'avatars');
        $avatar = 'avatar.jpg';
        $attributes['avatar'] = $avatar;
        auth()->user()->update($attributes);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ], 200);
    }

    // create role
    public function createRole(Request $request)
    {
        //validate fields
        $attributes = $request->validate([
            'role' =>    ['required', 'string', 'max:255', Rule::unique('roles')],
        ]);

        $role = Role::create($attributes);

        return response([
            'message' => 'Role created.',
            'role' => $role
        ], 200);
    }
}
