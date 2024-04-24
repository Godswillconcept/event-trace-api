<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;




class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        // dd($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'token' => $token,
            'user' => $user,
        ], 201); // 201 Created status code
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password', 'role');

        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Invalid login details',
            ], 401); // 401 Unauthorized status code
        }

        $user = Auth::user();

        if ($request->role === 'venue_owner' && $user->role != 'venue_owner') {
            return response([
                'message' => 'Invalid login details',
            ], 401); // 401 Unauthorized status code
        }

        if ($request->role === 'event_organizer' && $user->role != 'event_organizer') {
            return response([
                'message' => 'Invalid login details',
            ], 401); // 401 Unauthorized status code
        }

        if ($request->role === 'admin' && $user->role != 'admin') {
            return response([
                'message' => 'Invalid login details',
            ], 401); // 401 Unauthorized status code
        }

        if ($request->role === 'attendee' && $user->role != 'attendee') {
            return response([
                'message' => 'Invalid login details',
            ], 401); // 401 Unauthorized status code
        }

        return response([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 200); // 200 OK status code
    }


    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->where('name', 'auth_token')->delete();
        Auth::logout();

        return response([
            'message' => 'Logged out successfully',
        ], 200); // 200 OK status code
    }

    public function user()
    {
        $user = Auth::user();

        return response([
            'user' => $user,
        ], 200); // 200 OK status code
    }
    public function index()
    {
        $users = User::latest()->get();

        return response([
            'users' => $users,
        ], 200); // 200 OK status code
    }

    public function update(Request $request)
    {
        // $request->validate([
        //     'gender' => 'in:male,female,unspecified',
        //     'role' => 'in:venue_owner,admin,event_organizer,attendee',
        //     'phone' => 'numeric',
        //     'dob' => 'required',
        //     'username' => 'required',
        // ]);
            
        // dd($request->all());
        $user = Auth::user();
        $user->update($request->only('gender', 'role',  'phone', 'dob', 'username'));

        return response([
            'message' => 'User updated successfully',
        ], 200); // 200 OK status code
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();


        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imagePath = $this->saveImage($image, 'profiles');

            $user->photo = $imagePath;
            $user->save();
        }

        return response([
            'message' => 'Image uploaded successfully',
        ], 200);
    }

    public function destroy()
    {
        $user = Auth::user();

        $user->delete();

        return response([
            'message' => 'User permanently deleted successfully',
        ]);
    }
}
