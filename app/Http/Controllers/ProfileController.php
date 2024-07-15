<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function updateProfile(Request $request, $id)  {
        $user = User::find($id);
        if ($request->delete_account) {
            Auth::logout();
            $user->delete();
        }
        $user->name = $request->user_name;
        $user->email = $request->user_email;
        $user->save();

        return redirect()->back()->with('status', 'Profile updated');
    }
}
