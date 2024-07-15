<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $posts = $user->posts;
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'user_id' => 'required',
        ]);
       if ($validator->fails()) {
        $errors = $validator->getMessageBag()->toArray();
        // Pass the errors as session flash data
        return redirect()->back()->withErrors($errors)->withInput();
       }

       $post = Posts::create([
        'title' => $request->title,
        'slug' => Str::slug($request->title),
        'content' => $request->content ?? null,
        'user_id' => $request->user_id
       ]);

       return redirect()->back()->with('status', 'Post Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Posts::find($id);
        return response()->json(['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Posts $post, Request $request)
    {

        if ($post) {
            $post->title = $request->title;
            $post->content = $request->content;
            $post->save();
        }else {
            return redirect()->back()->with('error', 'Post not found');
        }

        return redirect()->back()->with('status', 'Post Updated Succefully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Posts $post)
    {
        if ($post) {
            $file_path = public_path('post_media/'.$post->image);
            if(File::exists($file_path)){
                File::delete($file_path);
            }
           $post->delete();
           return redirect()->back()->with('status', 'Post Deleted Successfully');
        }
        return redirect()->back()->with('error', 'Post not found');
    }

    public function adminPosts(){
        $posts = Posts::with('user')->get();
        return view('admin.posts.index', compact('posts'));
    }
}
