<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Jobs\ProcessUploadUsers;
use Illuminate\Support\Facades\Bus;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessDeleteFile;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function users(){
        $user = User::all();
        return response()->json($user, 200);
    }

    public function export() 
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function import(Request $request) 
    {
        // Validate incoming request data
        $request->validate([
            'file' => 'required|max:2048',
        ]);
        $file = $request->file('file')->store('temp'); 
        $path = storage_path('app'). '/' .$file;  

        // dispatch(new ProcessUploadUsers($path));
        Bus::chain([
            new ProcessUploadUsers($path),
            new ProcessDeleteFile($file)// new class added
        ])->dispatch();
        return response()->json(['success' => 'Users are being importing.'], 200);
    }
}
