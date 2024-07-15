<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\Posts;
use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomePageController extends Controller
{
    public function index()
    {
        $posts = Posts::all();
        return view('welcome', ['posts' => $posts]);
    }
    public function viewPost($lug)
    {

        $post = Posts::where('slug', $lug)->first();
        return view('view_post', compact('post'));
    }

    public function makeComment(Request $request)
    {
        if (Auth::check()) {
            $comment = new Comments();
            $comment->content = $request->comment;
            $comment->post_id = $request->postId;
            $comment->user_id = Auth::user()->id;
            $comment->save();
            return response()->json(['message' =>  'Commented Successfully']);
        }
        return response()->json(['error' => 'Please login first to make comment']);
    }

    public function comments(Request $request)
    {
        $comments = Comments::where('post_id', $request->query('post_id'))->with(['user', 'post'])->orderBy('created_at', 'desc')->get();

        return response()->json(['comments' => $comments]);
    }

    public function downloadMedia($postId)
    {
        $post = Posts::find($postId);
        foreach ($post->media as $key => $value) {
            $imgarr[] = public_path( "post_media" . '/' . $value->file);
        }

        $ziplink = $this->converToZip($imgarr);
        if ($ziplink) {
            // $zipFileName = asset('post_zip').'/'.basename($ziplink);
            // var_dump($zipFileName);
            return response()->download($ziplink)->deleteFileAfterSend(false);
        } else {
            return response()->json(['message' => 'Failed to create zip file', 'status' => 500]);
        }
    }
    public function converToZip($imgarr)
    {
       
        $zip = new ZipArchive;
        $storage_path = 'post_zip';
        $zipDir = public_path($storage_path);
         if (!file_exists($zipDir)) {
            mkdir($zipDir, 0755, true);
        }
        $timeName = time();
        $zipFileName = $storage_path . '/' . $timeName . '.zip';
        $zipPath = public_path($zipFileName);
        // var_dump($zipPath);
        if ($zip->open(($zipFileName), ZipArchive::CREATE) === true) {
            foreach ($imgarr as $relativName) {
                $zip->addFile($relativName, "/" . $timeName . "/" . basename($relativName));
            }
            $zip->close();
            if ($zip->open($zipFileName) === true) {
                return $zipPath;
            } else {
                return false;
            }
        }
    }

    // public function downloadMedia(Request $request)
    // {
    //     $postId = $request->post_id;
    //     $post = Posts::find($postId);
    //     $files = $post->media;
    //     $donloadFiles = [];
    //     foreach ($files as $key => $file) {
    //         $donloadFiles[] = $file->file;
    //     }
    //     $zip = new \ZipArchive();
    //     $fileName = 'zipFile.zip';
    //     if ($zip->open(public_path($fileName), \ZipArchive::CREATE) == TRUE) {

    //         $genFiles = File::files(public_path('post_media/' . implode(',', $donloadFiles)));
    //         foreach ($genFiles as $key => $value) {
    //             $relativeName = basename($value);
    //             $zip->addFile($value, $relativeName);
    //         }
    //         $zip->close();
    //     }

    // return response()->download(public_path($fileName));
    // }
}
