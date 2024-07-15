<?php

namespace App\Http\Controllers\Api;

use App\Http\Middleware\Cors;
use App\Models\Comments;
use App\Models\Posts;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function __construct() {
        $this->middleware('auth:sanctum');
        $this->middleware(Cors::class);
    }



    public function index(){
        $posts = Posts::getUserPostsByApi();

        return response()->json($posts);
    }




    public function store(Request $request)  {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
           'files.*' => 'nullable|file|max:4096', 
        ]);
        
        if ($validator->fails()) {
            return response()->json(['erros' => $validator->getMessageBag()]);
        }


        $user = auth('api')->user();
     
        $post =  Posts::create([
            'title' => $request->title,
            // 'file_name' => $fileOriginalName ,
            // 'file_extension' => $fileExtension,
            'user_id' => $user->id,
            'content' => $request->content,
            // 'image' => $fileName,
            'slug' => Str::slug($request->title),
        ]);
    
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $path = 'post_media';
                $fileOriginalName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $fileOriginalName;
    
                $imageExtensions = ['jpg', 'jpeg', 'png'];
                $videoExtensions = ['mp4', 'mkv', 'web'];
                $documentExtensions = ['pdf', 'doc', 'docx', 'txt'];
                
                if (in_array($fileExtension, $imageExtensions)) {
                    $fileType = 'image';
                } elseif (in_array($fileExtension, $videoExtensions)) {
                    $fileType = 'video';
                } elseif (in_array($fileExtension, $documentExtensions)) {
                    $fileType = 'document';
                } else {
                    $fileType = 'other'; // Default type for unsupported file types
                }
    
                $file->move($path, $fileName);
    
                PostMedia::create([
                    'post_id' => $post->id,
                    'file' => $fileName,
                    'file_type' => $fileType,
                    'file_name' => $fileOriginalName,
                    'file_extension' => $fileExtension
                ]);
            }
        }
        $status = [
            'status' => 200,
            'message' => 'Post Created Succesfully'
        ];
        return response()->json($status);
    }

    public function show (Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $post = Posts::with('comments')->find($request->post_id);
        if ($post) {
            return response()->json(['post' => $post], 200);
        }
        return response()->json(['message' => 'Post not found', 'status' => 404]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'title' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $fileName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = 'post_media';
            $fileName = $image->getClientOriginalName();
            $image->move($path, $fileName);
        }

        $post = Posts::find($request->post_id);
        if ($post) {
            $post->title = $request->title;
            $post->slug = Str::slug($request->title);
            $post->content = $request->content ?? $post->content;
            $post->image = $fileName;
            $post->save();
            return response()->json(['message' => 'Post updated successfully', 'status' => 200]);
        }
        return response()->json(['message' => 'Post not found', 'status' => 404]);
    }

    public function destroy(Request $request){
        // $postId = $request->header('post-id');
        // if (!$postId) {
        //     return response()->json(['message' => 'Post id is required', 'status' => 401]);
        // }

        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $postId = $request->post_id;

        $post = Posts::find($postId);
        if ($post) {
            if ($post->media) {
                foreach ($post->media as $key => $file) {
                    $file_path = public_path('post_media/'.$file->file);
                    if(File::exists($file_path)){
                        File::delete($file_path); 
                    }      
              }
            }
           
            $post->delete();
            return response()->json(['message' => 'Post deleted successfully', 'status' => 200]);
        }

        return response()->json(['message' => 'Post not found', 'status' => 404]);
    }

    public function createComment (Request $request){
        $validator = Validator::make($request->all(),[
            'post_id' => 'required',
            'user_id' => 'required',
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $comment = new Comments();
        $comment->post_id = $request->post_id;
        $comment->user_id = $request->user_id;
        $comment->content = $request->comment;
        $comment->save();


        return response()->json([
            'message' => 'Comment Created',
            'status' => 200,
            'comment' => $comment->content,
        ]);
    }

    public function filterPost(Request $request){
        $getFields = $request->fields;
        $fields = explode(',', $getFields);
        if ($getFields) {
        $fields = array_map(function ($field) {
            return 'posts.' . trim($field);
        }, $fields);
        if (!in_array('posts.user_id', $fields)) {
            $fields[] = 'posts.user_id';
        }
    }else{
        $fields = ['posts.*'];
    }


        $perPage = $request->per_page;
        $searchQuery = $request->search;
        $posts = Posts::where('title', 'like', '%' . $searchQuery . '%')->with('user:id,name') 
        ->select($fields)
        ->paginate($perPage ?? 10);
        return response()->json(['posts' => $posts, 'status' => 200]);
    }
}
