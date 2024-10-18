<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^[6789]\d{9}$/', // Indian phone number validation
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create user without profile_image initially
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'description' => $request->description,
            'role_id' => $request->role_id,
        ]);

        // Handle file upload if exists
        if ($request->hasFile('profile_image')) {
            $fileName = time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $profileImagePath = $request->file('profile_image')->storeAs('public/profile_images', $fileName);
            // Update the user's profile_image field
            $user->profile_image = 'profile_images/' . $fileName;
            $user->save(); // Save the changes
        }

        return response()->json(['message' => 'User created successfully!', 'user' => $user], 201);
    }

    public function index()
    {
        return response()->json(User::with('role')->get());
    }
}
